<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent;
use Setting\Bundle\ToolBundle\Event\EcommercePreOrderSmsEvent;
use Setting\Bundle\ToolBundle\Event\SmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class EcommercePreOrderSmsListener extends BaseSmsAwareListener
{

    /**
     * @var EntityManager
     */
    protected  $em;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    protected $gateway;

    public function  __construct(Registry $doctrine, SmsGateWay $gateway)
    {
        $this->doctrine = $doctrine;
        $this->gateway = $gateway;
        $this->em = $doctrine->getManager();
    }


    public function sendSms(EcommercePreOrderSmsEvent $event)
    {

        /**
         * @var EcommercePreOrderSmsEvent $event
         */

        $post = $event->getPreOrder();
        $globalOption = $post->getGlobalOption();
        $shopName = $globalOption->getName();
        $domainName = 'www.'.$globalOption->getDomain();
        $customerMobile = "88".$post->getCreatedBy()->getProfile()->getMobile();
        $administratorMobile = "+88".$globalOption->getNotificationConfig()->getMobile();

        $created = $post->getCreated()->format('d-m-Y');
        $invoice = $post->getInvoice();
        $items = $post->getItem();
        $amount = $post->getGrandTotalAmount();
        //  $mobile = "8801828148148";

        $customerMsg = "Dear Sir, We have received your pre-order no. $invoice for ($items) items totaling to BDT $amount and delivery date $created. Thanks for using $domainName.";
        $administratorMsg = "You have received new pre-order $invoice on $shopName for $items item(s) totaling $amount and delivery date $created";

        if(!empty($globalOption->getSmsSenderTotal()) and $globalOption->getSmsSenderTotal()->getRemaining() > 0 and $globalOption->getNotificationConfig()->getSmsActive() == 1 and $globalOption->getNotificationConfig()->getOnlineOrder() == 1){

            if(!empty($customerMobile)){
                $status =  $this->gateway->send($customerMsg, $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertEcommerceSenderSms($globalOption,$customerMobile,$customerMsg,'Pre-order',$status);
            }
            if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                $status = $this->gateway->send($administratorMsg, $administratorMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertAdminEcommerceSenderSms($globalOption,$administratorMobile,$customerMsg,'Pre-order',$status);
            }
        }

    }

    public function sendConfirm(EcommercePreOrderSmsEvent $event)
    {
        /**
         * @var EcommercePreOrderSmsEvent $event
         */

        $post = $event->getPreOrder();
        $globalOption = $post->getGlobalOption();
        
        $domainName = 'www.'.$globalOption->getDomain();
        $deliveryDate = $post->getDeliveryDate()->format('d-m-Y');
        $invoice = $post->getInvoice();

        $customerMobile = "88".$post->getCreatedBy()->getProfile()->getMobile();

        $msg = "Dear Customer, Your pre-order $invoice is confirmed  and Delivery Date $deliveryDate . Thanks for using $domainName.";

        if(!empty($globalOption->getSmsSenderTotal()) and $globalOption->getSmsSenderTotal()->getRemaining() > 0 and $globalOption->getNotificationConfig()->getSmsActive() == 1 and $globalOption->getNotificationConfig()->getOnlineOrder() == 1){

            if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                $status =  $this->gateway->send($msg, $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertEcommerceOrderConfirmSms($globalOption, $customerMobile , $msg ,'Pre-order Confirm',$status);
            }
        }

    }

    public function sendComment(EcommercePreOrderSmsEvent $event)
    {
        /**
         * @var EcommercePreOrderSmsEvent $event
         */

        $post = $event->getPreOrder();
        $globalOption = $post->getGlobalOption();
        $domainName = 'www.'.$globalOption->getDomain();
        $invoice = $post->getInvoice();
        $comment = $post->getComment();

        $customerMobile = "88".$post->getCreatedBy()->getProfile()->getMobile();

        $msg = "Dear Customer, Your pre-order $invoice. $comment . Thanks for using $domainName.";

        if(!empty($globalOption->getSmsSenderTotal()) and $globalOption->getSmsSenderTotal()->getRemaining() > 0 and $globalOption->getNotificationConfig()->getSmsActive() == 1 and $globalOption->getNotificationConfig()->getOnlineOrder() == 1){

            if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                $status =  $this->gateway->send($msg, $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertEcommerceOrderConfirmSms($globalOption, $customerMobile , $msg,'Pre-order Comment',$status);
            }
        }
    }

}