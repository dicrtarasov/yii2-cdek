<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 02.02.21 05:47:54
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\AbstractEntity;

/**
 * Регион в базе СДЭК.
 */
class Region extends AbstractEntity
{
    /** @var string UUID Идентификатор сущности в ИС СДЭК */
    public $regionUuid;

    /** @var string (255) Название региона */
    public $regionName;

    /** @var ?string (20) Префикс (возможные значения: обл, край, респ, АО, обл, г) */
    public $prefix;

    /** @var ?int Код региона */
    public $regionCodeExt;

    /** @var ?int Код региона в ИС СДЭК */
    public $regionCode;

    /** @var ?string UUID Код региона по ФИАС */
    public $regionFiasGuid;

    /** @var string (255) Название страны */
    public $countryName;

    /** @var int|null Код страны */
    public $countryCode;

    /** @var ?int Код ОКСМ */
    public $countryCodeExt;
}
