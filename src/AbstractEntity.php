<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 05:20:07
 */

declare(strict_types = 1);
namespace dicr\cdek;

use dicr\json\JsonEntity;

/**
 * Class AbstractEntity
 */
abstract class AbstractEntity extends JsonEntity
{
    /**
     * @inheritDoc
     */
    public function attributeFields() : array
    {
        // отключаем преобразования по-умолчанию
        return [];
    }
}
