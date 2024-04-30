<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class EcommerceOrderSmsListener extends BaseSmsAwareListener
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


    public function sendSms(EcommerceOrderSmsEvent $event)
    {

            /**
             * @var EcommerceOrderSmsEvent $event
             */

            $post = $event->getOrder();
            $globalOption = $post->getGlobalOption();
            $shopName = $globalOption->getName();
            $domainName = 'www.'.$globalOption->getDomain();
            $customerMobile = "88".$post->getCustomerMobile();
            $administratorMobile = "+88".$globalOption->getNotificationConfig()->getMobile();

            $deliveryDate = $post->getDeliveryDate()->format('d-m-Y');
            $created = $post->getCreated()->format('d-m-Y');
            $invoice = $post->getInvoice();
            $items = $post->getItem();
            $amount = $post->getGrandTotalAmount();
            //  $mobile = "8801828148148";

            $customerMsg = "Dear Sir, We have received your order no. $invoice for ($items) items totaling to BDT $amount and delivery date $deliveryDate. Thanks for using $domainName.";
            $administratorMsg = "You have received new order $invoice on $shopName for $items item(s) totaling $amount and delivery date $deliveryDate";

            if(!empty($globalOption->getSmsSenderTotal()) and $globalOption->getSmsSenderTotal()->getRemaining() > 0 and $globalOption->getNotificationConfig()->getSmsActive() == 1 and $globalOption->getNotificationConfig()->getOnlineOrder() == 1){

                if(!empty($customerMobile)){
                    $status =  $this->gateway->send($customerMsg, $customerMobile);
                    $this->em->getRepository('SettingToolBundle:SmsSender')->insertEcommerceSenderSms($globalOption,$customerMobile,$customerMsg,'Order',$status);
                }
                
                if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                    $status = $this->gateway->send($administratorMsg, $administratorMobile);
                    $this->em->getRepository('SettingToolBundle:SmsSender')->insertAdminEcommerceSenderSms($globalOption,$administratorMobile,$customerMsg,'Order',$status);
                }
            }

    }

    public function sendConfirm(EcommerceOrderSmsEvent $event)
    {
        /**
         * @var EcommerceOrderSmsEvent $event
         */

        $post = $event->getOrder();
        $globalOption = $post->getGlobalOption();
        $domainName = 'www.'.$globalOption->getDomain();
        $deliveryDate = $post->getDeliveryDate()->format('d-m-Y');
        $invoice = $post->getInvoice();

        $customerMobile = "88".$post->getCreatedBy()->getProfile()->getMobile();

        $msg = "Dear Customer, Your order $invoice is confirmed  and Delivery Date $deliveryDate . Thanks for using $domainName.";

        if(!empty($globalOption->getSmsSenderTotal()) and $globalOption->getSmsSenderTotal()->getRemaining() > 0 and $globalOption->getNotificationConfig()->getSmsActive() == 1 and $globalOption->getNotificationConfig()->getOnlineOrder() == 1){

            if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                $status =  $this->gateway->send($msg, $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertEcommerceOrderConfirmSms($globalOption, $customerMobile , $msg ,'Order confirm',$status);
            }
        }

    }

    public function sendComment(EcommerceOrderSmsEvent $event)
    {
        /**
         * @var EcommerceOrderSmsEvent $event
         */

        $post = $event->getOrder();
        $globalOption = $post->getGlobalOption();
        $domainName = 'www.'.$globalOption->getDomain();
        $invoice = $post->getInvoice();
        $comment = $post->getComment();

        $customerMobile = "88".$post->getCreatedBy()->getProfile()->getMobile();

        $msg = "Dear Customer, Your order $invoice. $comment . Thanks for using $domainName.";

        if(!empty($globalOption->getSmsSenderTotal()) and $globalOption->getSmsSenderTotal()->getRemaining() > 0 and $globalOption->getNotificationConfig()->getSmsActive() == 1 and $globalOption->getNotificationConfig()->getOnlineOrder() == 1){

            if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                $status =  $this->gateway->send($msg, $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertEcommerceOrderConfirmSms($globalOption, $customerMobile , $msg,'Order comment',$status);
            }
        }
    }
}