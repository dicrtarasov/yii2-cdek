<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 16:17:24
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * Дополнительная услуга в запросе стоимости.
 */
class ServiceParams extends CdekEntity
{
    /** Идентификатор номера дополнительной услуги (CdekApi::SERVICE_TYPES) */
    public string|int|null $id = null;

    /**
     * Параметр дополнительной услуги, если необходимо
     * Для дополнительных услуг 2, 24, 25 и 32 значение параметра является обязательным и должно быть передано
     * в запросе. Для услуги 2 - страховка в param необходимо передать сумму, с которой будет рассчитана страховка
     * (необходимо передавать в валюте взаиморасчетов). Услуга 30 доступна только для договора ИМ, поэтому в запросе
     * должны быть переданы значения authLogin и secure. Для услуг 24,25 и 32 в param передается значение количества.
     */
    public mixed $param = null;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['id', 'required'],
            ['id', 'integer', 'min' => 1],
            ['id', 'filter', 'filter' => 'intval'],

            ['param', 'default']
        ];
    }
}
