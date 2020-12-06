<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 07:47:25
 */

declare(strict_types = 1);
namespace dicr\cdek;

use dicr\validate\ValidateException;
use InvalidArgumentException;
use Yii;
use yii\base\Exception;
use yii\httpclient\Client;
use yii\httpclient\Request;

/**
 * Базовый класс запросов.
 */
abstract class AbstractRequest extends AbstractEntity
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
     * HTTP-запрос.
     *
     * @return Request
     */
    abstract protected function httpRequest() : Request;

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

        $request = $this->httpRequest();
        Yii::debug('Запрос: ' . $request->toString());

        $response = $request->send();
        Yii::debug('Ответ: ' . $response->toString());

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
