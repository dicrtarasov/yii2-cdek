<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 19.04.21 14:31:44
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * Дополнительная услуга в запросе стоимости.
 */
class ServiceParams extends CdekEntity
{
    /** @var int Идентификатор номера дополнительной услуги (CdekApi::SERVICE_TYPES) */
    public $id;

    /**
     * @var ?mixed Параметр дополнительной услуги, если необходимо
     * Для дополнительных услуг 2, 24, 25 и 32 значение параметра является обязательным и должно быть передано
     * в запросе. Для услуги 2 - страховка в param необходимо передать сумму, с которой будет рассчитана страховка
     * (необходимо передавать в валюте взаиморасчетов). Услуга 30 доступна только для договора ИМ, поэтому в запросе
     * должны быть переданы значения authLogin и secure. Для услуг 24,25 и 32 в param передается значение количества.
     */
    public $param;

    /**
     * @inheritDoc
     */
    public function rules() : array
    {
        return [
            ['id', 'required'],
            ['id', 'integer', 'min' => 1],
            ['id', 'filter', 'filter' => 'intval'],

            ['param', 'default']
        ];
    }
}
