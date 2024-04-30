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
use Setting\Bundle\ToolBundle\Event\EcommerceOrderPaymentSmsEvent;
use Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent;
use Setting\Bundle\ToolBundle\Event\EcommercePreOrderPaymentSmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class EcommercePreOrderPaymentSmsListener extends BaseSmsAwareListener
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


    public function sendSms(EcommercePreOrderPaymentSmsEvent $event)
    {

            /**
             * @var EcommercePreOrderPaymentSmsEvent $event
             */

            $payment = $event->getPayment();
            $paymentAmount = $payment->getAmount();
            $method = $payment->getAccountMobileBank()->getName().'/'.$payment->getTransaction();
            $created = $payment->getCreated()->format('d-m-Y');

            $post = $event->getPayment()->getPreOrder();
            $globalOption = $post->getGlobalOption();
            $administratorMobile = "+88".$globalOption->getNotificationConfig()->getMobile();

            $invoice = $post->getInvoice();
            $amount = $post->getGrandTotalAmount();
            $paid = $post->getPaidAmount();
            $administratorMsg = "You have received BDT $paymentAmount , created $created , Transaction $method pre-order no $invoice total amount BDT $amount & total paid amount BDT $paid ";

            if(!empty($globalOption->getSmsSenderTotal()) and $globalOption->getSmsSenderTotal()->getRemaining() > 0 and $globalOption->getNotificationConfig()->getSmsActive() == 1 and $globalOption->getNotificationConfig()->getOnlineOrder() == 1){

                if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                    $status = $this->gateway->send($administratorMsg, $administratorMobile);
                    $this->em->getRepository('SettingToolBundle:SmsSender')->insertAdminEcommercePaymentSms($globalOption,$administratorMobile,$administratorMsg,'Pre-order',$status);
                }
            }

    }

    public function sendPaymentConfirmSms(EcommercePreOrderPaymentSmsEvent $event)
    {

        /**
         * @var EcommercePreOrderPaymentSmsEvent $event
         */

        $payment = $event->getPayment();
        $paymentAmount = $payment->getAmount();
        $method = $payment->getAccountMobileBank()->getName().'/'.$payment->getTransaction();
        $created = $payment->getCreated()->format('d-m-Y');


        $post = $event->getPayment()->getPreOrder();

        $domainName = 'www.'.$post->getGlobalOption()->getDomain();
        $customerMobile = "88".$post->getCreatedBy()->getProfile()->getMobile();

        $invoice = $post->getInvoice();
        $paid = $post->getPaidAmount();

        if($payment->getStatus() == 2 ){
            $customerMsg = "We have no received BDT $paymentAmount ,Transaction $method pre-order no $invoice  & total paid amount BDT $paid. Thanks for using $domainName.";
        }else{
            $customerMsg = "We have received BDT $paymentAmount ,Transaction $method pre-order no $invoice & total paid amount BDT $paid. Thanks for using $domainName.";
        }

        if(!empty($post->getGlobalOption()->getSmsSenderTotal()) and $post->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 and $post->getGlobalOption()->getNotificationConfig()->getSmsActive() == 1 and $post->getGlobalOption()->getNotificationConfig()->getOnlineOrder() == 1){

            if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                $status = $this->gateway->send($customerMsg, $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertAdminEcommercePaymentConfirmSms($post->getGlobalOption(),$customerMobile,$customerMsg,'Pre-order',$status);
            }
        }

    }
}