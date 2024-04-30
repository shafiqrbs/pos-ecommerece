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
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class EcommerceOrderPaymentSmsListener extends BaseSmsAwareListener
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


    public function sendSms(EcommerceOrderPaymentSmsEvent $event)
    {

            /**
             * @var EcommerceOrderPaymentSmsEvent $event
             */

            $payment = $event->getPayment();
            $paymentAmount = $payment->getAmount();
            $method = $payment->getAccountMobileBank()->getName().'/'.$payment->getTransaction();
            $created = $payment->getCreated()->format('d-m-Y');

            $post = $event->getPayment()->getOrder();
            $administratorMobile = "+88".$post->getGlobalOption()->getNotificationConfig()->getMobile();

            $invoice = $post->getInvoice();
            $amount = $post->getGrandTotalAmount();
            $paid = $post->getPaidAmount();
            $administratorMsg = "You have received BDT $paymentAmount , created $created , Transaction $method order no $invoice total amount BDT $amount & total paid amount BDT $paid ";

            if(!empty($post->getGlobalOption()->getSmsSenderTotal()) and $post->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 and $post->getGlobalOption()->getNotificationConfig()->getSmsActive() == 1 and $post->getGlobalOption()->getNotificationConfig()->getOnlineOrder() == 1){

                if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                    $status = $this->gateway->send($administratorMsg, $administratorMobile);
                    $this->em->getRepository('SettingToolBundle:SmsSender')->insertAdminEcommercePaymentSms($post->getGlobalOption(),$administratorMobile,$administratorMsg,'Order',$status);
                }
            }

    }

    public function sendPaymentConfirmSms(EcommerceOrderPaymentSmsEvent $event)
    {

        /**
         * @var EcommerceOrderPaymentSmsEvent $event
         */

        $payment = $event->getPayment();
        $paymentAmount = $payment->getAmount();
        $method = $payment->getAccountMobileBank()->getName().'/'.$payment->getTransaction();
        $created = $payment->getCreated()->format('d-m-Y');


        $post = $event->getPayment()->getOrder();
        $domainName = 'www.'.$post->getGlobalOption()->getDomain();
        $customerMobile = "88".$post->getCustomerMobile();

        $invoice = $post->getInvoice();
        $amount = $post->getGrandTotalAmount();
        $paid = $post->getPaidAmount();

        if($payment->getStatus() == 2 ) {
            $customerMsg = "You have no received BDT $paymentAmount , created $created , Transaction $method order no $invoice total paid amount BDT $paid. Thanks for using $domainName.";
        }else{
            $customerMsg = "You have received BDT $paymentAmount , created $created , Transaction $method order no $invoice total paid amount BDT $paid. Thanks for using $domainName.";
        }
        if(!empty($post->getGlobalOption()->getSmsSenderTotal()) and $post->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 and $post->getGlobalOption()->getNotificationConfig()->getSmsActive() == 1 and $post->getGlobalOption()->getNotificationConfig()->getOnlineOrder() == 1){

            if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                $status = $this->gateway->send($customerMsg, $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertAdminEcommercePaymentConfirmSms($post->getGlobalOption(),$customerMobile,$customerMsg,'Order',$status);
            }
        }

    }
}