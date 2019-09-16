<?php
namespace dicr\cdek;

use yii\base\Exception;
use yii\caching\TagDependency;
use yii\helpers\Json;

/**
 * Запрос информации о регионах.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class RegionRequest extends AbstractRequest
{
    /** @var string URL для получения ответа в XML */
    const URL_XML = '/v1/location/regions';

    /** @var string url для получения ответа в JSON */
    const URL_JSON = '/v1/location/regions/json';

    /** @var string [10] Код региона */
    public $regionCodeExt;

    /** @var int Код региона в ИС СДЭК */
    public $regionCode;

    /** @var string UUID Код региона по ФИАС */
    public $regionFiasGuid;

    /** @var string (2) Код страны в формате ISO 3166-1 alpha-2 */
    public $countryCode;

    /** @var int Код ОКСМ */
    public $countryCodeExt;

    /** @var int Номер страницы выборки результата. По умолчанию 0 */
    public $page;

    /** @var int Ограничение выборки результата. По умолчанию 1000 */
    public $size;

    /** @var string [3] Локализация. По умолчанию "rus". */
    public $lang;

    /**
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [['regionCodeExt', 'regionCode', 'countryCodeExt'], 'default'],
            [['regionCodeExt', 'regionCode', 'countryCodeExt'], 'integer', 'min' => 1],
            [['regionCodeExt', 'regionCode', 'countryCodeExt'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['regionFiasGuid', 'default'],
            ['regionFiasGuid', 'string', 'max' => 45],

            ['countryCode', 'default'],
            ['countryCode', 'string', 'length' => 2],

            ['page', 'default'],
            ['page', 'integer', 'min' => 0],
            ['page', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['size', 'default'],
            ['size', 'integer', 'min' => 1],
            ['size', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['lang', 'default'],
            ['lang', 'string', 'max' => 3]
        ];
    }

    /**
     * Отправляет запрос и возвращает список регионов.
     *
     * @throws Exception
     * @return array|\dicr\cdek\Region
     */
    public function send()
    {
        if (!$this->validate()) {
            throw new Exception('Ошибка валидации: ' . array_values($this->firstErrors)[0]);
        }

        $request = $this->api->get(self::URL_JSON, $this->toArray());
        $key = $request->toString();
        $content = null;

        // пробуем достать из кэша
        if (!empty($this->api->catalogCache)) {
            $content = $this->api->catalogCache->get($key);
            if ($content !== false) {
                $content = @gzuncompress($content);
            }
        }

        // отправляем запрос
        if ($content === null || $content === false) {
            $response = $request->send();
            if (!$response->isOk) {
                throw new Exception('Некорректный ответ от СДЭК: ' . $response->statusCode);
            }

            $content = $response->content;
        }

        // декодируем ответ
        $json = Json::decode($content, true);
        if ($json === null) {
            throw new Exception('Ошибка декодирование ответа СДЭК: ' . $content);
        }

        // сохраняем в кеш
        if (!empty($this->api->catalogCache)) {
            $this->api->catalogCache->set($key, gzcompress($content), $this->api->catalogCacheDuration, new TagDependency([
                'tags' => [__CLASS__, __NAMESPACE__]
            ]));
        }

        $content = null;

        return array_map(function($config) {
            return new Region($config);
        }, $json);
    }
}