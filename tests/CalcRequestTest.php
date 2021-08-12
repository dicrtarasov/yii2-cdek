<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 12.08.21 22:58:27
 */

declare(strict_types = 1);

namespace dicr\tests;

use dicr\cdek\Cdek;
use dicr\helper\ArrayHelper;
use yii\base\Exception;
use yii\base\InvalidConfigException;

use function array_keys;

/**
 * CalcRequest Test.
 */
class CalcRequestTest extends AbstractTest
{
    /**
     * Тестовый запрос
     *
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function test() : void
    {
        $TEST_REQUEST = [
            'senderCityPostCode' => 614087, // Пермь
            'receiverCityId' => 44, // Москва,
            'tariffList' => [
                ['id' => Cdek::TARIF_POST_S_S],
                ['id' => Cdek::TARIF_POST_S_D],
                ['id' => Cdek::TARIF_ECOPOST_S_D],
                ['id' => Cdek::TARIF_ECOPOST_S_S]
            ],
            'goods' => [
                [
                    'weight' => 0.24,
                    'volume' => 0.1 * 0.05 * 0.2,
                    //'width' => 10, 'height' => 5, 'length' => 20
                ]
            ]
        ];

        $TEST_RESULT = [
            'price' => '252',
            'deliveryPeriodMin' => 2,
            'deliveryPeriodMax' => 3,
        ];

        $result = self::api()->calcRequest($TEST_REQUEST)->send();

        self::assertEquals($TEST_RESULT, ArrayHelper::filter((array)$result, array_keys($TEST_RESULT)));
    }
}
