<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 28.02.20 02:33:32
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
    public function test()
    {
        $result = self::api()->createRegionRequest()->send();
        self::assertIsArray($result);
        self::assertNotEmpty($result);
    }
}
