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
use Setting\Bundle\ToolBundle\Event\EcommercePreOrderConfirmEvent;
use Setting\Bundle\ToolBundle\Event\SmsEvent;
use Symfony\Component\Validator\Constraints\DateTime;


class EcommercePreOrderConfirmListener extends BaseSmsAwareListener
{

    public function sendSms(EcommercePreOrderConfirmEvent $event)
    {

        /**
         * @var EcommercePreOrderConfirmEvent $event
         */

        $post = $event->getPreOrder();

        $date = new DateTime($post->getDeliveryDate());
        echo $result = date_format($date,"Y/m/d H:i:s");;
       // echo $deliveryDate = strtotime($post->getDeliveryDate());
       // echo $date = date('d-m-Y',$deliveryDate);
        exit;
        $msg = "Dear Customer, Your invoice no:".$post->getInvoice().'order is confirmed  and Delivery Date:'.$date;
       // $mobile = "88".$post->getCreatedBy()->getProfile()->getMobile();
        $mobile = "8801828148148";
        $this->gateway->send($msg, $mobile);

    }
}