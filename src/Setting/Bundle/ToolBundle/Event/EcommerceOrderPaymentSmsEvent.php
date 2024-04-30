<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\OrderPayment;
use Symfony\Component\EventDispatcher\Event;


class EcommerceOrderPaymentSmsEvent extends Event
{

    /** @var \Appstore\Bundle\EcommerceBundle\Entity\OrderPayment  */

    protected $payment;

    public function __construct(OrderPayment $payment)
    {
        $this->payment = $payment;

    }


    /**
     * @return OrderPayment
     */
    public function getPayment()
    {
        return $this->payment;
    }


}