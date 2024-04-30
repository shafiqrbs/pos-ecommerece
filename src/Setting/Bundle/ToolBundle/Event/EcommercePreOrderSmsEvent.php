<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\PreOrder;
use Symfony\Component\EventDispatcher\Event;


class EcommercePreOrderSmsEvent extends Event
{

    /** @var \Appstore\Bundle\EcommerceBundle\Entity\PreOrder  */

    protected $preOrder;

    public function __construct(PreOrder $preOrder)
    {
        $this->preOrder = $preOrder;

    }


    /**
     * @return PreOrder
     */
    public function getPreOrder()
    {
        return $this->preOrder;
    }


}