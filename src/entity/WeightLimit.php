<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 05:20:07
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\AbstractEntity;

/**
 * Ограничения по весу.
 */
class WeightLimit extends AbstractEntity
{
    /** @var ?float Минимальный вес (в кг.), принимаемый в ПВЗ (> WeightMin) */
    public $weightMin;

    /** @var ?float Максимальный вес (в кг.), принимаемый в ПВЗ (<=WeightMax) */
    public $weightMax;

    /**
     * @inheritDoc
     */
    public function rules() : array
    {
        return [
            [['weightMin', 'weightMax'], 'default'],
            [['weightMin', 'weightMax'], 'float', 'min' => 0.1],
            [['weightMin', 'weightMax'], 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],
        ];
    }
}
