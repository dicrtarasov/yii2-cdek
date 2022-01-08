<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 16:15:30
 */

declare(strict_types = 1);
namespace dicr\cdek\request;

use dicr\cdek\CdekRequest;
use dicr\cdek\entity\Region;

/**
 * Запрос информации о регионах.
 *
 * @property-read array $params данные для запроса
 */
class RegionRequest extends CdekRequest
{
    /** URL для получения ответа в XML */
    public const URL_XML = '/v1/location/regions';

    /** url для получения ответа в JSON */
    public const URL_JSON = '/v1/location/regions/json';

    /** Код региона */
    public ?string $regionCodeExt = null;

    /** Код региона в ИС СДЭК */
    public string|int|null $regionCode = null;

    /** UUID Код региона по ФИАС */
    public ?string $regionFiasGuid = null;

    /** Код страны в формате ISO 3166-1 alpha-2 */
    public ?string $countryCode = null;

    /** Код ОКСМ */
    public string|int|null $countryCodeExt = null;

    /** Номер страницы выборки результата. По умолчанию 0 */
    public string|int|null $page = null;

    /** Ограничение выборки результата. По умолчанию 1000 */
    public string|int|null $size = null;

    /** Локализация. По умолчанию "rus". */
    public ?string $lang = null;

    /**
     * @inheritDoc
     */
    public function attributeLabels(): array
    {
        return [
            'regionCodeExt' => 'Код региона',
            'regionCode' => 'Код региона в ИС СДЭК',
            'regionFiasGuid' => 'Код региона по ФИАС',
            'countryCode' => 'Код страны в формате ISO 3166-1 alpha-2',
            'countryCodeExt' => 'Код ОКСМ',
            'page' => 'Номер страницы',
            'size' => 'Кол-во результатов',
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

            [['regionCode', 'countryCodeExt'], 'default'],
            [['regionCode', 'countryCodeExt'], 'integer', 'min' => 1],
            [['regionCode', 'countryCodeExt'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['regionFiasGuid', 'trim'],
            ['regionFiasGuid', 'default'],
            ['regionFiasGuid', 'string', 'max' => 45],

            ['countryCode', 'trim'],
            ['countryCode', 'default'],
            ['countryCode', 'string', 'length' => 2],

            ['page', 'default'],
            ['page', 'integer', 'min' => 0],
            ['page', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['size', 'default'],
            ['size', 'integer', 'min' => 1],
            ['size', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['lang', 'trim'],
            ['lang', 'default'],
            ['lang', 'string', 'max' => 3]
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
     * {@inheritDoc}
     *
     * @return Region[]
     */
    public function send(): array
    {
        return array_map(static fn(array $json): Region => new Region([
            'json' => $json
        ]), parent::send());
    }
}
