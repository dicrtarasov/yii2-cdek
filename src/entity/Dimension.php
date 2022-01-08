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
 * Максимальных размеров ячеек постамата
 */
class Dimension extends CdekEntity
{
    /** Ширина (см) */
    public string|float|null $width = null;

    /** Высота (см) */
    public string|float|null $height = null;

    /** Глубина (см) */
    public string|float|null $depth = null;

    /** Наличие зоны фулфилмента */
    public ?bool $fulfillment = null;

    /** Является пунктом выдачи */
    public ?bool $isHandout = null;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [['width', 'height', 'depth'], 'default'],
            [['width', 'height', 'depth'], 'float', 'min' => 0.1],
            [['width', 'height', 'depth'], 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],

            [['fulfillment', 'isHandout'], 'default'],
            [['fulfillment', 'isHandout'], 'boolean'],
            [['fulfillment', 'isHandout'], 'filter', 'filter' => 'boolval', 'skipOnEmpty' => true],
        ];
    }
}
