<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 28.02.20 02:16:22
 */

declare(strict_types = 1);

namespace dicr\cdek;

use dicr\validate\ValidateException;
use yii\base\Exception;
use yii\httpclient\Client;
use function array_filter;
use function array_map;
use function is_array;

/**
 * Запрос списка городов.
 *
 * @property-read array $params
 * @package dicr\cdek
 * @see https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-%D0%9F%D1%80%D0%BE%D1%82%D0%BE%D0%BA%D0%BE%D0%BB%D0%BE%D0%B1%D0%BC%D0%B5%D0%BD%D0%B0%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8(v1.5)-4.13.City%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA%D0%B3%D0%BE%D1%80%D0%BE%D0%B4%D0%BE%D0%B2
 */
class CityRequest extends AbstractRequest
{
    /** @var string  адрес запроса */
    public const REQUEST_URL_XML = '/v1/location/cities';

    /** @var string адрес запроса */
    public const REQUEST_URL_JSON = '/v1/location/cities/json';

    /** @var int|null Код региона */
    public $regionCodeExt;

    /** @var int|null Код региона в ИС СДЭК */
    public $regionCode;

    /** @var string|null Код региона из ФИАС */
    public $regionFiasGuid;

    /** @var int|null Номер страницы выборки результата. По умолчанию 0 */
    public $page;

    /** @var int|null Ограничение выборки результата. По умолчанию 1000 */
    public $size;

    /** @var string string(2) Код страны в формате ISO 3166-1 alpha-2 */
    public $countryCode;

    /** @var string|null Название города */
    public $cityName;

    /**
     * @var int|null Код города по базе СДЭК
     * @link https://cdek.ru/storage/source/document/1/CDEK_city.zip
     */
    public $cityCode;

    /** @var string|null Почтовый индекс */
    public $postcode;

    /** @var string|null string(3) Локализация. По-умолчанию "rus" */
    public $lang;

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'regionCodeExt' => 'Код региона',
            'regionCode' => 'Код региона СДЭК',
            'regionFiasGuid' => 'Код региона ФИАС',
            'page' => 'Страница',
            'size' => 'Результатов',
            'countryCode' => 'Код страны',
            'cityName' => 'Название города',
            'cityCode' => 'Код города СДЭК',
            'postcode' => 'Почтовый индекс',
            'lang' => 'Локализация'
        ];
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            ['regionCodeExt', 'trim'],
            ['regionCodeExt', 'default'],
            ['regionCodeExt', 'string', 'max' => 10],

            [['regionCode', 'cityCode'], 'default'],
            [['regionCode', 'cityCode'], 'integer', 'min' => 1],
            [['regionCode', 'cityCode'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['regionFiasGuid', 'trim'],
            ['regionFiasGuid', 'default'],
            ['regionFiasGuid', 'string', 'max' => 45],

            ['page', 'default'],
            ['page', 'integer', 'min' => 0],
            ['page', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['size', 'default'],
            ['size', 'integer', 'min' => 1],
            ['size', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['countryCode', 'trim'],
            ['countryCode', 'default'],
            ['countryCode', 'string', 'length' => 2],

            ['cityName', 'trim'],
            ['cityName', 'default'],

            ['postcode', 'default'],
            ['postcode', 'integer', 'min' => 1],
            ['postcode', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['lang', 'default'],
            ['lang', 'string', 'length' => 3]
        ];
    }

    /**
     * Параметры запроса.
     *
     * @return array
     */
    public function getParams()
    {
        $params = $this->toArray();

        return array_filter($params, static function($param) {
            return $param !== null && $param !== '';
        });
    }

    /**
     * Отправка запроса.
     *
     * @return \dicr\cdek\City[]
     * @throws \yii\base\Exception
     */
    public function send()
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        // отправляем запрос
        $request = $this->api->get(self::REQUEST_URL_JSON, $this->params);
        $response = $request->send();
        if (! $response->isOk) {
            throw new Exception('Ошибка запроса: ' . $response->toString());
        }

        // декодируем ответ
        $response->format = Client::FORMAT_JSON;
        $json = $response->data;
        if (! is_array($json)) {
            throw new Exception('Некорректный ответ: ' . $response->toString());
        }

        return array_filter(array_map(function(array $config) {
            $city = new City($config);

            if (isset($this->api->filterCity)) {
                $city = ($this->api->filterCity)($city, $this);
            }

            return $city;
        }, $json));
    }
}
