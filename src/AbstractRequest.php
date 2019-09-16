<?php
namespace dicr\cdek;

use yii\base\Model;

/**
 * Базовый класс запросов.
 *
 * @property-read \dicr\cdek\CdekApi $api
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
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
        if (empty($api)) {
            throw new \InvalidArgumentException('api');
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
}