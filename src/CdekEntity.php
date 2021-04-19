<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.04.21 14:31:44
 */

declare(strict_types = 1);
namespace dicr\cdek;

use dicr\json\JsonEntity;

/**
 * Class Entity
 */
abstract class CdekEntity extends JsonEntity
{
    /**
     * @inheritDoc
     */
    public function attributeFields(): array
    {
        // отключаем преобразования по-умолчанию
        return [];
    }
}
