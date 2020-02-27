<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 28.02.20 02:35:55
 */

declare(strict_types = 1);

namespace dicr\tests;

use PHPUnit\Framework\TestCase;
use Yii;

/**
 * Базовый класс для тестов.
 */
abstract class AbstractTest extends TestCase
{
    /**
     * Возвращает тестовое хранилище.
     *
     * @return \dicr\cdek\CdekApi
     * @throws \yii\base\InvalidConfigException
     */
    protected static function api()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::$app->get('api');
    }
}
