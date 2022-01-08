<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 16:13:40
 */

declare(strict_types = 1);
namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * Регион в базе СДЭК.
 */
class Region extends CdekEntity
{
    /** UUID Идентификатор сущности в ИС СДЭК */
    public ?string $regionUuid = null;

    /** Название региона */
    public ?string $regionName = null;

    /** Префикс (возможные значения: обл, край, респ, АО, обл, г) */
    public ?string $prefix = null;

    /** Код региона */
    public string|int|null $regionCodeExt = null;

    /** Код региона в ИС СДЭК */
    public string|int|null $regionCode = null;

    /** UUID Код региона по ФИАС */
    public ?string $regionFiasGuid = null;

    /** Название страны */
    public ?string $countryName = null;

    /** Код страны */
    public string|int|null $countryCode = null;

    /** Код ОКСМ */
    public string|int|null $countryCodeExt = null;
}
