<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Appstore\Bundle\EcommerceBundle\Entity\PreOrderPayment;
use Symfony\Component\EventDispatcher\Event;


class EcommercePreOrderPaymentSmsEvent extends Event
{

    /** @var \Appstore\Bundle\EcommerceBundle\Entity\PreOrderPayment  */

    protected $payment;

    public function __construct(PreOrderPayment $payment)
    {
        $this->payment = $payment;

    }

    /**
     * @return PreOrderPayment
     */
    public function getPayment()
    {
        return $this->payment;
    }


}