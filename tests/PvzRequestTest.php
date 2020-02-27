<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 28.02.20 02:33:47
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
    public function test()
    {
        $result = self::api()->createPvzRequest()->send();
        self::assertIsArray($result);
        self::assertNotEmpty($result);
    }
}
