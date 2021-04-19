<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.04.21 14:29:07
 */

declare(strict_types = 1);

namespace dicr\cdek\request;

use dicr\cdek\CdekRequest;
use dicr\cdek\entity\City;

use function array_map;

/**
 * Запрос списка городов.
 *
 * @property-read array $params
 * @package dicr\cdek
 * @see https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-%D0%9F%D1%80%D0%BE%D1%82%D0%BE%D0%BA%D0%BE%D0%BB%D0%BE%D0%B1%D0%BC%D0%B5%D0%BD%D0%B0%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8(v1.5)-4.13.City%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA%D0%B3%D0%BE%D1%80%D0%BE%D0%B4%D0%BE%D0%B2
 */
class CityRequest extends CdekRequest
{
    /** @var string адрес запроса */
    public const URL_JSON = '/v1/location/cities/json';

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
    public function attributeLabels(): array
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
    public function rules(): array
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
     * @inheritDoc
     */
    protected function method(): string
    {
        return 'GET';
    }

    /**
     * @inheritDoc
     */
    protected function url(): array
    {
        return [self::URL_JSON] + $this->json;
    }

    /**
     * {@inheritDoc}}
     *
     * @return City[]
     */
    public function send(): array
    {
        return array_map(static fn(array $json): City => new City([
            'json' => $json
        ]), parent::send());
    }
}
