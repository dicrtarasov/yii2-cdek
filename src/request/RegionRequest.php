<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 07:49:46
 */

declare(strict_types = 1);
namespace dicr\cdek\request;

use dicr\cdek\AbstractRequest;
use dicr\cdek\entity\Region;
use yii\base\Exception;
use yii\httpclient\Request;

use function array_merge;

/**
 * Запрос информации о регионах.
 *
 * @property-read array $params данные для запроса
 */
class RegionRequest extends AbstractRequest
{
    /** @var string URL для получения ответа в XML */
    public const URL_XML = '/v1/location/regions';

    /** @var string url для получения ответа в JSON */
    public const URL_JSON = '/v1/location/regions/json';

    /** @var string|null [10] Код региона */
    public $regionCodeExt;

    /** @var int|null Код региона в ИС СДЭК */
    public $regionCode;

    /** @var string|null UUID Код региона по ФИАС */
    public $regionFiasGuid;

    /** @var string|null [2] Код страны в формате ISO 3166-1 alpha-2 */
    public $countryCode;

    /** @var int|null Код ОКСМ */
    public $countryCodeExt;

    /** @var int|null Номер страницы выборки результата. По умолчанию 0 */
    public $page;

    /** @var int|null Ограничение выборки результата. По умолчанию 1000 */
    public $size;

    /** @var string|null [3] Локализация. По умолчанию "rus". */
    public $lang;

    /**
     * @inheritDoc
     */
    public function attributeLabels() : array
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
     * {@inheritDoc}
     */
    public function rules() : array
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
    protected function httpRequest() : Request
    {
        return $this->api->httpClient->get(array_merge($this->json, [
            0 => self::URL_JSON
        ]), null, [
            'Accept' => 'application/json'
        ]);
    }

    /**
     * Отправляет запрос и возвращает список регионов.
     *
     * @return Region[]
     * @throws Exception
     */
    public function send() : array
    {
        return array_map(static function (array $json) : Region {
            return new Region([
                'json' => $json
            ]);
        }, parent::send());
    }
}
