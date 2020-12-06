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
 * Информация о фотографии
 */
class Image extends AbstractEntity
{
    /** @var string url изображения */
    public $url;

    /**
     * @inheritDoc
     */
    public function rules() : array
    {
        return [
            ['url', 'trim'],
            ['url', 'required'],
            ['url', 'string', 'max' => 255],
            ['url', 'url']
        ];
    }
}
