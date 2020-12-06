<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 02:12:13
 */

declare(strict_types = 1);
namespace dicr\tests;

use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * RegionRequest Test.
 */
class RegionRequestTest extends AbstractTest
{
    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function test() : void
    {
        $result = self::api()->regionRequest()->send();
        self::assertIsArray($result);
        self::assertNotEmpty($result);
    }
}
