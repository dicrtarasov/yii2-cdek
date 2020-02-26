<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 26.02.20 19:22:56
 */

namespace dicr\tests;

/**
 * CalcRequest Test.
 */
class CalcRequestTest extends AbstractTest
{
    /**
     * Тестовый запрос
     *
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
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
