<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.04.21 14:31:44
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * Максимальных размеров ячеек постамата
 */
class Dimension extends CdekEntity
{
    /** @var ?float Ширина (см) */
    public $width;

    /** @var ?float Высота (см) */
    public $height;

    /** @var ?float Глубина (см) */
    public $depth;

    /** @var ?bool Наличие зоны фулфилмента */
    public $fulfillment;

    /** @var ?bool Является пунктом выдачи */
    public $isHandout;

    /**
     * @inheritDoc
     */
    public function rules() : array
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
