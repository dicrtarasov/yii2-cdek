<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 15:54:36
 */

declare(strict_types = 1);

namespace dicr\cdek\entity;

use dicr\cdek\CdekEntity;

/**
 * Город СДЭК.
 *
 * @package dicr\cdek
 * @see https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-%D0%9F%D1%80%D0%BE%D1%82%D0%BE%D0%BA%D0%BE%D0%BB%D0%BE%D0%B1%D0%BC%D0%B5%D0%BD%D0%B0%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8(v1.5)-4.12.Region%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA%D1%80%D0%B5%D0%B3%D0%B8%D0%BE%D0%BD%D0%BE%D0%B2
 */
class City extends CdekEntity
{
    /** UUID Идентификатор сущности в ИС СДЭК */
    public ?string $cityUuid = null;

    /** string[255] Название города */
    public ?string $cityName = null;

    /** Код города по базе СДЭК */
    public string|int|null $cityCode = null;

    /** Название региона */
    public ?string $region = null;

    /** Код региона */
    public string|int|null $regionCodeExt = null;

    /** Код региона в ИС СДЭК */
    public string|int|null $regionCode = null;

    /** Название района региона */
    public ?string $subRegion = null;

    /** Название страны */
    public ?string $country = null;

    /** Код страны */
    public string|int|null $countryCode = null;

    /** Широта */
    public string|float|null $latitude = null;

    /** Долгота */
    public string|float|null $longitude = null;

    /** Код города по КЛАДР */
    public ?string $kladr = null;

    /** UUID Код адресного объекта в ФИАС */
    public ?string $fiasGuid = null;

    /** UUID Код региона из ФИАС */
    public ?string $regionFiasGuid = null;

    /**
     * Ограничение на сумму наложенного платежа, возможные значения:
     *   -1 - ограничения нет;
     *    0 - наложенный платеж не принимается;
     *    положительное значение - сумма наложенного платежа не более данного значения.
     */
    public string|float|null $paymentLimit = null;

    /** Часовой пояс города */
    public ?string $timezone = null;
}
