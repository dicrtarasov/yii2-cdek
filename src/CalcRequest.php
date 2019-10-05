<?php
namespace dicr\cdek;

use yii\base\Exception;
use yii\caching\TagDependency;
use yii\helpers\Json;
use yii\httpclient\Client;

/**
 * Запрос рассчета доставки.
 *
 * При использовании тарифов для обычной доставки авторизация не обязательна и параметры authLogin и secure можно не передавать.
 *
 * Дата планируемой отправки dateExecute не обязательна (в этом случае принимается текущая дата).
 * Но, если вы работаете с авторизацией, она должна быть передана, так как дата учитывается при шифровании/дешифровке пароля.
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
 * Для услуги 2 - страховка в param необходимо передать сумму, с которой будет рассчитана страховка (необходимо передавать
 * в валюте взаиморасчетов). Услуга 30 доступна только для договора ИМ, поэтому в запросе должны быть переданы значения
 * authLogin и secure. Для услуг 24,25 и 32 в param передается значение количества.
 *
 * @link https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-%D0%9F%D1%80%D0%BE%D1%82%D0%BE%D0%BA%D0%BE%D0%BB%D0%BE%D0%B1%D0%BC%D0%B5%D0%BD%D0%B0%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8(v1.5)-4.14.2.%D0%A0%D0%B0%D1%81%D1%87%D0%B5%D1%82%D1%81%D1%82%D0%BE%D0%B8%D0%BC%D0%BE%D1%81%D1%82%D0%B8%D0%BF%D0%BE%D1%82%D0%B0%D1%80%D0%B8%D1%84%D0%B0%D0%BC%D0%B1%D0%B5%D0%B7%D0%BF%D1%80%D0%B8%D0%BE%D1%80%D0%B8%D1%82%D0%B5%D1%82%D0%B0
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 180415
 */
class CalcRequest extends AbstractRequest
{
    /** @var string API version */
    const API_VERSION = '1.0';

    /** @var string URL API калькулятора */
    const REQUEST_URL = 'http://api.cdek.ru/calculator/calculate_price_by_json.php';

    /** @var string Планируемая дата отправки заказа в формате “ГГГГ-ММ-ДД” */
    public $dateExecute;

    /** @var int Код города отправителя из базы СДЭК */
    public $senderCityId;

    /** @var int Индекс города отправителя из базы СДЭК */
    public $senderCityPostCode;

    /** @var int Код города получателя из базы СДЭК */
    public $receiverCityId;

    /** @var int Индекс города получателя из базы СДЭК */
    public $receiverCityPostCode;

    /** @var int Код выбранного тарифа (CdekApi::TARIF_TYPES) */
    public $tariffId;

    /**
     * @var array Список тарифов
     * - int $id код тарифа (CdekApi::TARIF_TYPES)
     * - int $priority Заданный приоритет
     * - int $modeId режим доставки (CdekApi::DELIVERY_TYPES)
     */
    public $tariffList;

    /**
     * @var array Габаритные характеристики места
     * - float $weight - Вес места (в килограммах)
     * - int $length - Длина места (в сантиметрах)
     * - int $width - Ширина места (в сантиметрах)
     * - int $height - Высота места (в сантиметрах)
     * - float $volume - Объём места (в м³)
     */
    public $goods;

    /**
     * @var array[] Список передаваемых дополнительных услуг
     * - int $id - Идентификатор номера дополнительной услуги (CdekApi::SERVICE_TYPES)
     * - int $param - Параметр дополнительной услуги, если необходимо
     */
    public $services;

	/**
	 * {@inheritDoc}
	 * @see \yii\base\Model::rules()
	 */
	public function rules()
	{
		return [
		    ['dateExecute', 'default', 'value' => date('Y-m-d')],
		    ['dateExecute', 'date', 'format' => 'php:Y-m-d'],

			[['senderCityId', 'receiverCityId'], 'default'],
			[['senderCityId', 'receiverCityId'], 'integer', 'min' => 1],
		    [['senderCityId', 'receiverCityId'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

			[['senderCityPostCode', 'receiverCityPostCode'], 'default'],
			[['senderCityPostCode', 'receiverCityPostCode'], 'integer', 'min' => 1, 'max' => 999999],
		    [['senderCityPostCode', 'receiverCityPostCode'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

		    [['senderCityId', 'senderCityPostCode'], function($attribute, $params, $validator) {
		        if (empty($this->senderCityId) && empty($this->senderCityPostCode)) {
		            if (!empty($this->api->defaultSenderCityId) || !empty($this->api->defaultSenderCityPostCode)) {
		                $this->senderCityId = $this->api->defaultSenderCityId;
		                $this->senderCityPostCode = $this->api->defaultSenderCityPostCode;
		            } else {
                        $this->addError($attribute, 'Требуется senderCityId либо senderCityPostCode');
		            }
		        }
		    }, 'skipOnEmpty' => false],

		    [['receiverCityId', 'receiverCityPostCode'], function($attribute, $params, $validator) {
		        if (empty($this->receiverCityId) && empty($this->receiverCityPostCode)) {
		            $this->addError($attribute, 'Требуется receiverCityId либо receiverCityPostCode');
		        }
		    }, 'skipOnEmpty' => false],

			['tariffId', 'default'],
			['tariffId', 'in', 'range' => array_keys(CdekApi::TARIF_TYPES)],
			['tariffId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

			['tariffList', 'default'],
			['tariffList', 'validateTariffList', 'skipOnEmpty' => true],

			[['tariffId', 'tariffList'], function($attribute, $params, $validator) {
			    // если не установлен ни код тарифа, ни список тарифов
			    if (empty($this->tariffId) && empty($this->tariffList)) {
			        // если заданы значения по-умолчанию
			        if (!empty($this->api->defaultTariffId) || !empty($this->api->defaultTariffList)) {
			            // берем значения по-умолчанию
			            $this->tariffId = $this->api->defaultTariffId;
			            $this->tariffList = $this->api->defaultTariffList;

			            // повторно проверяем список тарифов
			            if (!empty($this->tariffList)) {
			                $this->validateTariffList();
			            }
			        } else {
                        $this->addError($attribute, 'необходимо установить tariffId либо tariffList');
			        }
			    }
			}, 'skipOnEmpty' => false],

			['goods', 'default', 'value' => []],
			['goods', 'validateGoods', 'skipOnEmpty' => false],

			['services', 'default', 'value' => $this->api->defaultServices],
			['services', 'validateServices', 'skipOnEmpty' => false],
		];
	}

	/**
	 * Проверяет список тарифов.
	 */
	public function validateTariffList()
	{
	    $attribute = 'tariffList';
	    $val = (array)($this->{$attribute} ?: []);

	    if (empty($val)) {
	        return $this->{$attribute} = null;
	    }

	    foreach ($val as $i => $tarif) {
	        if (is_numeric($tarif)) {
	            $tarif = [
	                'id' => (int)$tarif
	            ];
	        } elseif (!is_array($tarif)) {
                return $this->addError($attribute, 'Некорректный тип данных тарифа');
            }

            if (!in_array($tarif['id'] ?? '', array_keys(CdekApi::TARIF_TYPES))) {
                return $this->addError($attribute, 'Некорректный код тарифа: ' . $tarif['id']);
            }

            if (!isset($tarif['priority'])) {
                $tarif['priority'] = (int)$i;
            } else {
                $tarif['priority'] = (int)($tarif['priority'] ?? 0);
            }

            if (isset($tarif['modeId']) && !in_array($tarif['modeId'], array_keys(CdekApi::DELIVERY_TYPES))) {
                return $this->addError($attribute, 'Некорректный тип доставки');
            }

            $val[$i] = $tarif;
	    }

	    $this->{$attribute} = $val;
	}

	/**
	 * Валидация товаров.
	 */
	public function validateGoods()
	{
	    $attribute = 'goods';
	    $val = (array)($this->{$attribute} ?: []);

	    // если товары не заданы, то используем парамеры посылки по-умолчанию
	    if (empty($val)) {
	        if (!empty($this->api->defaultWeight) && !empty($this->api->defaultVolume)) {
	            $val = [
	                ['weight' => $this->api->defaultWeight, 'volume' => $this->api->defaultVolume]
	            ];
	        } else {
	            return $this->addError($attribute, 'Не заданы товары и параметры веса и объема посылки по-умолчанию');
	        }
	    }

	    // проверяем параметры товаров посылки
	    foreach ($val as $i => $good) {
	        // если не задан вес товара, то берем по-умолчанию
	        if (empty($good['weight'])) {
	            $good['weight'] = $this->api->defaultWeight;
	        }

	        // проверяем вес товара
	        $good['weight'] = (float)$good['weight'];
	        if ($good['weight'] <= 0) {
	            return $this->addError($attribute, 'Некорректный вес товара');
	        }

	        // если не заданы никакие размеры, то берем объем послыки по-умолчанию
	        if (empty($good['volume']) && empty($good['width']) && empty($good['height']) && empty($good['length'])) {
	            $good['colume'] = $this->api->defaultVolume;
	        }

            // проверяем вариант указания объема
	        if (isset($good['volume'])) {
	            $good['volume'] = (float)$good['volume'];
	            if ($good['volume'] <= 0) {
	                return $this->addError($attribute, 'Некоректный обьем товара');
	            }
	        } else {
	            // проверяем вариант через линейные размеры
	            foreach (['length', 'width', 'height'] as $field) {
	                $good[$field] = (int)($good[$field] ?? 0);
	                if ($good[$field] <= 0) {
	                    return $this->addError($attribute, 'Некорректный ' . $field . ' товара');
	                }
	            }
	        }

	        $val[$i] = $good;
	    }

	    $this->{$attribute} = $val;
	}

	/**
	 * Валидация сервисов.
	 */
	public function validateServices()
	{
	    $attribute = 'services';

	    $val = $this->{$attribute};
	    if (empty($val)) {
	        $this->{$attribute} = null;
	        return true;
	    }

	    if (!is_array($val)) {
	        $this->addError($attribute, 'Некорректный тип значения сервисов');
	        return false;
	    }

	    foreach ($val as $i => $service) {
	        if (is_numeric($service)) {
	            $service = ['id' => $service];
	        } elseif (!is_array($service)) {
	            $this->addError($attribute, 'некорректный тип значения');
	            return false;
	        }

	        $service['id'] = (int)($service['id'] ?? 0);
	        if (!in_array($service['id'], array_keys(CdekApi::SERVICE_TYPES))) {
	            $this->addError($attribute, 'некорректный код сервиса');
	            return false;
	        }

	        $val[$i] = $service;
	    }

	    $this->{$attribute} = $val;
	    return true;
	}

	/**
	 * Возвращает секретный ключ.
	 *
	 * @return string|null
	 */
	protected function getSecure()
	{
	    if (empty($this->api->password)) {
	        return null;
	    }

	    if (empty($this->dateExecute)) {
	        $this->dateExecute = date('Y-m-d');
	    }

	    return md5($this->dateExecute . '&' . $this->api->password);
	}

	/**
	 * Отправляет запрос и возвращает рассчет доставки.
	 *
	 * @throws \yii\base\Exception
	 * @return \dicr\cdek\CalcResult
	 */
	public function send()
	{
	    if (!$this->validate()) {
	        throw new Exception('Ошибка валидации: ' . array_values($this->firstErrors)[0]);
	    }

	    $data = array_merge($this->toArray(), [
            'version' => self::API_VERSION
        ]);

	    if (isset($this->api->login)) {
	        $data['authLogin'] = $this->api->login;
	        $data['secure'] = $this->getSecure();
	    }

	    $request = $this->api->post(self::REQUEST_URL);
	    $request->format = Client::FORMAT_JSON;
	    $request->data = array_filter($data, function($val) {
	        return $val !== null && $val !== '';
	    });

	    $content = null;
	    $key = $request->toString();

	    // берем из кэша
	    if (!empty($this->api->calcCache)) {
	        $content = $this->api->calcCache->get($key);
	        if (!empty($content)) {
	            $content = @gzdecode($content);
	        }
	    }

	    // отправляем запрос
	    if (!isset($content) || $content === false) {
	        $response = $request->send();
	        if (!$response->isOk) {
	            throw new Exception('Ошибка запроса СДЭК: ' . $response->statusCode);
	        }

	        $content = $response->content;
	    }

	    // декодируем Json
	    $json = Json::decode($content, true);
	    if ($json === null || empty($json['result'])) {
	        if (!empty($json['error'][0]['text'])) {
	            throw new Exception('Ошибка СДЭК: ' . $json['error'][0]['text']);
	        }

	        throw new Exception('Ошибка декодирования ответа СДЭК: ' . $content);
	    }

	    // сохраняем контент в кеш
	    if (!empty($this->api->calcCache)) {
	        $this->api->calcCache->set($key, gzencode($content), $this->api->calcCacheDuration, new TagDependency([
	            'tags' => [__CLASS__, __NAMESPACE__]
	        ]));
	    }

	    $content = null;

	    return new CalcResult($json['result']);
	}
}