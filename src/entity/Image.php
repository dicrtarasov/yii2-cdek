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
 * Информация о фотографии
 */
class Image extends CdekEntity
{
    /** @var string url изображения */
    public $url;

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
