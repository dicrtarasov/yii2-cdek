<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 16:11:32
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\Cdek;
use dicr\cdek\CdekEntity;
use dicr\json\EntityValidator;

use function array_keys;
use function is_array;

/**
 * Пункт выдачи заказов (ПВЗ).
 *
 * @link https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-%D0%9F%D1%80%D0%BE%D1%82%D0%BE%D0%BA%D0%BE%D0%BB%D0%BE%D0%B1%D0%BC%D0%B5%D0%BD%D0%B0%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8(v1.5)-4.1.PVZ%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%D1%81%D0%BF%D0%B8%D1%81%D0%BA%D0%B0%D0%9F%D0%92%D0%97
 */
class Pvz extends CdekEntity implements Cdek
{
    /** код ПВЗ */
    public ?string $code = null;

    public ?string $status = null;

    /** Почтовый индекс */
    public string|int|null $postalCode = null;

    /** название */
    public ?string $name = null;

    /** Код страны */
    public ?string $countryCode = null;

    /** Код страны в формате ISO_3166-1_alpha-2 */
    public ?string $countryCodeIso = null;

    /** Название страны */
    public ?string $countryName = null;

    /** Код региона */
    public string|int|null $regionCode = null;

    /** Название региона */
    public ?string $regionName = null;

    /** код города по базе СДЭК */
    public string|int|null $cityCode = null;

    /** название города */
    public ?string $city = null;

    /** Режим работы, строка вида «пн-пт 9-18, сб 9-16» */
    public ?string $workTime = null;

    /** Адрес (улица, дом, офис) в указанном городе */
    public ?string $address = null;

    /** Полный адрес с указанием страны, региона, города, и т.д. */
    public ?string $fullAddress = null;

    /** Телефоны через запятую */
    public string|array|null $phone = null;

    /** Примечание по ПВЗ */
    public ?string $note = null;

    /** @var Phone[]|null */
    public ?array $phoneDetailList = null;

    /** Адрес электронной почты */
    public ?string $email = null;

    /** Описание местоположения */
    public ?string $addressComment = null;

    /** Координаты местоположения (долгота) в градусах */
    public string|float|null $coordX = null;

    /** Координаты местоположения (широта) в градусах */
    public string|float|null $coordY = null;

    /** Тип ПВЗ: Склад СДЭК или Почтомат партнера (TYPE_*) */
    public ?string $type = null;

    /** Принадлежность ПВЗ компании (OWNER_*) */
    public ?string $ownerCode = null;

    /** Есть ли примерочная */
    public string|bool|null $isDressingRoom = null;

    /** Есть терминал оплаты */
    public string|bool|null $haveCashless = null;

    /** Есть приём наличных */
    public string|bool|null $haveCash = null;

    /** Разрешен наложенный платеж в ПВЗ */
    public string|bool|null $allowedCod = null;

    /** только прием */
    public string|bool|null $takeOnly = null;

    public string|bool|null $isHandout = null;

    public string|bool|null $fulfillment = null;

    /** Ближайшая станция/остановка транспорта */
    public ?string $nearestStation = null;

    /** Ближайшая станция метро */
    public ?string $metroStation = null;

    /** сайт */
    public ?string $site = null;

    /** @var Image[]|null Все фото офиса (кроме фото как доехать). */
    public ?array $officeImage = null;

    /** @var WorkTimeY[]|null график работы на неделю. */
    public ?array $workTimeYList = null;

    /** @var WorkTimeException[]|null Исключения в графике работы офиса */
    public ?array $workTimeExceptions = null;

    /** лимиты веса */
    public ?WeightLimit $weightLimit = null;

    /** @var Dimension[]|null Перечень максимальных размеров ячеек постамата */
    public ?array $dimensions = null;

    /**
     * @inheritDoc
     */
    public function rules(): array
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

            ['phone', function (string $attribute) {
                if (is_array($this->{$attribute})) {
                    $this->{$attribute} = implode(',', $this->{$attribute});
                }
            }],
            ['phone', 'trim'],
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
    public function attributeEntities(): array
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
