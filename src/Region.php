<?php
namespace dicr\cdek;

use yii\base\BaseObject;

/**
 * Регион в базе СДЭК.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class Region extends BaseObject
{
    /** @var string UUID Идентификатор сущности в ИС СДЭК */
    public $regionUuid;

    /** @var string (255) Название региона */
    public $regionName;

    /** @var string|null (20) Префикс (возможные значения: обл, край, респ, АО, Аобл, г) */
    public $prefix;

    /** @var int|null Код региона */
    public $regionCodeExt;

    /** @var int|null Код региона в ИС СДЭК */
    public $regionCode;

    /** @var string|null UUID Код региона по ФИАС */
    public $regionFiasGuid;

    /** @var string (255) Название страны */
    public $countryName;

    /** @var int|null Код страны */
    public $countryCode;

    /** @var int|null Код ОКСМ */
    public $countryCodeExt;
}