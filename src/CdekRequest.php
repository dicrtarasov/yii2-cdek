<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.04.21 14:31:44
 */

declare(strict_types = 1);
namespace dicr\cdek;

use dicr\helper\Log;
use dicr\validate\ValidateException;
use InvalidArgumentException;
use yii\base\Exception;
use yii\httpclient\Client;

/**
 * Базовый класс запросов.
 */
abstract class CdekRequest extends CdekEntity
{
    /** @var CdekApi */
    protected $api;

    /**
     * Конструктор.
     *
     * @param CdekApi $api
     * @param array $config
     * @throws InvalidArgumentException
     */
    public function __construct(CdekApi $api, array $config = [])
    {
        $this->api = $api;

        parent::__construct($config);
    }

    /**
     * Метод запроса.
     *
     * @return string
     */
    abstract protected function method(): string;

    /**
     * URL запроса.
     *
     * @return string|array
     */
    abstract protected function url();

    /**
     * Данные запроса
     *
     * @return ?array
     */
    protected function data(): array
    {
        return [];
    }

    /**
     * Отправка запроса.
     *
     * @return array данные ответа (переопределяется)
     * @throws Exception
     * @noinspection PhpMissingReturnTypeInspection, ReturnTypeCanBeDeclaredInspection
     */
    public function send()
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        $request = $this->api->httpClient->createRequest()
            ->setMethod($this->method())
            ->setUrl($this->url())
            ->setData($this->data());

        Log::debug('Запрос: ' . $request->toString());

        $response = $request->send();
        Log::debug('Ответ: ' . $response->toString());

        if (! $response->isOk) {
            throw new Exception('HTTP-error: ' . $response->statusCode);
        }

        $response->format = Client::FORMAT_JSON;
        if (! empty($response->data['error'])) {
            throw new Exception(
                'Ошибка запроса: ' . ($response->data['error']['text'] ?? $response->data['error'][0]['text'])
            );
        }

        return $response->data;
    }
}
