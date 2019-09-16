<?php
namespace dicr\tests;

/**
 * RegionRequest Test.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class RegionRequestTest extends AbstractTest
{
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