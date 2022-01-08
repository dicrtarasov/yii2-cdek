<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 15:59:41
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * Информация о фотографии
 */
class Image extends CdekEntity
{
    /** url изображения */
    public ?string $url = null;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['url', 'trim'],
            ['url', 'required'],
            ['url', 'string', 'max' => 255],
            ['url', 'url']
        ];
    }
}
