<?php
namespace dicr\cdek;

use yii\base\BaseObject;

/**
 * Пункт выдачи заказов (ПВЗ).
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 180412
 */
class Pvz extends BaseObject
{
    /** @var string для отображения только складов СДЭК */
    const TYPE_PVZ = 'PVZ';

    /** @var string для отображения постоматов партнёра */
    const TYPE_POSTOMAT = 'POSTOMAT';

    /** @var string[] типы ПВЗ */
    const TYPES = [
        self::TYPE_PVZ => 'Склад СДЭК',
        self::TYPE_POSTOMAT => 'Постомат партнера'
    ];

    /** @var string (10) код ПВЗ */
	public $code;

	/** @var string Почтовый индекс */
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

	/** @var string Описание местоположения */
	public $addressComment;

	/** @var string Телефон */
	public $phone;

	/** @var string Адрес электронной почты */
	public $email;

	/** @var string */
	public $qqId;

	/** @var string (255) Примечание по ПВЗ */
	public $note;

	/** @var float Координаты местоположения (долгота) в градусах */
	public $coordX;

	/** @var float Координаты местоположения (широта) в градусах */
	public $coordY;

	/** @var string Тип ПВЗ: Склад СДЭК или Почтомат партнера */
	public $type;

	/** @var string Принадлежность ПВЗ компании: CDEK — ПВЗ принадлежит компании СДЭК, InPost — ПВЗ принадлежит компании InPost. */
	public $ownerCode;

	/** @var bool Есть ли примерочная */
	public $isDressingRoom;

	/** @var bool Есть терминал оплаты */
	public $haveCashless;

	/** @var bool Разрешен наложенный платеж в ПВЗ */
	public $allowedCod;

	/** @var string (255) Ближайшая станция/остановка транспорта */
	public $nearestStation;

	/** @var string (255) Ближайшая станция метро */
	public $metroStation;

	/** @var string (255) Сайт пвз на странице СДЭК */
	public $site;

	/** @var array[] [[number => 0, url => string]] массив url фотографий офиса (кроме фото как доехать) */
	public $officeImageList;

	/**
	 * @var array[] [[day => period]] график работы на неделю.
	 */
	public $workTimeYList;

	/** @var int[] лимиты веса [weightMin => ...., weightMax => ...] */
	public $weightLimit;

    /** @var string[][] [[number => string]] */
	public $phoneDetailList;
}