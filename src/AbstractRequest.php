<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 07.03.20 04:09:42
 */

declare(strict_types = 1);
namespace dicr\cdek;

use InvalidArgumentException;
use yii\base\Model;

/**
 * Базовый класс запросов.
 *
 * @property-read \dicr\cdek\CdekApi $api
 */
abstract class AbstractRequest extends Model
{
    /** @var \dicr\cdek\CdekApi */
    protected $_api;

    /**
     * Конструктор.
     *
     * @param CdekApi $api
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct(CdekApi $api, array $config = [])
    {
        if ($api === null) {
            throw new InvalidArgumentException('api');
        }

        $this->_api = $api;

        parent::__construct($config);
    }

    /**
     * Возвращает API.
     *
     * @return \dicr\cdek\CdekApi
     */
    public function getApi()
    {
        return $this->_api;
    }

    /**
     * Параметры запроса.
     *
     * @return array
     */
    abstract protected function getParams();

    /**
     * Отправка запроса.
     *
     * @return mixed
     * @throws \dicr\validate\ValidateException
     */
    abstract public function send();
}
