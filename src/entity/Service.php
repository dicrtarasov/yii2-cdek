<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 16:17:16
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * Дополнительная услуга в ответе стоимости.
 */
class Service extends CdekEntity
{
    /** Идентификатор номера дополнительной услуги (CdekApi::SERVICE_TYPES) */
    public string|int|null $id = null;

    /** Заголовок услуги */
    public ?string $title = null;

    /** Стоимость услуги без учета НДС в рублях */
    public string|float|null $price = null;

    /** Процент для расчета дополнительной услуги */
    public string|float|null $rate = null;

    /**
     * @inheritDoc
     */
    public function rules(): array
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
