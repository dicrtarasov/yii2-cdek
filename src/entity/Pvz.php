<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.04.21 15:25:40
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\Cdek;
use dicr\cdek\CdekEntity;
use dicr\json\EntityValidator;

use function array_keys;

/**
 * Пункт выдачи заказов (ПВЗ).
 *
 * @link https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-%D0%9F%D1%80%D0%BE%D1%82%D0%BE%D0%BA%D0%BE%D0%BB%D0%BE%D0%B1%D0%BC%D0%B5%D0%BD%D0%B0%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8(v1.5)-4.1.PVZ%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%D1%81%D0%BF%D0%B8%D1%81%D0%BA%D0%B0%D0%9F%D0%92%D0%97
 */
class Pvz extends CdekEntity implements Cdek
{
    /** @var string (10) код ПВЗ */
    public $code;

    /** @var string */
    public $status;

    /** @var ?int Почтовый индекс */
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

    /** @var int код города по базе СДЭК */
    public $cityCode;

    /** @var string (50) название города */
    public $city;

    /** @var string (100) Режим работы, строка вида «пн-пт 9-18, сб 9-16» */
    public $workTime;

    /** @var string (255) Адрес (улица, дом, офис) в указанном городе */
    public $address;

    /** @var string Полный адрес с указанием страны, региона, города, и т.д. */
    public $fullAddress;

    /** @var string Телефоны через запятую */
    public $phone;

    /** @var string (255) Примечание по ПВЗ */
    public $note;

    /** @var Phone[] */
    public $phoneDetailList;

    /** @var string Адрес электронной почты */
    public $email;

    /** @var ?string Описание местоположения */
    public $addressComment;

    /** @var ?float Координаты местоположения (долгота) в градусах */
    public $coordX;

    /** @var ?float Координаты местоположения (широта) в градусах */
    public $coordY;

    /** @var string Тип ПВЗ: Склад СДЭК или Почтомат партнера (TYPE_*) */
    public $type;

    /** @var string Принадлежность ПВЗ компании (OWNER_*) */
    public $ownerCode;

    /** @var bool Есть ли примерочная */
    public $isDressingRoom;

    /** @var bool Есть терминал оплаты */
    public $haveCashless;

    /** @var bool Есть приём наличных */
    public $haveCash;

    /** @var bool Разрешен наложенный платеж в ПВЗ */
    public $allowedCod;

    /** @var bool только прием */
    public $takeOnly;

    /** @var ?bool */
    public $isHandout;

    /** @var ?bool */
    public $fulfillment;

    /** @var string (50) Ближайшая станция/остановка транспорта */
    public $nearestStation;

    /** @var string (50) Ближайшая станция метро */
    public $metroStation;

    /** @var ?string сайт */
    public $site;

    /** @var Image[]|null Все фото офиса (кроме фото как доехать). */
    public $officeImage;

    /** @var WorkTimeY[] график работы на неделю. */
    public $workTimeYList;

    /** @var WorkTimeException[] Исключения в графике работы офиса */
    public $workTimeExceptions;

    /** @var ?WeightLimit лимиты веса */
    public $weightLimit;

    /** @var Dimension[]|null Перечень максимальных размеров ячеек постамата */
    public $dimensions;

    /**
     * @inheritDoc
     */
    public function rules() : array
    {
        return [
            ['code', 'required'],
            ['code', 'string', 'max' => 10],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],

            ['postalCode', 'required'],
            ['postalCode', 'integer', 'min' => 111111, 'max' => 999999],
            ['postalCode', 'filter', 'filter' => 'intval'],

            ['name', 'required'],
            ['name', 'string', 'max' => 50],

            ['countryCode', 'required'],
            ['countryCode', 'min' => 1, 'max' => 999999999],
            ['countryCode', 'filter', 'filter' => 'intval'],

            ['countryCodeIso', 'required'],
            ['countryCodeIso', 'string', 'length' => 2],

            ['countryName', 'required'],
            ['countryName', 'string', 'max' => 50],

            ['regionCode', 'required'],
            ['regionCode', 'integer', 'min' => 1, 'max' => 999999999],
            ['regionCode', 'filter', 'filter' => 'intval'],

            ['regionName', 'required'],
            ['regionName', 'string', 'max' => 50],

            ['cityCode', 'required'],
            ['cityCode', 'integer', 'min' => 1],
            ['cityCode', 'filter', 'filter' => 'intval'],

            ['city', 'required'],
            ['city', 'string', 'max' => 50],

            ['workTime', 'required'],
            ['workTime', 'string', 'max' => 100],

            ['address', 'required'],
            ['address', 'string', 'max' => 255],

            ['fullAddress', 'required'],
            ['fullAddress', 'string', 'max' => 255],

            ['phone', 'required'],
            ['phone', 'string', 'max' => 50],

            ['note', 'default'],
            ['note', 'string', 'max' => 255],

            ['phoneDetailList', 'required'],
            ['phoneDetailList', EntityValidator::class],

            ['email', 'required'],
            ['email', 'string', 'max' => 255],
            ['email', 'email'],

            ['addressComment', 'default'],
            ['addressComment', 'string', 'max' => 255],

            [['coordX', 'coordY'], 'default'],
            [['coordX', 'coordY'], 'number', 'min' => 0],
            [['coordX', 'coordY'], 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],

            ['type', 'required'],
            ['type', 'in', 'range' => array_keys(self::TYPES)],

            ['ownerCode', 'required'],
            ['ownerCode', 'in', 'range' => self::OWNERS],

            [['isDressingRoom', 'haveCashless', 'haveCash', 'allowedCod', 'takeOnly', 'isHandout', 'fulfillment'],
                'default', 'value' => false],
            [['isDressingRoom', 'haveCashless', 'haveCash', 'allowedCod', 'takeOnly', 'isHandout', 'fulfillment'],
                'boolean'],
            [['isDressingRoom', 'haveCashless', 'haveCash', 'allowedCod', 'takeOnly', 'isHandout', 'fulfillment'],
                'filter', 'filter' => 'boolval'],

            ['nearestStation', 'default'],
            ['nearestStation', 'string', 'max' => 50],

            ['metroStation', 'default'],
            ['metroStation', 'string', 'max' => 50],

            ['site', 'default'],
            ['site', 'string', 'max' => 255],
            ['site', 'url'],

            ['officeImage', 'default'],
            ['officeImage', EntityValidator::class],

            ['workTimeYList', 'default'],
            ['workTimeYList', EntityValidator::class],

            ['weightLimit', 'default'],
            ['weightLimit', EntityValidator::class],

            ['dimensions', 'default'],
            ['dimensions', EntityValidator::class],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeEntities() : array
    {
        return [
            'phoneDetailList' => [Phone::class],
            'officeImage' => [Image::class],
            'workTimeYList' => [WorkTimeY::class],
            'weightLimit' => WeightLimit::class,
            'dimensions' => [Dimension::class]
        ];
    }
}
