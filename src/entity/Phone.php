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
 * Телефон.
 */
class Phone extends AbstractEntity
{
    /** @var string */
    public $number;

    /**
     * @inheritDoc
     */
    public function rules() : array
    {
        return [
            ['number', 'trim'],
            ['number', 'required']
        ];
    }
}
