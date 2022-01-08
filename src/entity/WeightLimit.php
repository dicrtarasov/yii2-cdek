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
 * Ограничения по весу.
 */
class WeightLimit extends CdekEntity
{
    /** Минимальный вес (в кг.), принимаемый в ПВЗ (> WeightMin) */
    public string|float|null $weightMin = null;

    /** Максимальный вес (в кг.), принимаемый в ПВЗ (<=WeightMax) */
    public string|float|null $weightMax = null;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['weightMin', 'weightMax'], 'default'],
            [['weightMin', 'weightMax'], 'float', 'min' => 0.1],
            [['weightMin', 'weightMax'], 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],
        ];
    }
}
