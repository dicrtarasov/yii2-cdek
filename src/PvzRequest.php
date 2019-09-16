<?php
namespace dicr\cdek;

use yii\base\Exception;
use yii\caching\TagDependency;
use yii\helpers\Json;

/**
 * Запрос списка ПВЗ (пунктов самовывоза).
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class PvzRequest extends AbstractRequest
{
    /** @var string URL запроса */
    const REQUEST_JSON = '/pvzlist/v1/json';

    /** @var string URL запроса */
    const REQUEST_XML = '/pvzlist/v1/xml';

    /** @var string почтовый индекс города */
    public $citypostcode;

    /** @var int код города по базе СДЭК */
    public $cityid;

    /** @var string Тип пункта выдачи */
    public $type;

    /** @var int Код страны по базе СДЭК */
    public $countryid;

    /** @var string Код страны в формате ISO_3166-1_alpha-2 (см. “Общероссийский классификатор стран мира”) */
    public $countryiso;

    /** @var int Код региона по базе СДЭК */
    public $regionid;

    /** @var bool Наличие терминала оплаты */
    public $havecashless;

    /** @var bool Разрешен наложенный платеж */
    public $allowedcod;

    /** @var bool Наличие примерочной */
    public $isdressingroom;

    /** @var int Максимальный вес, который может принять ПВЗ
     *  - значения больше 0 - передаются ПВЗ, которые принимают этот вес;
     *  - 0 - все ПВЗ;
     *  - значение не указано - ПВЗ с нулевым весом не передаются.
     */
    public $weightmax;

    /** @var string Локализация ПВЗ. По умолчанию "rus" */
    public $lang;

    /** @var bool Является ли ПВЗ только пунктом выдачи (либо только прием посылок на отправку) */
    public $takeonly;

    /**
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            ['citypostcode', 'default'],

            ['cityid', 'default'],
            ['cityid', 'integer', 'min' => 1],
            ['cityid', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['type', 'default'],
            ['type', 'in', 'range' => [Pvz::TYPE_PVZ, Pvz::TYPE_POSTOMAT]],

            ['countryid', 'default'],
            ['countryid', 'integer', 'min' => 1],

            ['countryiso', 'default'],
            ['countryiso', 'string', 'length' => 2],

            ['regionid', 'default'],
            ['regionid', 'integer', 'min' => 1],

            [['havecashless', 'allowedcod', 'isdressingroom', 'takeonly'], 'default'],
            [['havecashless', 'allowedcod', 'isdressingroom', 'takeonly'], 'boolean'],
            [['havecashless', 'allowedcod', 'isdressingroom', 'takeonly'], 'filter', 'filter' => 'boolval', 'skipOnEmpty' => true],

            ['weightmax', 'default'],
            ['weightmax', 'integer', 'min' => 0],

            ['lang', 'default'],
            ['lang', 'string', 'length' => 3]
        ];
    }

	/**
	 * {@inheritDoc}
	 * @see \yii\base\Model::attributeLabels()
	 */
	public function attributeLabels()
	{
		return [
			'cityid' => 'Код города',
			'citypostcode' => 'Почтовый индекс',
			'countryid' => 'Код страны',
			'regionid' => 'Код региона',
			'havecashless' => 'Есть терминал оплаты',
			'isdressingroom' => 'Есть примерочная',
			'weightmax' => 'Максимальный вес',
			'allowedcod' => 'Разрешен наложенный платеж'
		];
	}

    /**
     * Возвращает список ПВЗ.
     *
     * @throws \yii\base\Exception
     * @return \dicr\cdek\Pvz[] список ПВЗ
     */
    public function send()
    {
        if (!$this->validate()) {
            throw new Exception('Ошибка валиации: ' . rarray_values($this->firstErrors)[0]);
        }

        /** @var \yii\httpclient\Request */
        $request = $this->api->get(self::REQUEST_JSON, $this->toArray());
        $key = $request->toString();
        $content = null;

        /** @var string ответ */
        if (!empty($this->api->catalogCache)) {
            $content = $this->api->catalogCache->get($key);
            if ($content !== false) {
                $content = @gzuncompress($content);
            }
        }

        // делаем запрос к API
        if ($content === null || $content === false) {
            $response = $request->send();
			if (!$response->isOk) {
			    throw new Exception('Ошибка ответа СДЭК: '.$response->statusCode);
			}

			$content = $response->content;
        }

        // декодируем ответ
        $json = Json::decode($content, true);
        if ($json === null || !isset($json['pvz'])) {
            throw new Exception('Ошибка ответа СДЭК: ' . $content);
        }

        // сохраняем в кеше
        if (!empty($this->api->catalogCache)) {
            $this->api->catalogCache->set($key, gzcompress($content), $this->api->catalogCacheDuration, new TagDependency([
                'tags' => [__CLASS__, __NAMESPACE__]
            ]));
        }

		$content = null;

		return array_map(function($config) {
		    return new Pvz($config);
		}, $json['pvz']);
    }
}
