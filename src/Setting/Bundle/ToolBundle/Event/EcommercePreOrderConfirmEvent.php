<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Proxies\__CG__\Appstore\Bundle\EcommerceBundle\Entity\PreOrder;
use Symfony\Component\EventDispatcher\Event;


class EcommercePreOrderConfirmEvent extends Event
{

    /** @var \Appstore\Bundle\EcommerceBundle\Entity\PreOrder  */

    protected $perOrder;

    public function __construct(PreOrder $perOrder)
    {
        $this->perOrder = $perOrder;

    }


    /**
     * @return Order
     */
    public function getPreOrder()
    {
        return $this->perOrder;
    }


}