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
 * Описание тарифа.
 */
class Tariff extends CdekEntity
{
    /** @var int Заданный приоритет (CdekApi::TARIF_TYPES) */
    public $priority;

    /** @var int Код тарифа */
    public $id;

    /**
     * @var ?int Режим доставки
     * - в документации ошибка - modeId в тарифе не учитывается
     */
    public $modelId;

    /**
     * @inheritDoc
     */
    public function rules() : array
    {
        return [
            ['priority', 'required'],
            ['priority', 'integer', 'min' => 0],
            ['priority', 'filter', 'filter' => 'intval'],

            ['id', 'required'],
            ['id', 'integer', 'min' => 1],
            ['id', 'filter', 'filter' => 'intval'],

            ['modelId', 'default'],
            ['modelId', 'integer', 'min' => 1],
            ['modelId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true]
        ];
    }
}
