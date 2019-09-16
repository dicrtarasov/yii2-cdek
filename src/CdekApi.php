<?php
namespace dicr\cdek;

use dicr\http\HttpCompressionBehavior;
use yii\caching\CacheInterface;
use yii\di\Instance;
use yii\httpclient\Client;
use yii\base\InvalidConfigException;

/**
 * Компонент API службы доставки СДЭК v1.5.
 *
 * @link https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129&src=contextnavpagetreemode
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class CdekApi extends Client
{
	/** @var string url интеграции */
	const URL_INTEGRATION = 'https://integration.cdek.ru';

	/** @var string url тестовой интеграции */
	const URL_TEST = 'https://integration.edu.cdek.ru';

	// Режимы доставки
	const DELIVERY_DOOR_DOOR = 1;
	const DELIVERY_DOOR_SKLAD = 2;
	const DELIVERY_SKLAD_DOOR = 3;
	const DELIVERY_SKLAD_SKLAD = 4;

	const DELIVERY_TYPES = [
		self::DELIVERY_DOOR_DOOR => 'дверь-дверь',
		self::DELIVERY_DOOR_SKLAD => 'дверь-склад',
		self::DELIVERY_SKLAD_DOOR => 'склад-дверь',
		self::DELIVERY_SKLAD_SKLAD => 'склад-склад'
	];

	// Дополнительные услуги
	const SERVICE_INSURANCE = 2;
	const SERVICE_DANGER = 7;
	const SERVICE_FETCHSENDER = 16;
	const SERVICE_DETCHRECEIVER = 17;
	const SERVICE_PACKING1 = 24;
	const SERVICE_PACKING2 = 25;
	const SERVICE_FITTING = 30;
	const SERVICE_TOHANDS = 31;
	const SERVICE_SCANNING = 32;
	const SERVICE_PARTIAL = 36;
	const SERVICE_INSPECTION = 37;

	const SERVICE_TYPES = [
		self::SERVICE_INSURANCE => 'страхование посылки',
		self::SERVICE_DANGER => 'достава опасных грузов',
		self::SERVICE_FETCHSENDER => 'забор в городе отправителя',
		self::SERVICE_FETCHSENDER => 'доставка по городу получателя',
		self::SERVICE_PACKING1 => 'коробка 310x215x280мм до 10кг',
		self::SERVICE_PACKING2 => 'коробка 430x310x280мм до 15кг',
		self::SERVICE_FITTING => 'примерка на дому в течении 10 минут',
		self::SERVICE_TOHANDS => 'лично в руки по документам',
		self::SERVICE_SCANNING => 'скан подписи получателя о доставке',
		self::SERVICE_PARTIAL => 'отказ получателя от части посылки',
		self::SERVICE_INSPECTION => 'проверка содержимого перед оплатой'
	];

	// Тарифы
	const TARIF_POST_S_S = 136;
	const TARIF_POST_S_D = 137;
	const TARIF_POST_D_S = 138;
	const TARIF_POST_D_D = 139;

	const TARIF_ECOPOST_S_D = 233;
	const TARIF_ECOPOST_S_S = 234;

	const TARIF_CDEK_S_S = 291;
	const TARIF_CDEK_D_D = 293;
	const TARIF_CDEK_S_D = 294;
	const TARIF_CDEK_D_S = 295;

	const TARIF_CHINAE_S_S = 243;
	const TARIF_CHINAE_D_D = 245;
	const TARIF_CHINAE_S_D = 246;
	const TARIF_CHINAE_D_S = 247;

	const TARIF_ECOLIGHT_D_D = 1;
	const TARIF_ECOLIGHT_S_S = 10;
	const TARIF_ECOLIGHT_S_D = 11;
	const TARIF_ECOLIGHT_D_S = 12;

	const TARIF_ECOHARD_S_S = 15;
	const TARIF_ECOHARD_S_D = 16;
	const TARIF_ECOHARD_D_S = 17;
	const TARIF_ECOHARD_D_D = 18;

	const TARIF_ECOEXP_S_S = 5;

	const TARIF_SUPEXP_9 = 57;
	const TARIF_SUPEXP_10 = 58;
	const TARIF_SUPEXP_12 = 59;
	const TARIF_SUPEXP_14 = 60;
	const TARIF_SUPEXP_16 = 61;
	const TARIF_SUPEXP_18 = 3;

	const TARIF_MAGEXP_S_S = 62;
	const TARIF_MAGSUP_S_S = 63;

	const TARIF_WRLD_CARGO = 8;
	const TARIF_WRLD_DOC = 7;

	const TARIF_TYPES = [
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
		self::TARIF_CHINAE_S_S => 'Китайский экспресс',
		self::TARIF_CHINAE_D_D => 'Китайский экспресс',
		self::TARIF_CHINAE_S_D => 'Китайский экспресс',
		self::TARIF_CHINAE_D_S => 'Китайский экспресс',
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

	/** @var \yii\caching\CacheInterface кэш для больших списков регионов, городов и ПВЗ */
	public $catalogCache = 'cache';

	/** @var int срок хранения списков каталога */
	public $catalogCacheDuration = 86400 * 7;

	/** @var \yii\caching\CacheInterface кэш для рассчетов стоимости услуг */
	public $calcCache = 'cache';

	/** @var int срок жизни кэша рассчетов доставки и сервисов */
	public $calcCacheDuration = 86400;

	/** @var callable function(\dicr\cdek\Pvz $pvz, \dicr\cdek\PvzRequest $request) : Pvz|null перезаписывает и фильтрует список ПВЗ */
	public $overridePvz;

	/** @var callable function(\dicr\cdek\CalcRequest, \dicr\cdek\CalcResult) : \dicr\cdek\CalcResult перезаписывает рассчет стоимости */
	public $overrideCost;

	/** @var string логин магазина */
	public $login;

	/** @var string пароль магазина */
	public $password;

	/** @var int код города отправителя по-умолчанию по базе СДЭК */
	public $defaultSenderCityId;

	/** @var int ндекс города отправителя по-умолчанию */
	public $defaultSenderCityPostCode;

	/** @var int код тарифа по-умолчанию */
	public $defaultTariffId;

	/**
	 * @var array[] список используемых тарифов по-умолчанию
	 * @see CalcRequest::tariffList
	 */
	public $defaultTariffList;

	/**
	 * @var array[] список сервисов по-умолчанию
	 * @see CalcRequest::services
	 */
	public $defaultServices;

	/** @var float вес посылки по-умолчанию */
	public $defaultWeight;

	/** @var float объем посылки по умолчанию */
	public $defaultVolume;

	/**
	 * {@inheritDoc}
	 * @see \yii\base\BaseObject::init()
	 */
	public function init()
	{
	    parent::init();

	    if (!empty($this->catalogCache)) {
	        $this->catalogCache = Instance::ensure($this->catalogCache, CacheInterface::class);
	    }

	    if (!empty($this->calcCache)) {
	        $this->calcCache = Instance::ensure($this->calcCache, CacheInterface::class);
	    }

	    if (!empty($this->defaultSenderCityId)) {
	        $this->defaultSenderCityId = (int)$this->defaultSenderCityId;
	        if ($this->defaultSenderCityId < 1) {
	            throw new InvalidConfigException('defaultSenderCityId');
	        }
	    }

	    if (!empty($this->defaultSenderCityPostCode)) {
	        $this->defaultSenderCityPostCode = (int)$this->defaultSenderCityPostCode;
	        if ($this->defaultSenderCityPostCode < 1) {
	            throw new InvalidConfigException('defaultSenderCityPostCode');
	        }
	    }

	    if (!empty($this->defaultTariffId)) {
	        $this->defaultTariffId = (int)$this->defaultTariffId;
	        if (!in_array($this->defaultTariffId, array_keys(CdekApi::TARIF_TYPES))) {
	            throw new InvalidConfigException('defaultTariffId');
	        }
	    }

	    if (!empty($this->defaultVolume)) {
	        $this->defaultVolume = (float)$this->defaultVolume;
	        if ($this->defaultVolume <= 0) {
	            throw new InvalidConfigException('defaultVolume');
	        }
	    }

	    if (!empty($this->defaultWeight)) {
	        $this->defaultWeight = (float)$this->defaultWeight;
	        if ($this->defaultWeight <= 0) {
	            throw new InvalidConfigException('defaultWeight');
	        }
	    }
	}

	/**
	 * {@inheritDoc}
	 * @see \yii\base\Component::behaviors()
	 */
	public function behaviors()
	{
		return [
			HttpCompressionBehavior::class
		];
	}

	/**
	 * Возвращает запрос ПВЗ.
	 *
	 * @param array $config
	 * @return \dicr\cdek\PvzRequest
	 */
	public function createPvzRequest(array $config = [])
	{
	    return new PvzRequest($this, $config);
	}

	/**
	 * Возвращает запрос списка регионов.
	 *
	 * @param array $config
	 * @return \dicr\cdek\RegionRequest
	 */
	public function createRegionRequest(array $config = [])
	{
	    return new RegionRequest($this, $config);
	}

	/**
	 * Возвращает запрос рассчета доставки.
	 *
	 * @param array $config
	 * @return \dicr\cdek\CalcRequest
	 */
	public function createCalcRequest(array $config = [])
	{
        return new CalcRequest($this, $config);
	}
}