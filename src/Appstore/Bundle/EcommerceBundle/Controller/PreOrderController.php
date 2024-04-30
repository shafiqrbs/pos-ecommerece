<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\PreOrderItem;
use Appstore\Bundle\EcommerceBundle\Entity\PreOrderPayment;
use Appstore\Bundle\EcommerceBundle\Form\PreOrderItemType;
use Appstore\Bundle\EcommerceBundle\Form\PreOrderPaymentType;
use Appstore\Bundle\EcommerceBundle\Form\PreOrderProcessType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\PreOrder;
use Appstore\Bundle\EcommerceBundle\Form\PreOrderType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * PreOrder controller.
 *
 */
class PreOrderController extends Controller
{


    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Lists all PreOrder entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ecommerce = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('EcommerceBundle:PreOrder')->findBy(array('globalOption' => $ecommerce), array('updated' => 'desc'));
        $pagination = $this->paginate($entities);
        return $this->render('EcommerceBundle:PreOrder:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    /**
     * Finds and displays a PreOrder entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:PreOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        return $this->render('EcommerceBundle:PreOrder:show.html.twig', array(
            'entity' => $entity,

        ));
    }


    /**
     * Creates a form to edit a PreOrder entity.
     *
     * @param PreOrder $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditProcessForm(PreOrder $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new PreOrderProcessType($entity->getGlobalOption(),$location), $entity, array(
            'action' => $this->generateUrl('customer_preorder_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'id' => 'orderProcess',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Creates a form to edit a PreOrder entity.
     *
     * @param PreOrder $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditPaymentForm(PreOrderPayment $entity,PreOrder $preOrder)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new PreOrderPaymentType($preOrder->getGlobalOption(),$location), $entity, array(
            'action' => $this->generateUrl('ecommerce_preorder_ajax_payment', array('id' => $preOrder->getId())),
            'method' => 'POST',
            'attr' => array(
                'id' => 'ecommerce-payment',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    public function itemAction(PreOrder $preOrder)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$preOrder) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        $paymentEntity = new  PreOrderPayment();
        $payment = $this->createEditPaymentForm($paymentEntity,$preOrder);
        $process = $this->createEditProcessForm($preOrder);


        return $this->render('EcommerceBundle:PreOrder:item.html.twig', array(
            'entity'      => $preOrder,
            'processForm'   => $process->createView(),
            'paymentForm'   => $payment->createView(),
        ));
    }


    /**
     * Edits an existing PreOrder entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:PreOrder')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        $process = $entity->getProcess();
        $editForm = $this->createEditProcessForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            if($entity->getProcess() == 'sms'){
                $entity->setProcess($process);
            }
            $em->flush();
            $this->getDoctrine()->getRepository('EcommerceBundle:PreOrder')->updatePreOder($entity);
            if($entity->getProcess() == 'confirm'){
                $em->getRepository('EcommerceBundle:PreOrderItem')->updatePreOrderItem($entity);
                $em->getRepository('EcommerceBundle:PreOrder')->updatePreOder($entity);
                $em->getRepository('EcommerceBundle:PreOrderPayment')->updatePreOrderPayment($entity);
                $em->getRepository('EcommerceBundle:PreOrder')->updatePreOderPayment($entity);

                $this->get('session')->getFlashBag()->add('success',"Customer has been verified");
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.pre_order_confirm_sms', new \Setting\Bundle\ToolBundle\Event\EcommercePreOrderSmsEvent($entity));
            }else{
                $this->get('session')->getFlashBag()->add('success',"Message has been sent successfully");
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.pre_order_comment_sms', new \Setting\Bundle\ToolBundle\Event\EcommercePreOrderSmsEvent($entity));

            }
            return $this->redirect($this->generateUrl('customer_preorder_item', array('id' => $id)));
        }




    }

    /**
     * Deletes a PreOrder entity.
     *
     */

    public function deleteAction(PreOrder $preOrder)
    {
        if ($preOrder) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($preOrder);
            $em->flush();
            return new Response('success');
        }else{
            return new Response('failed');
        }
    }
    
    
    public function processAction(PreOrder $preOrder,$process)
    {
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $preOrder->setProcess($process);
        if(!empty( $_GET['delivery'])){
            $address = $data['address'];
            $preOrder->setAddress($address);
            $delivery = $_GET['delivery'];
            $preOrder->setDelivery($delivery);
        }
        $em->persist($preOrder);
        $em->flush();
        return new Response('success');
    }

    public function approveAction(PreOrder $preOrder)
    {

        $em = $this->getDoctrine()->getManager();
        $preOrder->setApprovedBy($this->getUser());
        $preOrder->setProcess('approved');
        $em->persist($preOrder);
        $em->flush();
        return new Response('success');

    }

    public function paymentAction(Request $request ,PreOrder $preOrder)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = new PreOrderPayment();
        $entity->setPreOrder($preOrder);
        if($data['transactionType'] == 'Return'){
            $entity->setTransactionType('Return');
            $entity->setAmount('-'.$data['amount']);
        }else{
            $entity->setTransactionType('Payment');
            $entity->setAmount($data['amount']);
        }
        $accountMobileBank =$this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->find($data['accountMobileBank']);
        $entity->setAccountMobileBank($accountMobileBank);
        $entity->setMobileAccount($data['mobileAccount']);
        $entity->setTransaction($data['transaction']);
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('EcommerceBundle:PreOrder')->updatePreOderPayment($preOrder);
        return new Response('success');
    }

    public function paymentConfirmAction(PreOrderPayment $entity,$process)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setStatus($process);
        $em->persist($entity);
        $em->flush();
        if($entity->getStatus() == 1 ){
            $this->getDoctrine()->getRepository('EcommerceBundle:PreOrder')->updatePreOderPayment($entity->getPreOrder());
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.pre_order_payment_confirm_sms', new \Setting\Bundle\ToolBundle\Event\EcommercePreOrderPaymentSmsEvent($entity));
        }else{
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.pre_order_payment_confirm_sms', new \Setting\Bundle\ToolBundle\Event\EcommercePreOrderPaymentSmsEvent($entity));
        }
        return new Response('success');
        exit;


    }

    public function confirmItemAction(PreOrder $preOrder, PreOrderItem $preOrderItem)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        if($data['status'] == 1 ) {
            $preOrderItem->setConvertRate($data['convertRate']);
            $preOrderItem->setConvertUnitPrice($preOrderItem->getUnitPrice() * $data['convertRate']);
            $convertSubTotal = floatval($preOrderItem->getSubTotal() * $data['convertRate']);
            $preOrderItem->setConvertSubTotal($convertSubTotal);
            $preOrderItem->setShippingCharge($data['shippingCharge']);
            $convertTotal = floatval($preOrderItem->getConvertSubTotal() + $preOrderItem->getShippingCharge());
            $preOrderItem->setConvertTotal($convertTotal);
        }
        $preOrderItem->setStatus($data['status']);
        $em->persist($preOrderItem);
        $em->flush();
        $this->getDoctrine()->getRepository('EcommerceBundle:PreOrder')->updatePreOder($preOrder);
        return new Response('success');

    }

    public function invoiceAction(PreOrder $preOrder)
    {
        return $this->render('EcommerceBundle:PreOrder:invoice.html.twig', array(
            'entity' => $preOrder
        ));
    }



}
