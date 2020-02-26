<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 26.02.20 19:23:20
 */

declare(strict_types = 1);
namespace dicr\tests;

/**
 * PvzRequest Test.
 */
class PvzRequestTest extends AbstractTest
{
    /**
     * Тест списка ПВЗ.
     *
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function testPvzList()
    {
        $api = self::api();
        $request = $api->createPvzRequest();
        $pvzs = $request->send();

        static::assertIsArray($pvzs);
        static::assertNotEmpty($pvzs);

        //var_dump($pvzs);
    }
}
