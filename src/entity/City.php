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
 * Город СДЭК.
 *
 * @package dicr\cdek
 * @see https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-%D0%9F%D1%80%D0%BE%D1%82%D0%BE%D0%BA%D0%BE%D0%BB%D0%BE%D0%B1%D0%BC%D0%B5%D0%BD%D0%B0%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8(v1.5)-4.12.Region%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA%D1%80%D0%B5%D0%B3%D0%B8%D0%BE%D0%BD%D0%BE%D0%B2
 */
class City extends AbstractEntity
{
    /** @var string UUID Идентификатор сущности в ИС СДЭК */
    public $cityUuid;

    /** @var string string[255] Название города */
    public $cityName;

    /** @var int Код города по базе СДЭК */
    public $cityCode;

    /** @var string string[255] Название региона */
    public $region;

    /** @var int|null Код региона */
    public $regionCodeExt;

    /** @var int|null Код региона в ИС СДЭК */
    public $regionCode;

    /** @var string|null string(255) Название района региона */
    public $subRegion;

    /** @var string string(255) Название страны */
    public $country;

    /** @var int Код страны */
    public $countryCode;

    /** @var float|null Широта */
    public $latitude;

    /** @var float|null Долгота */
    public $longitude;

    /** @var string|null string(20) Код города по КЛАДР */
    public $kladr;

    /** @var string|null UUID Код адресного объекта в ФИАС */
    public $fiasGuid;

    /** @var string|null UUID Код региона из ФИАС */
    public $regionFiasGuid;

    /**
     * @var float Ограничение на сумму наложенного платежа, возможные значения:
     *   -1 - ограничения нет;
     *    0 - наложенный платеж не принимается;
     *    положительное значение - сумма наложенного платежа не более данного значения.
     */
    public $paymentLimit;

    /** @var string|null Часовой пояс города */
    public $timezone;
}
