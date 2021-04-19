<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.04.21 17:13:44
 */

declare(strict_types = 1);
namespace dicr\cdek;

use dicr\cdek\request\CalcRequest;
use dicr\cdek\request\CityRequest;
use dicr\cdek\request\PvzRequest;
use dicr\cdek\request\RegionRequest;
use dicr\http\CachingClient;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;

use function array_merge;

use const CURLOPT_ENCODING;

/**
 * Компонент API службы доставки СДЭК v1.5.
 *
 * @property-read Client $httpClient
 *
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
        // senderCityId => код города отправителя по-умолчанию
        // senderCityPostCode => индекс отправителя по-умолчанию
        // tariffId => тариф для расчета по-умолчанию
        // tariffList => список тарифов для расчета по-умолчанию
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
    public function init(): void
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
    public function getHttpClient(): Client
    {
        if ($this->_httpClient === null) {
            $this->_httpClient = Yii::createObject(array_merge([
                'class' => CachingClient::class,
                'cacheMethods' => ['GET', 'POST'],
                'transport' => CurlTransport::class,
                'baseUrl' => $this->debug ? self::URL_INTEGRATION_TEST : self::URL_INTEGRATION,
                'requestConfig' => [
                    'format' => Client::FORMAT_JSON,
                    'headers' => [
                        'Accept' => 'application/json'
                    ],
                    'options' => [
                        CURLOPT_ENCODING => ''
                    ]
                ],
                'responseConfig' => [
                    'format' => Client::FORMAT_JSON
                ]
            ], $this->httpConfig ?: []));
        }

        return $this->_httpClient;
    }

    /**
     * Запрос.
     *
     * @param array $config
     * @return CdekRequest
     * @throws InvalidConfigException
     */
    public function request(array $config): CdekRequest
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject($config, [$this]);
    }

    /**
     * Запрос списка регионов.
     *
     * @param array $config
     * @return RegionRequest
     * @throws InvalidConfigException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function regionRequest(array $config = []): RegionRequest
    {
        return $this->request(array_merge($config, [
            'class' => RegionRequest::class
        ]));
    }

    /**
     * Запрос списка городов.
     *
     * @param array $config
     * @return CityRequest
     * @throws InvalidConfigException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function cityRequest(array $config = []): CityRequest
    {
        return $this->request(array_merge($config, [
            'class' => CityRequest::class
        ]));
    }

    /**
     * Запрос ПВЗ.
     *
     * @param array $config
     * @return PvzRequest
     * @throws InvalidConfigException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function pvzRequest(array $config = []): PvzRequest
    {
        return $this->request(array_merge($config, [
            'class' => PvzRequest::class
        ]));
    }

    /**
     * Возвращает запрос расчета доставки.
     *
     * @param array $config
     * @return CalcRequest
     * @throws InvalidConfigException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function calcRequest(array $config = []): CalcRequest
    {
        return $this->request(array_merge($config, [
            'class' => CalcRequest::class
        ]));
    }
}
