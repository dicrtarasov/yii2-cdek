<?php
namespace dicr\tests;

/**
 * PvzRequest Test.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class PvzRequestTest extends AbstractTest
{
    /**
     * Тест списка ПВЗ.
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