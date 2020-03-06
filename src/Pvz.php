<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.03.20 04:11:16
 */

declare(strict_types = 1);
namespace dicr\cdek;

use yii\base\BaseObject;

/**
 * Пункт выдачи заказов (ПВЗ).
 *
 * @link https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-%D0%9F%D1%80%D0%BE%D1%82%D0%BE%D0%BA%D0%BE%D0%BB%D0%BE%D0%B1%D0%BC%D0%B5%D0%BD%D0%B0%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8(v1.5)-4.1.PVZ%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%D1%81%D0%BF%D0%B8%D1%81%D0%BA%D0%B0%D0%9F%D0%92%D0%97
 */
class Pvz extends BaseObject
{
    /** @var string для отображения только складов СДЭК */
    public const TYPE_PVZ = 'PVZ';

    /** @var string для отображения постоматов партнёра */
    public const TYPE_POSTOMAT = 'POSTOMAT';

    /** @var string[] типы ПВЗ */
    public const TYPES = [
        self::TYPE_PVZ => 'Склад СДЭК',
        self::TYPE_POSTOMAT => 'Постомат партнера'
    ];

    /** @var string (10) код ПВЗ */
    public $code;

    /** @var int|null Почтовый индекс */
    public $postalCode;

    /** @var string (50) название */
    public $name;

    /** @var int Код страны */
    public $countryCode;

    /** @var string (2) Код страны в формате ISO_3166-1_alpha-2 */
    public $countryCodeIso;

    /** @var string Название страны */
    public $countryName;

    /** @var int Код региона */
    public $regionCode;

    /** @var string Название региона */
    public $regionName;

    /** @var int|null код города по базе СДЭК */
    public $cityCode;

    /** @var string (50) название города */
    public $city;

    /** @var string (100) Режим работы, строка вида «пн-пт 9-18, сб 9-16» */
    public $workTime;

    /** @var string (255) Адрес (улица, дом, офис) в указанном городе */
    public $address;

    /** @var string Полный адрес с указанием страны, региона, города, и т.д. */
    public $fullAddress;

    /** @var string Телефон */
    public $phone;

    /** @var string (255) Примечание по ПВЗ */
    public $note;

    /** @var float|null Координаты местоположения (долгота) в градусах */
    public $coordX;

    /** @var float|null Координаты местоположения (широта) в градусах */
    public $coordY;

    /** @var string Тип ПВЗ: Склад СДЭК или Почтомат партнера */
    public $type;

    /** @var string Принадлежность ПВЗ компании: CDEK — ПВЗ принадлежит компании СДЭК, InPost — ПВЗ принадлежит компании InPost. */
    public $ownerCode;

    /** @var bool Есть ли примерочная */
    public $isDressingRoom;

    /** @var bool Есть терминал оплаты */
    public $haveCashless;

    /** @var bool|null */
    public $haveCash;

    /** @var bool Разрешен наложенный платеж в ПВЗ */
    public $allowedCod;

    /** @var bool только прием */
    public $takeOnly;

    /** @var string (255) Ближайшая станция/остановка транспорта */
    public $nearestStation;

    /** @var string (255) Ближайшая станция метро */
    public $metroStation;

    /** @var string|null сайт */
    public $site;

    /** @var string|null Адрес электронной почты */
    public $email;

    /** @var string Описание местоположения */
    public $addressComment;

    /** @var int[] лимиты веса [weightMin => ...., weightMax => ...] */
    public $weightLimit;

    /** @var array[] [[number => 0, url => string]] массив url фотографий офиса (кроме фото как доехать) */
    public $officeImageList;

    /** @var array[] [[day => period]] график работы на неделю. */
    public $workTimeYList;

    /** @var array|null [[number => string]] */
    public $phoneDetailList;

    /**
     * Конструктор.
     *
     * @param array $json
     */
    public function __construct(array $json = [])
    {
        if (! empty($json)) {
            $this->configure($json);
        }

        parent::__construct();
    }

    /**
     * Конфигурация из данных JSON.
     *
     * @param array $json
     * @return $this
     */
    public function configure(array $json)
    {
        $this->code = (string)$json['code'];
        $this->postalCode = ! empty($json['postalCode']) ? (int)$json['postalCode'] : null;
        $this->name = (string)$json['name'];
        $this->countryCode = (int)$json['countryCode'];
        $this->countryCodeIso = (string)$json['countryCodeIso'];
        $this->countryName = (string)$json['countryName'];
        $this->regionCode = (int)$json['regionCode'];
        $this->regionName = (string)$json['regionName'];
        $this->cityCode = ! empty($json['cityCode']) ? (int)$json['cityCode'] : null;
        $this->city = (string)$json['city'];
        $this->workTime = (string)$json['workTime'];
        $this->address = (string)$json['address'];
        $this->fullAddress = (string)$json['fullAddress'];
        $this->phone = (string)$json['phone'];
        $this->note = (string)$json['note'];
        $this->coordX = ! empty($json['coordX']) ? (float)$json['coordX'] : null;
        $this->coordY = ! empty($json['coordY']) ? (float)$json['coordY'] : null;
        $this->type = (string)$json['type'];
        $this->ownerCode = (string)$json['ownerCode'];
        $this->isDressingRoom = (bool)$json['isDressingRoom'];
        $this->haveCashless = (bool)$json['haveCashless'];
        $this->haveCash = isset($json['haveCash']) && $json['haveClash'] !== '' ? (bool)$json['haveCash'] : null;
        $this->allowedCod = (bool)$json['allowedCod'];
        $this->takeOnly = (bool)$json['takeOnly'];
        $this->nearestStation = (string)$json['nearestStation'];
        $this->metroStation = (string)$json['metroStation'];
        $this->site = ! empty($json['site']) ? (string)$json['site'] : null;
        $this->email = ! empty($json['email']) ? (string)$json['email'] : null;
        $this->addressComment = (string)$json['addressComment'];

        $this->weightLimit = ! empty($json['weightLimit']) ? [
            'weightMin' => (int)$json['weightLimit']['weightMin'],
            'weightMax' => (int)$json['weightLimit']['weightMax']
        ] : [];

        $this->officeImageList = array_map(static function(array $item) {
            return [
                'number' => isset($item['number']) ? (int)$item['number'] : null,
                'url' => (string)$item['url']
            ];
        }, (array)($json['officeImageList'] ?? []));

        $this->workTimeYList = array_map(static function(array $item) {
            return [
                'day' => (int)$item['day'],
                'periods' => (string)$item['periods']
            ];
        }, (array)$json['workTimeYList']);

        $this->phoneDetailList = array_map(static function(array $item) {
            return [
                'number' => (string)$item['number']
            ];
        }, (array)($json['phoneDetailList'] ?? []));

        return $this;
    }
}
