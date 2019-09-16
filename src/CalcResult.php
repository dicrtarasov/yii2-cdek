<?php
namespace dicr\cdek;

use yii\base\BaseObject;

/**
 * Результат рассчета стоимости доставки.
 *
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2019
 */
class CalcResult extends BaseObject
{
    /** @var array[] [code => Код ошибки, text => Текст ошибки] Массив ошибок при их возникновении */
    public $error;

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