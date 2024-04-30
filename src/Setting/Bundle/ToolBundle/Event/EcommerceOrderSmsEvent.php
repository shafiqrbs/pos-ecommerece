<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Symfony\Component\EventDispatcher\Event;


class EcommerceOrderSmsEvent extends Event
{

    /** @var \Appstore\Bundle\EcommerceBundle\Entity\Order  */

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;

    }


    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }


}