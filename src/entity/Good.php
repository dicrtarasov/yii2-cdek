<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 05:46:27
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\AbstractEntity;

/**
 * Отправляемая посылка.
 */
class Good extends AbstractEntity
{
    /** @var float Вес упаковки (в килограммах) */
    public $weight;

    /** @var ?float Объём места, м³ (вместо width, height, length) */
    public $volume;

    /** @var ?int Длина упаковки (в сантиметрах) */
    public $length;

    /** @var ?int Ширина упаковки (в сантиметрах) */
    public $width;

    /** @var ?int Высота упаковки (в сантиметрах) */
    public $height;

    /**
     * @inheritDoc
     */
    public function rules() : array
    {
        return [
            ['weight', 'required'],
            ['weight', 'number', 'min' => 0.01],
            ['weight', 'filter', 'filter' => 'floatval'],

            ['volume', 'default'],
            ['volume', 'number', 'min' => 0.001],
            ['volume', 'filter', 'filter' => 'floatval'],

            [['length', 'width', 'height'], 'default'],
            [['length', 'width', 'height'], 'required', 'when' => function () : bool {
                return empty($this->volume);
            }],
            [['length', 'width', 'height'], 'integer', 'min' => 1],
            [['length', 'width', 'height'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
        ];
    }
}
