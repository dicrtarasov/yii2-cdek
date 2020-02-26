<?php
/**
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 27.02.20 01:25:39
 */

declare(strict_types = 1);
namespace dicr\cdek;

use yii\base\BaseObject;

/**
 * Результат рассчета стоимости доставки.
 *
 * @see https://confluence.cdek.ru/pages/viewpage.action?pageId=15616129#id-%D0%9F%D1%80%D0%BE%D1%82%D0%BE%D0%BA%D0%BE%D0%BB%D0%BE%D0%B1%D0%BC%D0%B5%D0%BD%D0%B0%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC%D0%B8(v1.5)-4.14.1.2.%D0%A4%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%BE%D1%82%D0%B2%D0%B5%D1%82%D0%B0
 */
class CalcResult extends BaseObject
{
    /** @var float Сумма за доставку в рублях */
    public $price;

    /** @var int Минимальное время доставки в днях */
    public $deliveryPeriodMin;

    /** @var int Максимальное время доставки в днях */
    public $deliveryPeriodMax;

    /** @var string Минимальная дата доставки, формате 'ГГГГ-ММ-ДД' */
    public $deliveryDateMin;

    /** @var string Максимальная дата доставки, формате 'ГГГГ-ММ-ДД' */
    public $deliveryDateMax;

    /** @var int Код тарифа, по которому рассчитана сумма доставки */
    public $tariffId;

    /** @var float Ограничение оплаты наличными, появляется только если оно есть */
    public $cashOnDelivery;

    /** @var float Цена в валюте, по которой ИМ работает со СДЭК. Валюта определяется по authLogin и secure. */
    public $priceByCurrency;

    /** @var string (3) Валюта интернет-магазина */
    public $currency;

    /** @var int Размер ставки НДС для данного клиента. Появляется в случае, если переданы authLogin и secure, по ним же определяется ставка ИМ.
     * Если ставка НДС не предусмотрена условиями договора, данный параметр не будет отображен.*/
    public $percentVAT;

    /** @var array Список передаваемых дополнительных услуг (подробнее см. приложение 2)
     *  - int $id - Идентификатор переданной услуги
     *  - string $title - Заголовок услуги
     *  - float $price - Стоимость услуги без учета НДС в рублях
     *  - float $rate - Процент для расчета дополнительной услуги
     */
    public $services;
}
