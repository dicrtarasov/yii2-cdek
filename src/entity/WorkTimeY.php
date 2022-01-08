<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 16:20:03
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * График работы на неделю.
 */
class WorkTimeY extends CdekEntity
{
    /** Порядковый номер дня начиная с единицы. Понедельник = 1, воскресенье = 7 */
    public string|int|null $day = null;

    /** Период работы в эти дни. Если в этот день не работают, то не отображать. ("10:00/16:00") */
    public ?string $periods = null;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            ['day', 'required'],
            ['day', 'integer', 'min' => 1, 'max' => 7],
            ['day', 'filter', 'filter' => 'intval'],

            ['periods', 'trim'],
            ['periods', 'required'],
            ['periods', 'string', 'max' => 20]
        ];
    }
}
