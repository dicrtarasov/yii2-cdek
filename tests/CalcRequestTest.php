<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 28.02.20 03:59:08
 */

declare(strict_types = 1);

namespace dicr\tests;

use dicr\cdek\CdekApi;
use dicr\helper\ArrayHelper;
use function array_keys;

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
    public function test()
    {
        $TEST_REQUEST = [
            'senderCityPostCode' => 614087, // Пермь
            'receiverCityId' => 44, // Москва,
            'tariffList' => [
                ['id' => CdekApi::TARIF_POST_S_S],
                ['id' => CdekApi::TARIF_POST_S_D],
                ['id' => CdekApi::TARIF_ECOPOST_S_D],
                ['id' => CdekApi::TARIF_ECOPOST_S_S]
            ],
            'goods' => [
                ['weight' => 0.24, 'volume' => 0.001]
            ]
        ];

        $TEST_RESULT = [
            'price' => 210,
            'deliveryPeriodMin' => 2,
            'deliveryPeriodMax' => 4,
        ];

        $result = self::api()->createCalcRequest($TEST_REQUEST)->send();

        self::assertEquals($TEST_RESULT, ArrayHelper::filter((array)$result, array_keys($TEST_RESULT)));
    }
}
