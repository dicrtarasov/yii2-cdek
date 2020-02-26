<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 26.02.20 19:22:31
 */

namespace dicr\tests;

use dicr\cdek\CdekApi;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\caching\FileCache;
use yii\console\Application;
use yii\di\Container;

/**
 * Базовый класс для тестов.
 */
class AbstractTest extends TestCase
{
    /**
     * {@inheritdoc}
     *
     * @return \yii\console\Application
     * @throws \yii\base\InvalidConfigException
     */
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/config.local.php';

        new Application([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => VENDOR,
            'components' => [
                'cache' => FileCache::class,
                'api' => array_merge([
                    'class' => CdekApi::class,
                    'catalogCache' => 'cache',
                    'calcCache' => 'cache',
                ], TEST_API_CONFIG)
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @noinspection DisallowWritingIntoStaticPropertiesInspection
     */
    public static function tearDownAfterClass(): void
    {
        Yii::$app = null;
        Yii::$container = new Container();
    }

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

    /**
     * Test store configured
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function testComponentExists()
    {
        $api = self::api();

        self::assertInstanceOf(CdekApi::class, $api);
    }
}
