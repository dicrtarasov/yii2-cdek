<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 07:48:14
 */

declare(strict_types = 1);
namespace dicr\cdek;

use dicr\cdek\request\CalcRequest;
use dicr\cdek\request\CityRequest;
use dicr\cdek\request\PvzRequest;
use dicr\cdek\request\RegionRequest;
use dicr\http\CachingClient;
use dicr\http\HttpCompressionBehavior;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

/**
 * Компонент API службы доставки СДЭК v1.5.
 *
 * @property-read Client $httpClient
 * @link https://confluence.cdek.ru/display/documentation
 */
class CdekApi extends Component implements Cdek
{
    /**
     * @var string логин магазина
     * При использовании тарифов для обычной доставки авторизация не обязательна и параметры authLogin и secure можно
     * не передавать.
     */
    public $login;

    /** @var string пароль магазина */
    public $password;

    /** @var ?array конфиг http-клиент */
    public $httpConfig;

    /**
     * @var array конфиг запроса стоимости
     * @link CalcRequest
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

    /** @var bool режим отладки */
    public $debug = false;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init() : void
    {
        parent::init();

        if (empty($this->login)) {
            throw new InvalidConfigException('login');
        }

        if (empty($this->password)) {
            throw new InvalidConfigException('password');
        }
    }

    /** @var Client */
    private $_httpClient;

    /**
     * HTTP-клиент.
     *
     * @return Client
     * @throws InvalidConfigException
     */
    public function getHttpClient() : Client
    {
        if ($this->_httpClient === null) {
            $this->_httpClient = Yii::createObject(array_merge([
                'class' => CachingClient::class,
                'baseUrl' => $this->debug ? self::URL_INTEGRATION_TEST : self::URL_INTEGRATION,
                'as compression' => [
                    'class' => HttpCompressionBehavior::class
                ]
            ], $this->httpConfig ?: []));
        }

        return $this->_httpClient;
    }

    /**
     * Запрос списка регионов.
     *
     * @param array $config
     * @return RegionRequest
     */
    public function regionRequest(array $config = []) : RegionRequest
    {
        return new RegionRequest($this, $config);
    }

    /**
     * Запрос списка городов.
     *
     * @param array $config
     * @return CityRequest
     */
    public function cityRequest(array $config = []) : CityRequest
    {
        return new CityRequest($this, $config);
    }

    /**
     * Запрос ПВЗ.
     *
     * @param array $config
     * @return PvzRequest
     */
    public function pvzRequest(array $config = []) : PvzRequest
    {
        return new PvzRequest($this, $config);
    }

    /**
     * Возвращает запрос рассчета доставки.
     *
     * @param array $config
     * @return CalcRequest
     */
    public function calcRequest(array $config = []) : CalcRequest
    {
        return new CalcRequest($this, array_merge($this->calcRequestConfig, $config));
    }
}
