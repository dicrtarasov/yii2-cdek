<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 06:11:57
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\AbstractEntity;

/**
 * Дополнительная услуга в ответе стоимости.
 */
class Service extends AbstractEntity
{
    /** @var int Идентификатор номера дополнительной услуги (CdekApi::SERVICE_TYPES) */
    public $id;

    /** @var string Заголовок услуги */
    public $title;

    /** @var float Стоимость услуги без учета НДС в рублях */
    public $price;

    /** @var ?float Процент для расчета дополнительной услуги */
    public $rate;

    /**
     * @inheritDoc
     */
    public function rules() : array
    {
        return [
            ['id', 'required'],
            ['id', 'integer', 'min' => 1],
            ['id', 'filter', 'filter' => 'intval'],

            ['title', 'trim'],
            ['title', 'required'],

            ['price', 'required'],
            ['price', 'number', 'min' => 0.01],
            ['price', 'filter', 'filter' => 'floatval'],

            ['rate', 'default'],
            ['rate', 'number', 'min' => 0.001],
            ['rate', 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true]
        ];
    }
}
