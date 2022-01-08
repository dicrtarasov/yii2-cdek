<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 15:37:37
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
    /**
     * Конструктор.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected CdekApi $api,
        array $config = []
    ) {
        parent::__construct($config);
    }

    /**
     * Метод запроса.
     */
    abstract protected function method(): string;

    /**
     * URL запроса.
     */
    abstract protected function url(): array|string;

    /**
     * Данные запроса
     */
    protected function data(): array
    {
        return [];
    }

    /**
     * Отправка запроса.
     *
     * @throws Exception
     */
    public function send(): mixed
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
