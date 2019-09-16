<?php
namespace dicr\tests;

use PHPUnit\Framework\TestCase;
use dicr\cdek\CdekApi;
use Yii;
use yii\caching\FileCache;
use yii\di\Container;

/**
 * Базовый класс для тестов.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class AbstractTest extends TestCase
{
    /**
     * {@inheritdoc}
     *
     * @return \yii\console\Application
     */
    public static function setUpBeforeClass() : void
    {
        require_once __DIR__ . '/config.local.php';

        new \yii\console\Application([
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
     */
    public static function tearDownAfterClass() : void
    {
        Yii::$app = null;
        Yii::$container = new Container();
    }

    /**
     * Возвращает тестовое хранилище.
     *
     * @return \dicr\cdek\CdekApi
     */
    protected static function api()
    {
        return \Yii::$app->get('api');
    }

    /**
     * Test store configured
     */
    public function testComponentExists()
    {
        $api = self::api();

        self::assertInstanceOf(CdekApi::class, $api);
    }
}