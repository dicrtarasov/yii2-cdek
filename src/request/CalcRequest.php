<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 16:27:01
 */

declare(strict_types = 1);
namespace dicr\cdek\request;

use ArrayAccess;
use dicr\cdek\Cdek;
use dicr\cdek\CdekRequest;
use dicr\cdek\entity\Good;
use dicr\cdek\entity\ServiceParams;
use dicr\cdek\entity\Tariff;
use dicr\json\EntityValidator;

use function array_merge;
use function array_values;
use function date;
use function is_array;
use function md5;

/**
 * Запрос расчета доставки.
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
class CalcRequest extends CdekRequest
{
    public const API_VERSION = '1.0';

    /** URL API калькулятора */
    public const URL = '/calculator/calculate_price_by_json.php';

    /** Планируемая дата отправки заказа в формате “ГГГГ-ММ-ДД” */
    public ?string $dateExecute = null;

    /** Локализация названий городов. По умолчанию "rus" */
    public ?string $lang = null;

    /** Код страны отправителя в формате ISO_3166-1_alpha-2 */
    public ?string $senderCountryCode = null;

    /** Код города отправителя из базы СДЭК */
    public ?int $senderCityId = null;

    /** Индекс города отправителя из базы СДЭК (если не задан senderCityId) */
    public string|int|null $senderCityPostCode = null;

    /** Наименование города отправителя */
    public ?string $senderCity = null;

    /** Широта города отправителя */
    public ?float $senderLatitude = null;

    /** Долгота города отправителя */
    public ?float $senderLongitude = null;

    /** Код страны получателя в формате ISO_3166-1_alpha-2 */
    public ?string $receiverCountryCode = null;

    /** Код города получателя из базы СДЭК */
    public ?int $receiverCityId = null;

    /** Индекс города получателя из базы СДЭК (если не задан receiverCityId) */
    public ?int $receiverCityPostCode = null;

    /** Наименование города получателя */
    public ?string $receiverCity = null;

    /** Широта города получателя */
    public ?float $receiverLatitude = null;

    /** Долгота города получателя */
    public ?float $receiverLongitude = null;

    /** Код выбранного тарифа (CdekApi::TARIF_TYPES) */
    public ?int $tariffId = null;

    /** @var Tariff[]|null Список тарифов (если не задан tariffId) */
    public ?array $tariffList = null;

    /**
     * режим доставки (CdekApi::DELIVERY_TYPES) если указан tariffList
     * (ошибка в документации, modeId нет в списке тарифов, поэтому его нужно указывать даже при tariffList)
     */
    public ?int $modeId = null;

    /** @var Good[]|null Габаритные характеристики места */
    public ?array $goods = null;

    /** @var ServiceParams[]|null Список передаваемых дополнительных услуг */
    public ?array $services = null;

    /**
     * @inheritDoc
     */
    public function attributeLabels(): array
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
     */
    public function rules(): array
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
                'when' => static fn(self $model, string $attribute): bool => $attribute === 'senderCityId' ?
                    empty($model->senderCityPostCode) :
                    empty($model->senderCityId),
                'skipOnEmpty' => false],

            [['receiverCityId', 'receiverCityPostCode'], 'required',
                'when' => static fn(self $model, string $attribute): bool => $attribute === 'receiverCityId' ?
                    empty($model->receiverCityPostCode) :
                    empty($model->receiverCityId),
                'skipOnEmpty' => false],

            ['tariffId', 'default'],
            ['tariffId', 'in', 'range' => array_keys(Cdek::TARIF_TYPES)],
            ['tariffId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['tariffList', 'default'],
            ['tariffList', function() {
                $this->tariffList = array_values($this->tariffList);
                foreach ($this->tariffList as $i => &$tarif) {
                    if ((is_array($tarif) || ($tarif instanceof ArrayAccess)) && ! isset($tarif['priority'])) {
                        $tarif['priority'] = $i;
                    }
                }
            }, 'skipOnEmpty' => true],
            ['tariffList', EntityValidator::class],

            [['tariffId', 'tariffList'], 'required',
                'when' => static fn(self $model, $attribute) => $attribute === 'tariffId' ? empty($model->tariffList) :
                    empty($model->tariffId),
                'skipOnEmpty' => false],

            ['modeId', 'default'],
            ['modeId', 'in', 'range' => array_keys(Cdek::DELIVERY_TYPES)],
            ['modeId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['goods', 'required'],
            ['goods', EntityValidator::class],

            ['services', 'default'],
            ['services', EntityValidator::class],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeEntities(): array
    {
        return [
            'tariffList' => [Tariff::class],
            'goods' => [Good::class],
            'services' => [ServiceParams::class]
        ];
    }

    /**
     * @inheritDoc
     */
    protected function method(): string
    {
        return 'POST';
    }

    /**
     * @inheritDoc
     */
    protected function url(): string
    {
        return ($this->api->debug ? Cdek::URL_CALC_TEST : Cdek::URL_CALC) . self::URL;
    }

    /**
     * @inheritDoc
     */
    protected function data(): array
    {
        $date = date('Y-m-d');

        return array_merge($this->json, [
            'version' => self::API_VERSION,
            'authLogin' => $this->api->debug ? Cdek::LOGIN_TEST : $this->api->login,
            'dateExecute' => $date,
            'secure' => md5($date . '&' . ($this->api->debug ? Cdek::PASSWORD_TEST : $this->api->password))
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function send(): CalcResult
    {
        $data = parent::send();

        return new CalcResult([
            'json' => $data['result']
        ]);
    }
}
