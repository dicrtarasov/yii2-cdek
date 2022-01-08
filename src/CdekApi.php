<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 15:51:23
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
     * логин магазина
     * При использовании тарифов для обычной доставки авторизация не обязательна и параметры authLogin и secure можно
     * не передавать.
     */
    public string $login;

    /** пароль магазина */
    public string $password;

    /** конфиг http-клиента */
    public array $httpConfig = [];

    /* конфиг запроса стоимости */
    public array $calcRequestConfig = [
        // senderCityId => код города отправителя по-умолчанию
        // senderCityPostCode => индекс отправителя по-умолчанию
        // tariffId => тариф для расчета по-умолчанию
        // tariffList => список тарифов для расчета по-умолчанию
        // services => список включенных сервисов по-умолчанию
        // weight => вес посылки по-умолчанию
        // volume => объем посылки по-умолчанию
    ];

    /** режим отладки */
    public bool $debug = false;

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

    private Client $_httpClient;

    /**
     * HTTP-клиент.
     *
     * @throws InvalidConfigException
     */
    public function getHttpClient(): Client
    {
        if (! isset($this->_httpClient)) {
            /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
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
            ], $this->httpConfig));
        }

        return $this->_httpClient;
    }

    /**
     * Запрос.
     *
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
     * @throws InvalidConfigException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function calcRequest(array $config = []): CalcRequest
    {
        return $this->request(array_merge($this->calcRequestConfig ?: [], $config, [
            'class' => CalcRequest::class
        ]));
    }
}
