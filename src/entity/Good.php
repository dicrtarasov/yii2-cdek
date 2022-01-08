<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 16:27:33
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * Отправляемая посылка.
 */
class Good extends CdekEntity
{
    /** Вес упаковки (в килограммах) */
    public string|float|null $weight = null;

    /** Объём места, м³ (вместо width, height, length) */
    public string|float|null $volume = null;

    /** Длина упаковки (в сантиметрах) */
    public string|int|null $length = null;

    /** Ширина упаковки (в сантиметрах) */
    public string|int|null $width = null;

    /** Высота упаковки (в сантиметрах) */
    public string|int|null $height = null;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['weight', 'required'],
            ['weight', 'number', 'min' => 0.01],
            ['weight', 'filter', 'filter' => 'floatval'],

            ['volume', 'default'],
            ['volume', 'number', 'min' => 0.001],
            ['volume', 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],

            [['length', 'width', 'height'], 'default'],
            [['length', 'width', 'height'], 'required', 'when' => fn(): bool => empty($this->volume)],
            [['length', 'width', 'height'], 'integer', 'min' => 1],
            [['length', 'width', 'height'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
        ];
    }
}
