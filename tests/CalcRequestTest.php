<?php
namespace dicr\tests;

/**
 * CalcRequest Test.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class CalcRequestTest extends AbstractTest
{
    /**
     * Тестовый запрос
     */
    public function testCalcRequest()
    {
        // создаем тестовый запрос (конфиг из config.local.php)
        $request = self::api()->createCalcRequest(TEST_CALC_REQUEST);

        // отправляем тестовый запрос
        $result = $request->send();

        // проверяем ответ
        foreach (TEST_CALC_RESULT as $key => $val) {
            self::assertEquals($val, $result->{$key} ?? null);
        }
    }
}