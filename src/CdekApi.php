<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 27.02.20 00:28:12
 */

declare(strict_types = 1);
namespace dicr\cdek;

use dicr\http\CachingClient;
use dicr\http\HttpCompressionBehavior;
use yii\base\InvalidConfigException;

/**
 * Компонент API службы доставки СДЭК v1.5.
 *
 * @link https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129&src=contextnavpagetreemode
 */
class CdekApi extends CachingClient
{
    /** @var string url интеграции */
    public const URL_INTEGRATION = 'https://integration.cdek.ru';

    /** @var string url тестовой интеграции */
    public const URL_TEST = 'https://integration.edu.cdek.ru';

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

    /** @var string URL API интеграции */
    public $baseUrl = self::URL_INTEGRATION;

    /** @var string логин магазина */
    public $login;

    /** @var string пароль магазина */
    public $password;

    /**
     * @var array конфиг запроса стоимости
     * @link \dicr\cdek\CalcRequest
     */
    public $calcRequestConfig = [
        // senderCityId => код города отпарвителя по-умолчанию
        // senderCityPostCode => индекс отправителя по-умолчанию
        // tariffId => тариф для рассчета по-умолчанию
        // tariffList => список тарифов для рассчета по-умолчанию
        // services => список включенных сервисов по-умолчанию
        // weight => вес посылки по-умолчанию
        // volume => объем посылки по-умолчанию
    ];

    /** @var \Closure function(\dicr\cdek\Pvz $pvz, \dicr\cdek\PvzRequest $request) : Pvz|null перезаписывает и фильтрует список ПВЗ */
    public $overridePvz;

    /** @var \Closure function(\dicr\cdek\CalcRequest, \dicr\cdek\CalcResult) : \dicr\cdek\CalcResult перезаписывает рассчет стоимости */
    public $overrideCost;

    /**
     * {@inheritDoc}
     * @see \yii\base\BaseObject::init()
     */
    public function init()
    {
        parent::init();

        // login
        if (empty($this->login)) {
            throw new InvalidConfigException('login');
        }

        // password
        if (empty($this->password)) {
            throw new InvalidConfigException('password');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return [
            'compress' => HttpCompressionBehavior::class
        ];
    }

    /**
     * Запрос списка регионов.
     *
     * @param array $config
     * @return \dicr\cdek\RegionRequest
     */
    public function createRegionRequest(array $config = null)
    {
        return new RegionRequest($this, $config ?: []);
    }

    /**
     * Запрос списка городов.
     *
     * @param array|null $config
     * @return \dicr\cdek\CityRequest
     */
    public function createCityRequest(array $config = null)
    {
        return new CityRequest($this, $config ?: []);
    }

    /**
     * Запрос ПВЗ.
     *
     * @param array $config
     * @return \dicr\cdek\PvzRequest
     */
    public function createPvzRequest(array $config = null)
    {
        return new PvzRequest($this, $config ?: []);
    }

    /**
     * Возвращает запрос рассчета доставки.
     *
     * @param array $config
     * @return \dicr\cdek\CalcRequest
     */
    public function createCalcRequest(array $config = [])
    {
        return new CalcRequest($this, array_merge($this->calcRequestConfig, $config));
    }
}
