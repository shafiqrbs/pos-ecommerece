<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\EcommerceOrderConfirmEvent;
use Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent;
use Setting\Bundle\ToolBundle\Event\SmsEvent;
use Symfony\Component\Validator\Constraints\DateTime;


class EcommerceOrderConfirmListener extends BaseSmsAwareListener
{

    public function sendSms(EcommerceOrderConfirmEvent $event)
    {

        /**
         * @var EcommerceOrderSmsEvent $event
         */

        $post = $event->getOrder();
        $deliveryDate = $post->getDeliveryDate()->format('d-m-Y');
        $msg = "Dear Customer, Your invoice no:".$post->getInvoice().'order is confirmed  and Delivery Date:'.$deliveryDate;
       // $mobile = "88".$post->getCreatedBy()->getProfile()->getMobile();
        $mobile = "8801828148148";
        $this->gateway->send($msg, $mobile);

    }
}