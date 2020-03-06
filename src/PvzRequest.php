<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.03.20 04:09:42
 */

declare(strict_types = 1);
namespace dicr\cdek;

use dicr\validate\ValidateException;
use yii\base\Exception;
use yii\httpclient\Client;
use function array_filter;
use function array_keys;

/**
 * Запрос списка ПВЗ (пунктов самовывоза).
 *
 * @property-read array $params данные для отправки
 */
class PvzRequest extends AbstractRequest
{
    /** @var string URL запроса для JSON-ответа */
    public const REQUEST_JSON = '/pvzlist/v1/json';

    /** @var string URL запроса для XML-ответа */
    public const REQUEST_XML = '/pvzlist/v1/xml';

    /** @var int|null почтовый индекс города (если не задан id) */
    public $citypostcode;

    /** @var int|null код города по базе СДЭК */
    public $cityid;

    /** @var string|null Тип пункта выдачи (Pvz::TYPES) */
    public $type;

    /** @var int|null Код страны по базе СДЭК */
    public $countryid;

    /** @var string|null [2] Код страны в формате ISO_3166-1_alpha-2 (см. “Общероссийский классификатор стран мира”) */
    public $countryiso;

    /** @var int|null Код региона по базе СДЭК */
    public $regionid;

    /** @var bool|null Наличие терминала оплаты */
    public $havecashless;

    /** @var bool|null Разрешен наложенный платеж */
    public $allowedcod;

    /** @var bool|null Наличие примерочной */
    public $isdressingroom;

    /** @var int|null Максимальный вес, который может принять ПВЗ
     *  - значения больше 0 - передаются ПВЗ, которые принимают этот вес;
     *  - 0 - все ПВЗ;
     *  - значение не указано - ПВЗ с нулевым весом не передаются.
     */
    public $weightmax;

    /** @var int|null Минимальный вес в кг, который принимает ПВЗ (при переданном значении будут выводиться ПВЗ с минимальным весом до указанного значения) */
    public $weightmin;

    /** @var string|null Локализация ПВЗ. По умолчанию "rus" */
    public $lang;

    /** @var bool|null Является ли ПВЗ только пунктом выдачи (либо только прием посылок на отправку) */
    public $takeonly;

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return [
            'citypostcode' => 'Почтовый индекс',
            'cityid' => 'Код города',
            'type' => 'Тип пункта выдачи',
            'countryid' => 'Код страны',
            'countryiso' => 'Код страны в формате ISO_3166-1',
            'regionid' => 'Код региона',
            'havecashless' => 'Есть терминал оплаты',
            'allowedcod' => 'Разрешен наложенный платеж',
            'isdressingroom' => 'Есть примерочная',
            'weightmax' => 'Максимальный вес',
            'weightmin' => 'Минимальный вес',
            'lang' => 'Локализация ПВЗ',
            'takeonly' => 'Только выдача'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['citypostcode', 'default'],
            ['citypostcode', 'integer', 'min' => 1],
            ['citypostcode', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            [['cityid', 'countryid', 'regionid'], 'default'],
            [['cityid', 'countryid', 'regionid'], 'integer', 'min' => 1],
            [['cityid', 'countryid', 'regionid'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['type', 'default'],
            ['type', 'in', 'range' => array_keys(Pvz::TYPES)],

            ['countryiso', 'default'],
            ['countryiso', 'string', 'length' => 2],

            [['havecashless', 'allowedcod', 'isdressingroom', 'takeonly'], 'default'],
            [['havecashless', 'allowedcod', 'isdressingroom', 'takeonly'], 'boolean'],
            [['havecashless', 'allowedcod', 'isdressingroom', 'takeonly'], 'filter', 'filter' => 'boolval',
             'skipOnEmpty' => true],

            [['weightmax', 'weightmin'], 'default'],
            [['weightmax', 'weightmin'], 'integer', 'min' => 0],
            [['weightmax', 'weightmin'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

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
        return array_filter($this->toArray(), static function($param) {
            return $param !== null && $param !== '' && $param !== [];
        });
    }

    /**
     * Возвращает список ПВЗ.
     *
     * @return \dicr\cdek\Pvz[] список ПВЗ
     * @throws \yii\base\Exception
     */
    public function send()
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        // делаем запрос к API
        $request = $this->api->get(self::REQUEST_JSON, $this->params);
        $response = $request->send();
        if (! $response->isOk) {
            throw new Exception('Ошибка запроса: ' . $response->toString());
        }

        // декодируем ответ
        $response->format = Client::FORMAT_JSON;
        $json = $response->data;
        if ($json === null || ! isset($json['pvz'])) {
            throw new Exception('Некорректный ответ: ' . $response->toString());
        }

        return array_filter(array_map(function(array $config) {
            $pvz = new Pvz($config);

            // пользовательский фильтр
            if (isset($this->api->filterPvz)) {
                $pvz = ($this->api->filterPvz)($pvz, $this);
            }

            return $pvz;
        }, $json['pvz']));
    }
}
