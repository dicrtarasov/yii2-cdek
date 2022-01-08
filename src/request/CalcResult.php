<?php
/*
 * @copyright 2019-2022 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 08.01.22 15:48:53
 */

declare(strict_types = 1);
namespace dicr\cdek\request;

use dicr\cdek\CdekResponse;
use dicr\cdek\entity\Service;

/**
 * Результат расчета стоимости доставки.
 *
 * @see https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-%D0%9F%D1%80%D0%BE%D1%82%D0%BE%D0%BA%D0%BE%D0%BB%D0%BE%D0%B1%D0%BC%D0%B5%D0%BD%D0%B0%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8(v1.5)-4.14.1.2.%D0%A4%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%BE%D1%82%D0%B2%D0%B5%D1%82%D0%B0
 */
class CalcResult extends CdekResponse
{
    /** Сумма за доставку в рублях */
    public string|float|null $price = null;

    /** Минимальное время доставки в днях */
    public string|int|null $deliveryPeriodMin = null;

    /** Максимальное время доставки в днях */
    public string|int|null $deliveryPeriodMax = null;

    /** Минимальная дата доставки, формате 'ГГГГ-ММ-ДД' */
    public ?string $deliveryDateMin = null;

    /** Максимальная дата доставки, формате 'ГГГГ-ММ-ДД' */
    public ?string $deliveryDateMax = null;

    /** Код тарифа, по которому рассчитана сумма доставки */
    public string|int|null $tariffId = null;

    /** Ограничение оплаты наличными, появляется только если оно есть */
    public string|float|null $cashOnDelivery = null;

    /** Цена в валюте, по которой ИМ работает со СДЭК. Валюта определяется по authLogin и secure. */
    public string|float|null $priceByCurrency = null;

    /** Валюта интернет-магазина */
    public ?string $currency = null;

    /** Размер ставки НДС для данного клиента. Появляется в случае, если переданы authLogin и secure, по ним же определяется ставка ИМ.
     * Если ставка НДС не предусмотрена условиями договора, данный параметр не будет отображен.*/
    public string|int|null $percentVAT = null;

    /** @var Service[]|null Список передаваемых дополнительных услуг (подробнее см. приложение 2) */
    public ?array $services = null;

    /**
     * @inheritDoc
     */
    public function attributeEntities(): array
    {
        return [
            'services' => [Service::class]
        ];
    }
}
