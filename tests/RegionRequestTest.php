<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 26.02.20 19:23:40
 */

declare(strict_types = 1);
namespace dicr\tests;

/**
 * RegionRequest Test.
 */
class RegionRequestTest extends AbstractTest
{
    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function test1()
    {
        $api = self::api();

        $request = $api->createRegionRequest();
        $regions = $request->send();

        self::assertIsArray($regions);
        self::assertNotEmpty($regions);

        //var_dump($regions);
    }
}
