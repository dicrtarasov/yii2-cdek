<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 16:11:54
 */

declare(strict_types = 1);
namespace dicr\cdek\request;

use dicr\cdek\Cdek;
use dicr\cdek\CdekRequest;
use dicr\cdek\entity\Pvz;

use function array_keys;
use function array_map;

/**
 * Запрос списка ПВЗ (пунктов самовывоза).
 *
 * @property-read array $params данные для отправки
 */
class PvzRequest extends CdekRequest
{
    /** URL запроса для JSON-ответа */
    public const URL_JSON = '/pvzlist/v1/json';

    /** URL запроса для XML-ответа */
    public const URL_XML = '/pvzlist/v1/xml';

    /** почтовый индекс города (если не задан id) */
    public string|int|null $citypostcode = null;

    /** код города по базе СДЭК */
    public string|int|null $cityid = null;

    /** Тип пункта выдачи (Pvz::TYPES) */
    public ?string $type = null;

    /** Код страны по базе СДЭК */
    public string|int|null $countryid = null;

    /** Код страны в формате ISO_3166-1_alpha-2 (см. “Общероссийский классификатор стран мира”) */
    public ?string $countryiso = null;

    /** Код региона по базе СДЭК */
    public string|int|null $regionid = null;

    /** Наличие терминала оплаты */
    public ?bool $havecashless = null;

    /** Разрешен наложенный платеж */
    public ?bool $allowedcod = null;

    /** Наличие примерочной */
    public ?bool $isdressingroom = null;

    /**
     * Максимальный вес, который может принять ПВЗ
     *  - значения больше 0 - передаются ПВЗ, которые принимают этот вес;
     *  - 0 - все ПВЗ;
     *  - значение не указано - ПВЗ с нулевым весом не передаются.
     */
    public string|int|null $weightmax = null;

    /** Минимальный вес в кг, который принимает ПВЗ (при переданном значении будут выводиться ПВЗ с минимальным весом до указанного значения) */
    public string|int|null $weightmin = null;

    /** Локализация ПВЗ. По умолчанию "rus" */
    public ?string $lang = null;

    /** Является ли ПВЗ только пунктом выдачи (либо только прием посылок на отправку) */
    public string|bool|null $takeonly = null;

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
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
    public function rules(): array
    {
        return [
            ['citypostcode', 'default'],
            ['citypostcode', 'integer', 'min' => 1],
            ['citypostcode', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            [['cityid', 'countryid', 'regionid'], 'default'],
            [['cityid', 'countryid', 'regionid'], 'integer', 'min' => 1],
            [['cityid', 'countryid', 'regionid'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['type', 'default'],
            ['type', 'in', 'range' => array_keys(Cdek::TYPES)],

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
     * @return Pvz[]
     */
    public function send(): array
    {
        $data = parent::send();

        return array_map(static fn(array $json): Pvz => new Pvz([
            'json' => $json
        ]), $data['pvz'] ?? []);
    }
}
