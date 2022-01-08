<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 15:59:56
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * Телефон.
 */
class Phone extends CdekEntity
{
    public ?string $number = null;

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
