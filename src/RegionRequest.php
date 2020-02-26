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
    public $page = 0;

    /** @var int|null Ограничение выборки результата. По умолчанию 1000 */
    public $size = 1000;

    /** @var string|null [3] Локализация. По умолчанию "rus". */
    public $lang = 'rus';

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['regionCodeExt', 'default'],
            ['regionCodeExt', 'string', 'max' => 10],

            [['regionCode', 'countryCodeExt'], 'default'],
            [['regionCode', 'countryCodeExt'], 'integer', 'min' => 1],
            [['regionCode', 'countryCodeExt'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['regionFiasGuid', 'default'],
            ['regionFiasGuid', 'string', 'max' => 45],

            ['countryCode', 'default'],
            ['countryCode', 'string', 'length' => 2],

            ['page', 'default', 'value' => 0],
            ['page', 'integer', 'min' => 0],
            ['page', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['size', 'default', 'value' => 1000],
            ['size', 'integer', 'min' => 1],
            ['size', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['lang', 'default', 'value' => 'rus'],
            ['lang', 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
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
     * Отправляет запрос и возвращает список регионов.
     *
     * @return array|\dicr\cdek\Region
     * @throws Exception
     */
    public function send()
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        $request = $this->api->get(self::URL_JSON, $this->params);

        // отправляем запрос
        $response = $request->send();
        if (! $response->isOk) {
            throw new Exception('Некорректный ответ от СДЭК: ' . $response->toString());
        }

        // декодируем ответ
        $response->format = Client::FORMAT_JSON;
        $json = $response->data;
        if ($json === null) {
            throw new Exception('Ошибка декодирование ответа СДЭК: ' . $response->toString());
        }

        return array_map(static function($config) {
            return new Region($config);
        }, $json);
    }
}
