<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 00:02:45
 */

declare(strict_types = 1);

namespace dicr\tests;

use dicr\cdek\CdekApi;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Базовый класс для тестов.
 */
abstract class AbstractTest extends TestCase
{
    /**
     * Возвращает тестовое хранилище.
     *
     * @return CdekApi
     * @throws InvalidConfigException
     */
    protected static function api() : CdekApi
    {
        return Yii::$app->get('api');
    }
}
