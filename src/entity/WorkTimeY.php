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
 * График работы на неделю.
 */
class WorkTimeY extends CdekEntity
{
    /** @var int Порядковый номер дня начиная с единицы. Понедельник = 1, воскресенье = 7 */
    public $day;

    /** @var string Период работы в эти дни. Если в этот день не работают, то не отображать. ("10:00/16:00") */
    public $periods;

    /**
     * @inheritDoc
     */
    public function rules() : array
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
