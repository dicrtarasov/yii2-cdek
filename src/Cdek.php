<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 07:17:12
 */

declare(strict_types = 1);
namespace dicr\cdek;

/**
 * Константы СДЭК.
 */
interface Cdek
{
    /** @var string url интеграции */
    public const URL_INTEGRATION = 'https://integration.cdek.ru';

    /** @var string url тестовой интеграции */
    public const URL_INTEGRATION_TEST = 'https://integration.edu.cdek.ru';

    /** @var string url калькулятора */
    public const URL_CALC = 'http://api.cdek.ru';

    /** @var string url тестового калькулятора */
    public const URL_CALC_TEST = 'http://api.edu.cdek.ru';

    /** @var string тестовый аккаунт */
    public const LOGIN_TEST = 'EMscd6r9JnFiQ3bLoyjJY6eM78JrJceI';

    /** @var string тестовый Secure password */
    public const PASSWORD_TEST = 'PjLZkKBHEiLK3YsjtNrt3TGNG0ahs3kG';

    /** @var string локализация по-умолчанию */
    public const LANG_DEFAULT = 'rus';

    /** @var string код страны по-умолчанию */
    public const COUNTRY_CODE_DEFAULT = 'ru';

    /** @var int номер страницы по-умолчанию */
    public const PAGE_DEFAULT = 0;

    /** @var int размер страницы по-умолчанию */
    public const SIZE_DEFAULT = 1000;

    /** @var int достака курьером от дверей до дверей */
    public const DELIVERY_DOOR_DOOR = 1;

    /** @var int доставка от дверей до склада */
    public const DELIVERY_DOOR_SKLAD = 2;

    /** @var int доставка от склада до дверей */
    public const DELIVERY_SKLAD_DOOR = 3;

    /** @var int доставка от склада до склада */
    public const DELIVERY_SKLAD_SKLAD = 4;

    /** @var string[] режимы доставки */
    public const DELIVERY_TYPES = [
        self::DELIVERY_DOOR_DOOR => 'дверь-дверь',
        self::DELIVERY_DOOR_SKLAD => 'дверь-склад',
        self::DELIVERY_SKLAD_DOOR => 'склад-дверь',
        self::DELIVERY_SKLAD_SKLAD => 'склад-склад'
    ];

    /** @var int услуга страхования посылки */
    public const SERVICE_INSURANCE = 2;

    /** @var int доставка опасных грузов */
    public const SERVICE_DANGER = 7;

    /** @var int забор в городе отправителя */
    public const SERVICE_FETCH_SENDER = 16;

    /** @var int доставка по городу получателя */
    public const SERVICE_FETCH_RECEIVER = 17;

    /** @var int упаковка до 10 кг */
    public const SERVICE_PACKING1 = 24;

    /** @var int упаковка до 15 кг */
    public const SERVICE_PACKING2 = 25;

    /** @var int примерка на дому */
    public const SERVICE_FITTING = 30;

    /** @var int лично в руки */
    public const SERVICE_TOHANDS = 31;

    /** @var int скан подписи получателя */
    public const SERVICE_SCANNING = 32;

    /** @var int отказ получателя от части посылки */
    public const SERVICE_PARTIAL = 36;

    /** @var int проверка содержимого перед оплатой */
    public const SERVICE_INSPECTION = 37;

    /** @var string[] дополнительные услуги */
    public const SERVICE_TYPES = [
        self::SERVICE_INSURANCE => 'страхование посылки',
        self::SERVICE_DANGER => 'достава опасных грузов',
        self::SERVICE_FETCH_SENDER => 'забор в городе отправителя',
        self::SERVICE_FETCH_RECEIVER => 'доставка по городу получателя',
        self::SERVICE_PACKING1 => 'коробка 310x215x280мм до 10кг',
        self::SERVICE_PACKING2 => 'коробка 430x310x280мм до 15кг',
        self::SERVICE_FITTING => 'примерка на дому в течении 10 минут',
        self::SERVICE_TOHANDS => 'лично в руки по документам',
        self::SERVICE_SCANNING => 'скан подписи получателя о доставке',
        self::SERVICE_PARTIAL => 'отказ получателя от части посылки',
        self::SERVICE_INSPECTION => 'проверка содержимого перед оплатой'
    ];

    /** @var int посылка склад-склад */
    public const TARIF_POST_S_S = 136;

    /** @var int посылка склад дверь */
    public const TARIF_POST_S_D = 137;

    /** @var int посылка дверь-склад */
    public const TARIF_POST_D_S = 138;

    /** @var int посылка дверь-дверь */
    public const TARIF_POST_D_D = 139;

    /** @var int экономная посылка склад-дверь */
    public const TARIF_ECOPOST_S_D = 233;

    /** @var int экономная посылка склад-склад */
    public const TARIF_ECOPOST_S_S = 234;

    /** @var int СДЭК-экспресс склад-склад */
    public const TARIF_CDEK_S_S = 291;

    /** @var int СДЭК-экспресс дверь-дверь */
    public const TARIF_CDEK_D_D = 293;

    /** @var int СДЭК-экспресс склад-дверь */
    public const TARIF_CDEK_S_D = 294;

    /** @var int СДЭК-экспресс дверь-склад */
    public const TARIF_CDEK_D_S = 295;

    /** @var int Китайский экспресс склад-склад */
    public const TARIF_CHINAE_S_S = 243;

    /** @var int Китайский экспресс дверь-дверь */
    public const TARIF_CHINAE_D_D = 245;

    /** @var int Китайский экспресс склад-дверь */
    public const TARIF_CHINAE_S_D = 246;

    /** @var int Китайский экспресс дверь-склад */
    public const TARIF_CHINAE_D_S = 247;

    /** @var int Экспресс лайт дверь-дверь */
    public const TARIF_ECOLIGHT_D_D = 1;

    /** @var int Экспресс лайт склад-склад */
    public const TARIF_ECOLIGHT_S_S = 10;

    /** @var int Экспресс лайт склад-дверь */
    public const TARIF_ECOLIGHT_S_D = 11;

    /** @var int Экспресс лайт дверь-склад */
    public const TARIF_ECOLIGHT_D_S = 12;

    /** @var int Экспресс тяжеловесы склад-склад */
    public const TARIF_ECOHARD_S_S = 15;

    /** @var int Экспресс тяжеловесы склад-дверь */
    public const TARIF_ECOHARD_S_D = 16;

    /** @var int Экспресс тяеловесы дверь-склад */
    public const TARIF_ECOHARD_D_S = 17;

    /** @var int Экспресс тяжеловесы дверь-дверь */
    public const TARIF_ECOHARD_D_D = 18;

    /** @var int  Экономичный экспресс склад-склад */
    public const TARIF_ECOEXP_S_S = 5;

    /** @var int */
    public const TARIF_SUPEXP_9 = 57;

    /** @var int */
    public const TARIF_SUPEXP_10 = 58;

    /** @var int */
    public const TARIF_SUPEXP_12 = 59;

    /** @var int */
    public const TARIF_SUPEXP_14 = 60;

    /** @var int */
    public const TARIF_SUPEXP_16 = 61;

    /** @var int */
    public const TARIF_SUPEXP_18 = 3;

    /** @var int */
    public const TARIF_MAGEXP_S_S = 62;

    /** @var int */
    public const TARIF_MAGSUP_S_S = 63;

    /** @var int */
    public const TARIF_WRLD_CARGO = 8;

    /** @var int */
    public const TARIF_WRLD_DOC = 7;

    /** @var string[] тарифы */
    public const TARIF_TYPES = [
        self::TARIF_POST_S_S => 'Посылка склад-склад',
        self::TARIF_POST_S_D => 'Посылка склад-дверь',
        self::TARIF_POST_D_S => 'Посылка дверь-склад',
        self::TARIF_POST_D_D => 'Посылка дверь-дверь',
        self::TARIF_ECOPOST_S_D => 'Экономичная посылка склад-дверь',
        self::TARIF_ECOPOST_S_S => 'Экономичная посылка склад-склад',
        self::TARIF_CDEK_S_S => 'CDEK Express склад-склад',
        self::TARIF_CDEK_D_D => 'CDEK Express дверь-дверь',
        self::TARIF_CDEK_S_D => 'CDEK Express склад-дверь',
        self::TARIF_CDEK_D_S => 'CDEK Express дверь-склад',
        self::TARIF_CHINAE_S_S => 'Китайский экспресс склад-склад',
        self::TARIF_CHINAE_D_D => 'Китайский экспресс дверь-дверь',
        self::TARIF_CHINAE_S_D => 'Китайский экспресс склад-дверь',
        self::TARIF_CHINAE_D_S => 'Китайский экспресс дверь-склад',
        self::TARIF_ECOLIGHT_D_D => 'Экспресс лайт дверь-дверь',
        self::TARIF_ECOLIGHT_S_S => 'Экспресс лайт склад-склад',
        self::TARIF_ECOLIGHT_S_D => 'Экспресс лайт склад-дверь',
        self::TARIF_ECOLIGHT_D_S => 'Экспресс лайт дверь-склад',
        self::TARIF_ECOHARD_S_S => 'Экспресс тяжеловесы склад-склад',
        self::TARIF_ECOHARD_S_D => 'Экспресс тяжеловесы склад-дверь',
        self::TARIF_ECOHARD_D_S => 'Экспресс тяжеловесы дверь-склад',
        self::TARIF_ECOHARD_D_D => 'Экспресс тяжеловесы дверь-дверь',
        self::TARIF_ECOEXP_S_S => 'Экономичный экспресс склад-склад',
        self::TARIF_SUPEXP_9 => 'Супер-экспресс до 9',
        self::TARIF_SUPEXP_10 => 'Супер-экспресс до 10',
        self::TARIF_SUPEXP_12 => 'Супер-экспресс до 12',
        self::TARIF_SUPEXP_14 => 'Супер-экспресс до 14',
        self::TARIF_SUPEXP_16 => 'Супер-экспресс до 16',
        self::TARIF_SUPEXP_18 => 'Супер-экспресс до 18',
        self::TARIF_MAGEXP_S_S => 'Магистральный экспресс склад-склад',
        self::TARIF_MAGSUP_S_S => 'Магистральный супер-экспресс склад-склад',
        self::TARIF_WRLD_CARGO => 'Международный экспресс грузы',
        self::TARIF_WRLD_DOC => 'Международный экспресс документы'
    ];

    /** @var string для отображения только складов СДЭК */
    public const TYPE_PVZ = 'PVZ';

    /** @var string для отображения постоматов партнёра */
    public const TYPE_POSTOMAT = 'POSTOMAT';

    /** @var string[] типы ПВЗ */
    public const TYPES = [
        self::TYPE_PVZ => 'Склад СДЭК',
        self::TYPE_POSTOMAT => 'Постомат партнера'
    ];

    /** @var string ПВЗ принадлежит компании СДЭК */
    public const OWNER_CDEK = 'cdek';

    /** @var string ПВЗ принадлежит компании InPost */
    public const OWNER_INPOST = 'inpost';

    /** @var string[] */
    public const OWNERS = [
        self::OWNER_CDEK, self::OWNER_INPOST
    ];

    /** @var ?string */
    public const STATUS_ACTIVE = 'ACTIVE';
}
