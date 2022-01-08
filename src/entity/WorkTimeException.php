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
 * Исключения в графике работы офиса.
 */
class WorkTimeException extends CdekEntity
{
    /** Дата */
    public ?string $date = null;

    /** Период работы в указанную дату. Если в этот день не работают, то не отображается. */
    public ?string $time = null;

    /** Признак рабочего/нерабочего дня в указанную дату */
    public ?string $isWorking = null;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['date', 'required'],
            ['date', 'date', 'format' => 'php:Y-m-d'],

            ['time', 'required'],
            ['time', 'string', 'max' => 20],

            ['isWorking', 'required'],
            ['isWorking', 'string', 'max' => 4]
        ];
    }
}
