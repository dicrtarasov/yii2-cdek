<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 27.02.20 02:19:33
 */

declare(strict_types = 1);

namespace dicr\cdek;

use dicr\validate\ValidateException;
use yii\base\Exception;
use yii\httpclient\Client;
use function array_filter;
use function array_map;

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
    public $page = 0;

    /** @var int|null Ограничение выборки результата. По умолчанию 1000 */
    public $size = 1000;

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
    public $lang = 'rus';

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
            'lang' => 'Язык'
        ];
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['regionCodeExt', 'regionCode', 'cityCode'], 'default'],
            [['regionCodeExt', 'regionCode', 'cityCode'], 'integer', 'min' => 1],
            [['regionCodeExt', 'regionCode', 'cityCode'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['regionFiasGuid', 'trim'],
            ['regionFiasGuid', 'default'],

            ['page', 'default', 'value' => 0],
            ['page', 'integer', 'min' => 0],
            ['page', 'filter', 'filter' => 'intval'],

            ['size', 'default', 'value' => 1000],
            ['size', 'integer', 'min' => 1],
            ['size', 'filter', 'filter' => 'intval'],

            ['countryCode', 'trim'],
            ['countryCode', 'default'],
            ['countryCode', 'string', 'length' => 2],

            ['cityName', 'trim'],
            ['cityName', 'default'],

            ['postcode', 'default'],
            ['postcode', 'integer', 'min' => 1],

            ['lang', 'default', 'value' => 'rus'],
            ['lang', 'string', 'length' => 3]
        ];
    }

    /**
     * Даные для запроса.
     *
     * @return array
     */
    public function getParams()
    {
        $params = $this->toArray();
        if ($params['page'] === 0) {
            unset($params['page']);
        }

        if ($params['size'] === 1000) {
            unset($params['size']);
        }

        if ($params['lang'] === 'rus') {
            unset($params['lang']);
        }

        return array_filter($params, static function($val) {
            return $val !== null;
        });
    }

    /**
     * Отправка запроса.
     *
     * @return array
     * @throws \dicr\validate\ValidateException
     * @throws \yii\base\Exception
     * @throws \yii\httpclient\Exception
     */
    public function send()
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        $request = $this->api->get(self::REQUEST_URL_JSON, $this->params);
        $response = $request->send();
        if (! $response->isOk) {
            throw new Exception('Ошибка запроса: ' . $response->toString());
        }

        $response->format = Client::FORMAT_JSON;
        $json = $response->data;

        return array_map(static function(array $config) {
            return new City($config);
        }, $json);
    }
}
