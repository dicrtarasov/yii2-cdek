<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 06.12.20 05:20:07
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\AbstractEntity;

/**
 * Исключения в графике работы офиса.
 */
class WorkTimeException extends AbstractEntity
{
    /** @var string Дата */
    public $date;

    /** @var string Период работы в указанную дату. Если в этот день не работают, то не отображается. */
    public $time;

    /** @var string Признак рабочего/нерабочего дня в указанную дату */
    public $isWorking;

    /**
     * @inheritDoc
     */
    public function rules() : array
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
