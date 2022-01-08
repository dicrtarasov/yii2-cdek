<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 16:27:33
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * Описание тарифа.
 */
class Tariff extends CdekEntity
{
    /** Заданный приоритет (CdekApi::TARIF_TYPES) */
    public string|int|null $priority = null;

    /** Код тарифа */
    public string|int|null $id = null;

    /** Режим доставки - в документации ошибка - modeId в тарифе не учитывается */
    public string|int|null $modelId = null;

    /**
     * @inheritDoc
     */
    public function rules(): array
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
