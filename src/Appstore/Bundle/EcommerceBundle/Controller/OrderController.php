<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\EcommerceBundle\Entity\CourierService;
use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\OrderItem;
use Appstore\Bundle\EcommerceBundle\Entity\OrderPayment;
use Appstore\Bundle\EcommerceBundle\Form\MedicineItemType;
use Appstore\Bundle\EcommerceBundle\Form\OrderPaymentType;
use Appstore\Bundle\EcommerceBundle\Form\OrderType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Knp\Snappy\Pdf;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Order controller.
 *
 */
class OrderController extends Controller
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
     * Lists all Item entities.
     *
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_ORDER,ROLE_DOMAIN")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $entities = $em->getRepository('EcommerceBundle:Order')->findWithSearch($globalOption->getId(),$data);
        $processes = $em->getRepository('EcommerceBundle:Order')->orderCountByProcess();
        $pagination = $this->paginate($entities);
        $couriers = $this->getDoctrine()->getRepository(CourierService::class)->findBy(array('ecommerceConfig'=>$globalOption->geteCommerceConfig(),'status'=>1));
        return $this->render('EcommerceBundle:Order:index.html.twig', array(
            'entities' => $pagination,
            'processes' => $processes,
            'couriers' => $couriers,
            'searchForm' => $data,
        ));
    }

    /**
     * Lists all Item entities.
     *
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_ORDER,ROLE_DOMAIN")
     */

    public function archiveAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $entities = $em->getRepository('EcommerceBundle:Order')->findWithArchive($globalOption->getId(),$data);
        $pagination = $this->paginate($entities);
        $couriers = $this->getDoctrine()->getRepository(CourierService::class)->findBy(array('ecommerceConfig'=>$globalOption->geteCommerceConfig(),'status'=>1));
        return $this->render('EcommerceBundle:Order:archive.html.twig', array(
            'entities' => $pagination,
            'couriers' => $couriers,
        ));
    }

    /**
     * Lists all Item entities.
     *
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_ORDER,ROLE_DOMAIN")
     */

    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new Order();
        $config = $this->getUser()->getGlobalOption();
        $entity->setEcommerceConfig($config->getEcommerceConfig());
        $entity->setGlobalOption($config);
        $entity->setCreatedBy($this->getUser());
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($this->getUser()->getGlobalOption());
        $entity->setCustomer($customer);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('customer_order_edit', array('id' => $entity->getId())));

    }

    /**
     * Lists all Item entities.
     *
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_ORDER,ROLE_DOMAIN")
     */

    public function editAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $order = $em->getRepository('EcommerceBundle:Order')->findOneBy(array('ecommerceConfig'=>$config,'id'=>$id));
        $payment = $this->createEditForm($order);
        $salesItemForm = $this->createMedicineSalesItemForm(new OrderItem(),$order);
        $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSingleOutstanding($this->getUser()->getGlobalOption(),$order->getCustomer());
        $balance = empty($result) ? 0 : $result;
        $currentCredit= ($order->getCustomer()->getCreditLimit() - $balance);
        return $this->render("EcommerceBundle:Order/ecommerce:new.html.twig", array(
            'globalOption' => $order->getGlobalOption(),
            'entity'                => $order,
            'currentCredit'         => $currentCredit,
            'salesItem'             => $salesItemForm->createView(),
            'paymentForm'           => $payment->createView(),
        ));

    }

    /**
     * Lists all Item entities.
     *
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_ORDER,ROLE_DOMAIN")
     */

    public function confirmAction(Request $request ,Order $order)
    {
        $em = $this->getDoctrine()->getManager();
        $payment = $this->createEditForm($order);
        $payment->handleRequest($request);
        $data = $request->request->all();
        if ($payment->isValid()) {
            $method = ( isset($data['method']) and  $data['method']) ?  $data['method'] : "Credit";
            $meth = $em->getRepository(TransactionMethod::class)->findOneBy(array('name'=>$method));
            $order->setPaymentMode($method);
            $order->setTransactionMethod($meth);
            $due = ($order->getTotal() - $order->getReceive());
            $order->setDue($due);
            $order->setProcessBy($this->getUser());
            $order->setApprovedBy($this->getUser());
            $em->persist($order);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Customer has been confirmed");
            if ($order->getProcess() == 'confirm' ) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.order_confirm_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent($order));
            }elseif($order->getProcess() == "delivered" ){
                 $em->getRepository('EcommerceBundle:OrderItem')->updateOrderItem($order);
                if($order->getDiscountCalculation() > 0){
                    $this->getDoctrine()->getRepository(OrderItem::class)->updateSpecialDiscountEcommerceSales($order);
                }
                if(empty($order->getInventorySales())) {
                    $em->getRepository("InventoryBundle:Sales")->insertEcommerceDirectOrder($order);
                }
            }
            return $this->redirect($this->generateUrl('customer_order'));
        }
        $salesItemForm = $this->createMedicineSalesItemForm(new OrderItem(),$order);
        $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSingleOutstanding($this->getUser()->getGlobalOption(),$order->getCustomer());
        $balance = empty($result) ? 0 : $result;
        $currentCredit= ($order->getCustomer()->getCreditLimit() - $balance);
        return $this->render("EcommerceBundle:Order/ecommerce:new.html.twig", array(
            'globalOption' => $order->getGlobalOption(),
            'entity'                => $order,
            'currentCredit'         => $currentCredit,
            'salesItem'             => $salesItemForm->createView(),
            'paymentForm'           => $payment->createView(),
        ));


    }

    /**
    * Creates a form to edit a Order entity.
    *
    * @param Order $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Order $entity)
    {
        $globalOption = $entity->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new OrderType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('customer_order_confirm', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'id' => 'orderProcess',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Finds and displays a Order entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        if( $entity->getGlobalOption()->getDomainType() == 'medicine' ) {
            $theme = 'medicine';
        }else{
            $theme = 'ecommerce';
        }
        return $this->render("EcommerceBundle:Order/{$theme}:show.html.twig", array(
            'globalOption' => $entity->getGlobalOption(),
            'entity'      => $entity,
        ));

    }

    private function createMedicineSalesItemForm(OrderItem $orderItem,Order $order )
    {

        $form = $this->createForm(new MedicineItemType(), $orderItem, array(
            'action' => $this->generateUrl('customer_order_item',array('id' => $order->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'orderItem',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing OrderItem entity.
     *
     */
    public function orderItemAddAction(Order $entity , Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $orderItem = new OrderItem();
        $data = $request->request->all()['orderItem'];
        $stockId = $data['itemName'];
        /* @var $product Item */
        $product = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->find($stockId);
        $unit = ($product->getProductUnit()) ? $product->getProductUnit()->getName() :'';
        $brand = ($product->getBrand()) ? $product->getBrand()->getName() :'';
        $category = ($product->getCategory()) ? $product->getCategory()->getName() :'';
        $orderItem->setOrder($entity);
        $orderItem->setItem($product);
        $orderItem->setItemName($product->getNameBn());
        $orderItem->setBrandName($brand);
        $orderItem->setCategoryName($category);
        $orderItem->setUnitName($unit);
        $orderItem->setQuantity($data['quantity']);
        $orderItem->setPrice($product->getSalesPrice());
        $discount = ($product->getDiscountPrice() > 0 ) ? $product->getDiscountPrice() : $product->getSalesPrice();
        $orderItem->setDiscountPrice($discount);
        $orderItem->setSubTotal($product->getSalesPrice() * $data['quantity']);
        $em->persist($orderItem);
        $em->flush();
        $em->getRepository('EcommerceBundle:Order')->updateCustomOrder($entity);
        $array = $this->renderCatrtItem($entity);
        $data = array(
            'items'=> $array,
            'discount'=> $entity->getDiscount(),
            'total' => $entity->getTotal(),
        );
        return new Response (json_encode($data));

    }

    public function ajaxDiscountAction(Order $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $discountCal = (isset($_REQUEST['discount']) and $_REQUEST['discount']) ? $_REQUEST['discount']:0;
        if($discountCal > 0){
            $discount = ($entity->getSubTotal() * $discountCal)/100;
            $total = ($entity->getSubTotal()  - $discount);
            $entity->setDiscountCalculation($discountCal);
            $entity->setDiscount(round($discount));
            $entity->setTotal(round($total));
            $entity->setDue(round($total));
            $entity->setSpecialDiscount(true);
        }else{
            $order = $this->getDoctrine()->getRepository(Order::class)->updateCustomOrder($entity);
            $entity->setDiscountCalculation(0);
            $entity->setDiscount(round($order->getDiscount()));
            $entity->setTotal(round($order->getTotal()));
            $entity->setDue(round($order->getTotal()));
            $entity->setSpecialDiscount(false);
        }
        $em->persist($entity);
        $em->flush();
        $data = array(
            'discount'=> $entity->getDiscount(),
            'shippingCharge'=> $entity->getShippingCharge(),
            'vat' => $entity->getVat(),
            'total' => $entity->getTotal(),
            'receive' => $entity->getReceive(),
            'due' => ($entity->getTotal() - $entity->getReceive())
        );
        return new Response ( json_encode($data));

    }

    public function renderCatrtItem(Order $entity)
    {
        $theme = 'ecommerce';
        $html =  $this->renderView("EcommerceBundle:Order/ecommerce:ajaxOrderItem.html.twig", array(
            'globalOption' => $entity->getGlobalOption(),
            'entity' => $entity,
        ));
        return $html;

    }


    public function stockDetailsAction(Item $stock)
    {
        $unit = ($stock->getProductUnit()) ? $stock->getProductUnit()->getName() : '';
        return new Response(json_encode(array('unit' => $unit , 'price' => $stock->getSalesPrice())));
    }

    public function autoSearchAction(Request $request)
    {
        $item = trim($_REQUEST['q']);
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
            $item = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->searchWebStock($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function autoCustomerSearchAction(Request $request)
    {
        $q = $_REQUEST['term'];
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->searchEcommerceCustomer($option,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[] = ['id' => $entity['id'],'value' => $entity['text']];
        endforeach;
        return new JsonResponse($items);
    }

    public function orderUpdateCustomerAction()
    {
        $data = $_REQUEST;
        $order = $data['order'];
        $userId = $data['customer'];
        $em = $this->getDoctrine()->getManager();
        /* @var $entity Order */
        $entity = $this->getDoctrine()->getRepository("EcommerceBundle:Order")->find($order);
        $user = $this->getDoctrine()->getRepository("UserBundle:User")->find($userId);
        $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy(array('user'=>$user->getId()));
        $entity->setCustomerName($user->getProfile()->getName());
        $entity->setCustomerMobile($user->getProfile()->getMobile());
        $entity->setAddress($user->getProfile()->getAddress());
        if(empty($customer)){
            $customer = $this->getDoctrine()->getRepository(Customer::class)->insertEcommerceCustomer($user->getProfile());
        }
        $entity->setCustomer($customer);
        $em->flush();
        return new Response('success');
    }

    public function archiveProcessAction(Order $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setIsArchive(1);
        $entity->setApprovedBy($this->getUser());
        $em->flush();
        $this->getDoctrine()->getRepository(AccountSales::class)->insertEcommerceSales($entity);
        return new Response('success');
    }

    public function deliveredProcessAction(Order $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $customer = $entity->getCustomer();
        $balance = $this->getDoctrine()->getRepository(AccountSales::class)->customerSingleOutstanding( $customer->getGlobalOption(),$customer->getId());
        $currentOutStanding = ($balance + $entity->getTotal());
        $limit = ($currentOutStanding -  $customer->getCreditLimit());
        if( $customer->getCreditLimit() > $currentOutStanding and empty($entity->getInventorySales()) ){
            if($entity->getDiscountCalculation() > 0){
                $this->getDoctrine()->getRepository(OrderItem::class)->updateSpecialDiscountEcommerceSales($entity);
            }
            $this->getDoctrine()->getRepository('InventoryBundle:Sales')->insertEcommerceSales($entity);
            $entity->setProcess('delivered');
            $entity->setProcessBy($this->getUser());
            $em->flush();
            $this->get('session')->getFlashBag()->add('success',"Order has been process successfully");
        }else{
            $this->get('session')->getFlashBag()->add('notice',"This {$customer->getName()} credit is not available");
        }
        return new Response('success');
    }

    public function inlineOrderUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:Order')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $setName = 'set'.$data['name'];
        $entity->$setName($data['value']);
        $em->persist($entity);
        $em->flush();
        if($entity->getShippingCharge() > 0 ){
            $em->getRepository('EcommerceBundle:Order')->updateOrder($entity);
        }
        $array = $this->renderCatrtItem($entity);
        $data = array(
            'items'=> $array,
            'discount'=> $entity->getDiscount(),
            'total' => $entity->getTotal(),
        );
        return new Response (json_encode($data));
    }

    public function paymentAjaxUpdateAction(Request $request ,Order $order)
    {

        $data =$_REQUEST;
        $shippingCharge = $data['shippingCharge'];
        $discount = $data['discount'];
        $em = $this->getDoctrine()->getManager();
        $order->setShippingCharge($shippingCharge);
        $order->setDiscount($discount);
        $total = ($order->getSubTotal() + $order->getVat() + $order->getShippingCharge() - $order->getDiscount());
        $order->setTotal($total);
        $em->persist($order);
        $em->flush();
        $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateOrderPayment($order);
        $data = array('discount'=>$order->getDiscount(),'shippingCharge'=>$order->getShippingCharge(),'vat' => $order->getVat(),'total' => $order->getTotal(),'receive' => $order->getReceive(),'due' => ($order->getTotal() - $order->getReceive()));
        return new Response ( json_encode($data));
    }

    public function updateOrderInformation(Order $order,$data)
    {

        $em = $this->getDoctrine()->getManager();
        if (!empty($data['customerMobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
            $user = $this->getDoctrine()->getRepository('UserBundle:User')->newExistingCustomerForSales($order->getGlobalOption(),$mobile,$data);
            $order->setCreatedBy($user);
            $order->setCustomerName($user->getProfile()->getName());
            $order->setCustomerMobile($user->getProfile()->getMobile());
            $order->setAddress($user->getProfile()->getAddress());
        }
        if (isset($data['deliveryDate']) and $data['deliveryDate']) {
            $date = new \DateTime($data['deliveryDate']);
            $order->setDeliveryDate($date);
        }
        if (isset($data['deliverySlot']) and $data['deliverySlot']) {
            $order->setDeliverySlot($data['deliverySlot']);
        }
        if (isset($data['trackingNo']) and $data['trackingNo']) {
            $order->setTrackingNo($data['trackingNo']);
        }
        if (isset($data['timePeriod']) and $data['timePeriod']) {
            $timePeriod = $this->getDoctrine()->getRepository('EcommerceBundle:TimePeriod')->find($data['timePeriod']);
            $order->setTimePeriod($timePeriod);
        }
        if (isset($data['courier']) and $data['courier']) {
            $courier = $this->getDoctrine()->getRepository('EcommerceBundle:CourierService')->find($data['courier']);
            $order->setCourier($courier);
        }
        if (isset($data['shippingCharge']) and $data['shippingCharge']) {
            $order->setShippingCharge($data['shippingCharge']);
        }
        if (isset($data['discountAmount']) and $data['discountAmount']) {
            $order->setDiscountAmount($data['discountAmount']);
        }
        if (isset($data['process']) and $data['process']) {
            $order->setProcess($data['process']);
        }
        $em->persist($order);
        $em->flush();
        $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateOrder($order);
        return new Response('success');
    }


    /**
     * Displays a form to edit an existing OrderItem entity.
     *
     */
    public function inlineUpdateAction(Request $request,OrderItem $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity->setQuantity($data['value']);
        $entity->setSubTotal($entity->getPrice() *  floatval($data['value']));
        $em->flush();
        $em->getRepository('EcommerceBundle:Order')->updateOrder($entity->getOrder());
        return new Response('success');
    }


    /**
     * Displays a form to edit an existing OrderItem entity.
     *
     */
    public function inlineDisableAction(Request $request,OrderItem $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if($entity->getStatus() == 1){
            $entity->setStatus(false);
        }else{
            $entity->setStatus(true);
        }
        $em->flush();
        $em->getRepository('EcommerceBundle:Order')->updateOrder($entity->getOrder());
        return new Response('success');

    }

    public function itemUpdateAction(Order $order , OrderItem $item)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$item) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        $data = $_REQUEST;
        $item->setQuantity($data['quantity']);
        $item->setSubTotal($item->getPrice() * $data['quantity']);
        $em->persist($item);
        $em->flush();
        $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateOrder($order);

        $this->get('session')->getFlashBag()->add(
            'success',"Item has been updated successfully"
        );
        return new Response('success');

    }

    public function itemDeleteAction(Order $order , OrderItem $item)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();
        $entity = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateCustomOrder($order);
        $array = $this->renderCatrtItem($entity);
        $data = array(
            'items'=> $array,
            'discount'=> $entity->getDiscount(),
            'total' => $entity->getTotal(),
        );
        return new Response (json_encode($data));
    }



    public function orderProcessSourceAction()
    {
        $items[]=array('value' => 'created','text'=> 'Created');
        $items[]=array('value' => 'wfc','text'=> 'Waiting for Confirm');
        $items[]=array('value' => 'confirm','text'=> 'Confirm');
        $items[]=array('value' => 'cancel','text'=> 'Cancel');
        $items[]=array('value' => 'delete','text'=> 'Delete');
        return new JsonResponse($items);

    }

    public function inlineProcessUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        /* @var $order Order */
        $order = $em->getRepository('EcommerceBundle:Order')->find($data['pk']);
        if (!$order) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }
        $order->setProcess($data['value']);
        $em->flush();
        if($order->getProcess() == 'confirm') {
            $em->getRepository('EcommerceBundle:OrderItem')->updateOrderItem($order);
            $em->getRepository('EcommerceBundle:Order')->updateOrder($order);
            $em->getRepository('EcommerceBundle:OrderPayment')->updateOrderPayment($order);
            $em->getRepository('EcommerceBundle:Order')->updateOrderPayment($order);
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.order_confirm_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent($order));
        }
        return new Response('success');


    }


    public function confirmItemAction(Order $order, OrderItem $orderItem)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $orderItem->setStatus($data['status']);
        $em->persist($orderItem);
        $em->flush();
        $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateOrder($order);
        return new Response('success');

    }

    public function deleteAction(Order $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        $entity->setIsDelete(1);
        $em->persist($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return new Response('success');
    }


    public function getBarcode($invoice)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($invoice);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(32);
        $barcode->setFontSize(7);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,' . $code . '" />';
        return $data;
    }

    public function pdfAction(Order $order)
    {


        /* @var Order $order */

        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($order->getGrandTotalAmount());
        $barcode = $this->getBarcode($order->getInvoice());
        $html = $this->renderView( 'EcommerceBundle:Order/ecommerce:invoice.html.twig', array(
            'globalOption' => $order->getGlobalOption(),
            'entity' => $order,
            'amountInWords' => $amountInWords,
            'barcode' => $barcode,
            'print' => ''
        ));

        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="online-invoice-'.$order->getInvoice().'.pdf"');
        echo $pdf;
        return new Response('');

    }

    public function printAction(Order $order)
    {
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($order->getGrandTotalAmount());
        $barcode = $this->getBarcode($order->getInvoice());
        return $this->render('EcommerceBundle:Order:invoice.html.twig', array(
            'globalOption' => $order->getGlobalOption(),
            'entity' => $order,
            'amountInWords' => $amountInWords,
            'barcode' => $barcode,
            'print' => ''
        ));

    }

    public function downloadAttachFileAction(Order $order)
    {

        $file = $order->getWebPath();
        if (file_exists($file))
        {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }
    }

    public function orderCountsAction()
    {
        $count = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->orderCounts();
        return new Response($count);

    }

    public function orderCountsByProcessAction()
    {
        $counts = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->orderCountByProcess();
        $data = "";
        $data .= "<ul class='order-list'>";
        foreach ($counts as $count){
            $data .= "<li><a href='/e-commerce/order/?process={$count['process']}'>{$count['process']}-{$count['countId']}</a></li>";
        }
        $data .= "</ul>";
        return new Response($data);

    }


}
