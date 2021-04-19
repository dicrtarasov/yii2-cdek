<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.04.21 15:25:42
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * Телефон.
 */
class Phone extends CdekEntity
{
    /** @var string */
    public $number;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['number', 'trim'],
            ['number', 'required']
        ];
    }
}
