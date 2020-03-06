<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.03.20 04:09:42
 */

declare(strict_types = 1);
namespace dicr\cdek;

use dicr\validate\ValidateException;
use yii\base\Exception;
use yii\httpclient\Client;
use function array_key_exists;
use function is_array;
use function is_numeric;
use function md5;

/**
 * Запрос рассчета доставки.
 *
 * При использовании тарифов для обычной доставки авторизация не обязательна и параметры authLogin и secure можно не
 * передавать.
 *
 * Дата планируемой отправки dateExecute не обязательна (в этом случае принимается текущая дата).
 * Но, если вы работаете с авторизацией, она должна быть передана, так как дата учитывается при шифровании/дешифровке
 * пароля.
 *
 * Если задан код города cityId и индекс cityPostCode, то приоритет отдается коду id.
 *
 * При задании тарифа нужно задавать либо один выбранный тариф, либо список тарифов с приоритетами.
 * Если задаётся и tariffId, и tariffList – принимается tariffId, а список игнорируется.
 *
 * Задавать места в списке можно первым вариантом (через вес, длину, ширину и высоту) и вторым (через вес и объём),
 * а также комбинируя эти варианты (одно место первым, другое вторым и т.д.). Стоимость доставки будет рассчитываться
 * исходя из наибольшего значения объёмного или физического веса. Многие расчеты зависят от габаритов, рекомендуется
 * не использовать параметр volume, а задавать места через длину, ширину и высоту.
 *
 * Для дополнительных услуг 2, 24, 25 и 32 значение параметра является обязательным и должно быть передано в запросе.
 * Для услуги 2 - страховка в param необходимо передать сумму, с которой будет рассчитана страховка (необходимо
 * передавать в валюте взаиморасчетов). Услуга 30 доступна только для договора ИМ, поэтому в запросе должны быть
 * переданы значения authLogin и secure. Для услуг 24,25 и 32 в param передается значение количества.
 *
 * @property-read array $params параметры запроса
 *
 * @link https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-%D0%9F%D1%80%D0%BE%D1%82%D0%BE%D0%BA%D0%BE%D0%BB%D0%BE%D0%B1%D0%BC%D0%B5%D0%BD%D0%B0%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8(v1.5)-4.14.2.%D0%A0%D0%B0%D1%81%D1%87%D0%B5%D1%82%D1%81%D1%82%D0%BE%D0%B8%D0%BC%D0%BE%D1%81%D1%82%D0%B8%D0%BF%D0%BE%D1%82%D0%B0%D1%80%D0%B8%D1%84%D0%B0%D0%BC%D0%B1%D0%B5%D0%B7%D0%BF%D1%80%D0%B8%D0%BE%D1%80%D0%B8%D1%82%D0%B5%D1%82%D0%B0
 */
class CalcRequest extends AbstractRequest
{
    /** @var string API version */
    public const API_VERSION = '1.0';

    /** @var string URL API калькулятора */
    public const REQUEST_URL = 'http://api.cdek.ru/calculator/calculate_price_by_json.php';

    /** @var string|null Планируемая дата отправки заказа в формате “ГГГГ-ММ-ДД” */
    public $dateExecute;

    /** @var string|null Локализация названий городов. По умолчанию "rus" */
    public $lang;

    /** @var string|null Код страны отправителя в формате ISO_3166-1_alpha-2 */
    public $senderCountryCode;

    /** @var int|null Код города отправителя из базы СДЭК */
    public $senderCityId;

    /** @var int|null Индекс города отправителя из базы СДЭК (если не задан senderCityId) */
    public $senderCityPostCode;

    /** @var string|null Наименование города отправителя */
    public $senderCity;

    /** @var float|null Широта города отправителя */
    public $senderLatitude;

    /** @var float|null Долгота города отправителя */
    public $senderLongitude;

    /** @var string|null Код страны получателя в формате ISO_3166-1_alpha-2 */
    public $receiverCountryCode;

    /** @var int|null Код города получателя из базы СДЭК */
    public $receiverCityId;

    /** @var int|null Индекс города получателя из базы СДЭК (если не задан receiverCityId) */
    public $receiverCityPostCode;

    /** @var string|null Наименование города получателя */
    public $receiverCity;

    /** @var float|null Широта города получателя */
    public $receiverLatitude;

    /** @var float|null Долгота города получателя */
    public $receiverLongitude;

    /** @var int Код выбранного тарифа (CdekApi::TARIF_TYPES) */
    public $tariffId;

    /**
     * @var array[] Список тарифов (если не задан tariffId)
     * - int $id код тарифа (CdekApi::TARIF_TYPES)
     * - int $priority Заданный приоритет
     * - в документации ошибка - modeId в тарифе не учитывается
     */
    public $tariffList;

    /**
     * @var int режим доставки (CdekApi::DELIVERY_TYPES) если указан tariffList
     * (ошибка в документации, modeId нет в списке тарифов, поэтому его нужно указывать даже при tariffList)
     */
    public $modeId;

    /**
     * @var array[] Габаритные характеристики места
     * - float $weight - Вес места, кг
     * - float $volume - Объём места, м³
     * - int $length - Длина места (в сантиметрах, если не задан volume)
     * - int $width - Ширина места (в сантиметрах, если не задан volume)
     * - int $height - Высота места (в сантиметрах, если не задан volume)
     */
    public $goods;

    /**
     * @var array[] Список передаваемых дополнительных услуг
     * - int $id - Идентификатор номера дополнительной услуги (CdekApi::SERVICE_TYPES)
     * - int $param - Параметр дополнительной услуги, если необходимо
     */
    public $services;

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'dateExecute' => 'Дата отправки',
            'lang' => 'Локализация',
            'senderCountryCode' => 'Код страны отправителя',
            'senderCityId' => 'Код города отправителя',
            'senderCityPostCode' => 'Индекс отправителя',
            'senderCity' => 'Город отправителя',
            'senderLatitude' => 'Широта отправителя',
            'senderLongitude' => 'Долгота отправителя',
            'receiverCountryCode' => 'Код страны получателя',
            'receiverCityId' => 'Код города получателя',
            'receiverCityPostCode' => 'Индекс получателя',
            'receiverCity' => 'Город получателя',
            'receiverLatitude' => 'Широта получателя',
            'receiverLongitude' => 'Долгота получателя',
            'tariffId' => 'Тариф',
            'tariffList' => 'Список тарифов',
            'goods' => 'Характеристики посылки',
        ];
    }

    /**
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            ['dateExecute', 'default'],
            ['dateExecute', 'date', 'format' => 'php:Y-m-d'],

            ['lang', 'trim'],
            ['lang', 'default', 'value' => 'rus'],
            ['lang', 'string', 'length' => 3],

            [['senderCountryCode', 'receiverCountryCode'], 'trim'],
            [['senderCountryCode', 'receiverCountryCode'], 'default', 'value' => 'ru'],
            [['senderCountryCode', 'receiverCountryCode'], 'string', 'length' => 2],

            [['senderCityId', 'receiverCityId'], 'default'],
            [['senderCityId', 'receiverCityId'], 'integer', 'min' => 1],
            [['senderCityId', 'receiverCityId'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            [['senderCityPostCode', 'receiverCityPostCode'], 'default'],
            [['senderCityPostCode', 'receiverCityPostCode'], 'integer', 'min' => 1, 'max' => 999999],
            [['senderCityPostCode', 'receiverCityPostCode'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            [['senderCity', 'receiverCity'], 'trim'],
            [['senderCity', 'receiverCity'], 'default'],

            [['senderLatitude', 'senderLongitude', 'receiverLatitude', 'receiverLongitude'], 'default'],
            [['senderLatitude', 'senderLongitude', 'receiverLatitude', 'receiverLongitude'], 'number',
             'min' => 0.000001],
            [['senderLatitude', 'senderLongitude', 'receiverLatitude', 'receiverLongitude'], 'filter',
             'filter' => 'floatval', 'skipOnEmpty' => true],

            [['senderCityId', 'senderCityPostCode'], 'required',
             'when' => static function(self $model, string $attribute) {
                 return empty($attribute === 'senderCityId' ? $model->senderCityPostCode : $model->senderCityId);
             }, 'skipOnEmpty' => false],

            [['receiverCityId', 'receiverCityPostCode'], 'required',
             'when' => static function(self $model, string $attribute) {
                 return empty($attribute === 'receiverCityId' ? $model->receiverCityPostCode : $model->receiverCityId);
             }, 'skipOnEmpty' => false],

            ['tariffId', 'default'],
            ['tariffId', 'in', 'range' => array_keys(CdekApi::TARIF_TYPES)],
            ['tariffId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['tariffList', 'default'],
            ['tariffList', function($attribute) {
                $list = [];

                foreach (array_values((array)($this->{$attribute} ?: [])) as $i => $tarif) {
                    if (is_numeric($tarif)) {
                        $tarif = ['id' => (int)$tarif];
                    } elseif (! is_array($tarif)) {
                        $this->addError($attribute, 'Некорректный тип данных тарифа');

                        return false;
                    }

                    $id = (int)($tarif['id'] ?? 0);
                    $priority = isset($tarif['priority']) ? (int)$tarif['priority'] : $i;

                    if (! array_key_exists($tarif['id'], CdekApi::TARIF_TYPES)) {
                        $this->addError($attribute, 'Некорректный код тарифа: ' . $tarif['id']);

                        return false;
                    }

                    $list[] = ['id' => $id, 'priority' => $priority];
                }

                $this->{$attribute} = $list ?: null;

                return true;
            }],

            [['tariffId', 'tariffList'], 'required', 'when' => static function(self $model, $attribute) {
                return empty($attribute === 'tariffId' ? $model->tariffList : $model->tariffId);
            }, 'skipOnEmpty' => false],

            ['modeId', 'default'],
            ['modeId', 'in', 'range' => array_keys(CdekApi::DELIVERY_TYPES)],
            ['modeId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['goods', 'required'],
            ['goods', function($attribute) {
                $goods = [];

                // проверяем параметры товаров посылки
                foreach ((array)($this->{$attribute} ?: []) as $good) {
                    $weight = (float)($good['weight'] ?? 0);
                    $width = (int)($good['width'] ?? 0);
                    $height = (int)($good['height'] ?? 0);
                    $length = (int)($good['length'] ?? 0);
                    $volume = (float)($good['volume'] ?? 0);

                    if ($weight <= 0) {
                        $this->addError($attribute, 'Некоректный вес товара');

                        return false;
                    }

                    $good = ['weight' => $weight];

                    if (! empty($width) && ! empty($height) && ! empty($length)) {
                        if ($width < 0 || $height < 0 || $length < 0) {
                            $this->addError($attribute, 'Некорректные размеры товара');

                            return false;
                        }

                        $good['width'] = $width;
                        $good['height'] = $height;
                        $good['length'] = $length;
                    } else {
                        if ($volume < 0) {
                            $this->addError($attribute, 'Некорректный объем посылки');

                            return false;
                        }

                        if (empty($volume)) {
                            $this->addError($attribute, 'Не заданы габариты посылки');
                        }

                        $good['volume'] = $volume;
                    }

                    $goods[] = $good;
                }

                if (empty($goods)) {
                    $this->addError($attribute, 'Не заданы параметры товаров посылки');

                    return false;
                }

                $this->{$attribute} = $goods;

                return true;
            }, 'skipOnEmpty' => false],

            ['services', 'default'],
            ['services', function($attribute) {
                $services = [];

                foreach ((array)($this->{$attribute} ?: []) as $service) {
                    $id = (int)($service['id'] ?? 0);
                    $param = (int)($service['param'] ?? 0);

                    if ($id < 1) {
                        $this->addError($attribute, 'Некорректный id сервиса');

                        return false;
                    }

                    $service = ['id' => $id];
                    if (! empty($param)) {
                        $service['param'] = $param;
                    }

                    $services[] = $service;
                }

                $this->{$attribute} = $services ?: null;

                return true;
            }]
        ];
    }

    /**
     * Возвращает параметры запроса.
     *
     * @return array
     */
    public function getParams()
    {
        $params = $this->toArray();
        $params['version'] = self::API_VERSION;

        if (! empty($this->api->login)) {
            $params['authLogin'] = $this->api->login;

            if (empty($params['dateExecute'])) {
                $params['dateExecute'] = date('Y-m-d');
            }

            $params['secure'] = md5($params['dateExecute'] . '&' . $this->api->password);
        }

        return array_filter($params, static function($param) {
            return $param !== null && $param !== '' && $param !== [];
        });
    }

    /**
     * Отправляет запрос и возвращает рассчет доставки.
     *
     * @return \dicr\cdek\CalcResult
     * @throws \yii\base\Exception
     */
    public function send()
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        // отправляем запрос
        $request = $this->api->post(self::REQUEST_URL, $this->params);
        $request->format = Client::FORMAT_JSON;
        $response = $request->send();
        if (! $response->isOk) {
            throw new Exception('Ошибка запроса: ' . $response->toString());
        }

        // декодируем ответ
        $response->format = Client::FORMAT_JSON;
        $json = $response->data;
        if ($json === null || empty($json['result']) || ! empty($json['error'])) {
            throw new Exception('Ошибка ответа: ' . $response->toString());
        }

        $result = new CalcResult($json['result']);

        if (isset($this->api->filterCalc)) {
            $result = ($this->api->filterCalc)($result, $this);
        }

        return $result;
    }
}
