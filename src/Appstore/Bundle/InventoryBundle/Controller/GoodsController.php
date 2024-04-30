<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\GoodsItem;
use Appstore\Bundle\InventoryBundle\Entity\ItemGallery;
use Appstore\Bundle\InventoryBundle\Entity\ItemKeyValue;
use Appstore\Bundle\InventoryBundle\Entity\Product;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Form\EcommerceProductEditType;
use Appstore\Bundle\InventoryBundle\Form\EcommerceProductSubItemType;
use Appstore\Bundle\InventoryBundle\Form\EcommerceProductType;
use Appstore\Bundle\InventoryBundle\Form\InventoryGoodsType;
use Appstore\Bundle\InventoryBundle\Form\MasterProductType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;

/**
 * PurchaseVendorItem controller.
 *
 */
class GoodsController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Lists all PurchaseVendorItem entities.
     *
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_PRODUCT,ROLE_DOMAIN")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $getEcommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('InventoryBundle:PurchaseVendorItem')->findGoodsWithSearch($inventory,$data);
        $pagination = $this->paginate($entities);
        $promotions = $this->getDoctrine()->getRepository('EcommerceBundle:Promotion')->findBy(array('ecommerceConfig'=>$getEcommerceConfig,'status'=>1,'type'=>'Promotion'));
        return $this->render('InventoryBundle:Goods:index.html.twig', array(
            'promotions' => $promotions,
            'entities' => $pagination,
        ));
    }
    /**
     * Creates a new PurchaseVendorItem entity.
     *
     *
     */
    public function createAction(Request $request)
    {
        $entity = new PurchaseVendorItem();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setInventoryConfig($inventory);
            $entity->setMasterQuantity($entity->getQuantity());
            $entity->setSource('goods');
            $entity->setSubProduct(true);
            $entity->setIsWeb(true);
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository('InventoryBundle:GoodsItem')->initialInsertSubProduct($entity);
            return $this->redirect($this->generateUrl('inventory_goods_edit',array('id'=>$entity->getId())));
        }
        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $master = new Product();
        $masterForm   = $this->inventoryMasterProductForm($master);
        return $this->render('InventoryBundle:Goods:new.html.twig', array(
            'entity' => $entity,
            'ecommerceConfig' => $ecommerceConfig,
            'form'   => $form->createView(),
            'masterForm'   => $masterForm->createView(),
        ));
    }

    public function checkItemQuantity(Purchase $purchase, $vendorQnt)
    {
        $item = $purchase->getTotalItem();
        $quantity = $purchase->getTotalQnt();
        $vendorItem = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->getPurchaseVendorItemQuantity($purchase);
        $totalQnt = ($vendorItem['totalQnt'] + $vendorQnt);
        $totalItem = ($vendorItem['totalItem'] + 1);
        if($totalQnt > $quantity or $totalItem > $item ){
            return false;
        }else{
            return true;
        }

    }

    /**
     * Creates a form to create a PurchaseVendorItem entity.
     *
     * @param PurchaseVendorItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PurchaseVendorItem $entity)
    {
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new EcommerceProductType($em,$inventoryConfig), $entity, array(
            'action' => $this->generateUrl('inventory_goods_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'action',
                'novalidate' => 'novalidate',
            )


        ));
        return $form;
    }

    /**
     * Creates a form to edit a PurchaseVendorItem entity.
     *
     * @param PurchaseVendorItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function inventoryMasterProductForm(Product $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new MasterProductType($em,$inventory), $entity, array(
            'action' => $this->generateUrl('inventory_product_masteritem_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'id' => 'masterProduct',
                'class' => 'action',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to create a new PurchaseVendorItem entity.
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_PRODUCT,ROLE_DOMAIN")
     */
    public function newAction()
    {
        $entity = new PurchaseVendorItem();
        $form   = $this->createCreateForm($entity);
        $master = new Product();
        $masterForm   = $this->inventoryMasterProductForm($master);
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        return $this->render('InventoryBundle:Goods:new.html.twig', array(
            'entity' => $entity,
            'inventoryConfig' => $inventoryConfig,
            'form'   => $form->createView(),
            'masterForm'   => $masterForm->createView(),
        ));
    }

    /**
     * Finds and displays a PurchaseVendorItem entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseVendorItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseVendorItem entity.');
        }

        return $this->render('InventoryBundle:Goods:show.html.twig', array(
            'entity'      => $entity,
        ));
    }
    /**
     * Displays a form to edit an existing PurchaseVendorItem entity.
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_PRODUCT,ROLE_DOMAIN")
     */

    public function editAction(PurchaseVendorItem $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $goodsItem = new GoodsItem();
        $goodsItemForm = $this->createSubItemForm($goodsItem,$entity);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseVendorItem entity.');
        }
        if($entity->getSource() != 'inventory'){
            $editForm = $this->createEditForm($entity);
            $twig = 'edit';
        }else{
            $editForm = $this->inventoryEditForm($entity);
            $twig = 'inventoryEdit';
        }
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        return $this->render('InventoryBundle:Goods:'.$twig.'.html.twig', array(
            'entity'        => $entity,
            'inventoryConfig' => $inventoryConfig,
            'form'          => $editForm->createView(),
            'goodsItemForm'          => $goodsItemForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a PurchaseVendorItem entity.
     *
     * @param PurchaseVendorItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PurchaseVendorItem $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new EcommerceProductEditType($em,$inventory), $entity, array(
            'action' => $this->generateUrl('inventory_goods_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'action',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Creates a form to edit a PurchaseVendorItem entity.
     *
     * @param PurchaseVendorItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createSubItemForm(GoodsItem $entity, PurchaseVendorItem $purchaseVendorItem)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getRepository('InventoryBundle:ItemSize');
        $form = $this->createForm(new EcommerceProductSubItemType($em,$inventory), $entity, array(
            'action' => $this->generateUrl('inventory_vendoritem_subproduct', array('id' => $purchaseVendorItem->getId())),
            'method' => 'PUT',
            'attr' => array(
                'id' => 'subItemForm',
                'class' => 'action',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Creates a form to edit a PurchaseVendorItem entity.
     *
     * @param PurchaseVendorItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function inventoryEditForm(PurchaseVendorItem $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new InventoryGoodsType($em,$inventory), $entity, array(
            'action' => $this->generateUrl('inventory_goods_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'action',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Edits an existing PurchaseVendorItem entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em->getRepository('InventoryBundle:PurchaseVendorItem')->find($id);
        $goodsItem = new GoodsItem();
        $goodsItemForm = $this->createSubItemForm($goodsItem,$entity);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseVendorItem entity.');
        }

        if($entity->getSource() != 'inventory'){
            $editForm = $this->createEditForm($entity);
            $twig = 'edit';
        }else{
            $editForm = $this->inventoryEditForm($entity);
            $twig = 'inventoryEdit';
        }
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if($entity->upload() && !empty($entity->getFile())){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();
            $this->getDoctrine()->getRepository('InventoryBundle:ItemMetaAttribute')->insertProductAttribute($entity,$data);
            $this->getDoctrine()->getRepository('InventoryBundle:ItemKeyValue')->insertItemKeyValue($entity,$data);
            $this->getDoctrine()->getRepository('InventoryBundle:ItemGallery')->insertProductGallery($entity,$data);
            $this->getDoctrine()->getRepository('InventoryBundle:GoodsItem')->initialUpdateSubProduct($entity);
            $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->updateMasterProductQuantity($entity);
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('inventory_goods_edit', array('id' => $id)));
        }
        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        return $this->render('InventoryBundle:Goods:'.$twig.'.html.twig', array(
            'entity'                => $entity,
            'goodsItemForm'         => $goodsItemForm->createView(),
            'ecommerceConfig'       => $ecommerceConfig,
            'form'                  => $editForm->createView(),
        ));
    }

    public function addSubProductAction(Request $request, PurchaseVendorItem $entity)
    {

        $data = $request->request->all();
        $this->getDoctrine()->getRepository('InventoryBundle:GoodsItem')->insertSubProduct($entity,$data);
        $subItem = $this->renderView( 'InventoryBundle:Goods:subItem.html.twig', array(
            'entity'           => $entity,
        ));
        return new Response($subItem);

    }

    public function updateSubProductAction(Request $request, GoodsItem $entity)
    {

        $data = $request->request->all();
        $this->getDoctrine()->getRepository('InventoryBundle:GoodsItem')->updateSubProduct($entity,$data);
        return new Response('success');

    }


    /**
     * Deletes a PurchaseVendorItem entity.
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_PRODUCT,ROLE_DOMAIN")
     */
    public function deleteAction(PurchaseVendorItem $vendorItem)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$vendorItem) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        try {
            $em = $this->getDoctrine()->getManager();
            $vendorItem->deleteImageDirectory();
            $em->remove($vendorItem);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );
            return new Response('success');

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
            return new Response('failed');
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
            return new Response('failed');
        }
        exit;

    }

    /**
     * Status a PurchaseVendorItem entity.
     *
     */
    public function webStatusAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:PurchaseVendorItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('inventory_goods'));
    }



    public function uploadItemImageAction(PurchaseVendorItem $item)
    {
        $entity = new ItemGallery();
        $option = $this->getUser()->getGlobalOption();
        $entity ->upload($option->getId(),$item->getId());
    }

    public function subItemDeleteAction(GoodsItem $goodsItem)
    {
        if($goodsItem){
            $em = $this->getDoctrine()->getManager();
            $em->remove($goodsItem);
            $em->flush();
            return new Response('success');
        }else{
            return new Response('failed');
        }
    }

    public function keyValueDeleteAction(ItemKeyValue $itemKeyValue)
    {
        if($itemKeyValue){
            $em = $this->getDoctrine()->getManager();
            $em->remove($itemKeyValue);
            $em->flush();
            return new Response('success');
        }else{
            return new Response('failed');
        }
    }

    public function itemCopyAction(PurchaseVendorItem $copyEntity)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new PurchaseVendorItem();
        $entity->setMasterItem($copyEntity->getMasterItem());
        $entity->setName($copyEntity->getName());
        $entity->setWebName($copyEntity->getWebName());
        $entity->setSubProduct(true);
        $entity->setQuantity($copyEntity->getQuantity());
        $entity->setMasterQuantity($copyEntity->getMasterQuantity());
        $entity->setPurchasePrice($copyEntity->getPurchasePrice());
        $entity->setSalesPrice($copyEntity->getSalesPrice());
        $entity->setOverHeadCost($copyEntity->getOverHeadCost());
        $entity->setSize($copyEntity->getSize());
        $entity->setItemColors($copyEntity->getItemColors());
        $entity->setBrand($copyEntity->getBrand());
        $entity->setDiscount($copyEntity->getDiscount());
        $entity->setDiscountPrice($copyEntity->getDiscountPrice());
        $entity->setContent($copyEntity->getContent());
        $entity->setTag($copyEntity->getTag());
        $entity->setPromotion($copyEntity->getPromotion());
        $entity->setCountry($copyEntity->getCountry());
        $entity->setInventoryConfig($copyEntity->getInventoryConfig());
        $entity->setProductUnit($copyEntity->getProductUnit());
        $entity->setWarningLabel($copyEntity->getWarningLabel());
        $entity->setWarningText($copyEntity->getWarningText());
        $entity->setIsWeb(true);
        $entity->setStatus(true);
        $entity->setSource('goods');
        $em->persist($entity);
        $em->flush();
     //   $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->insertCopyPurchaseItem($entity,$item);
        $this->getDoctrine()->getRepository('InventoryBundle:ItemMetaAttribute')->insertCopyProductAttribute($entity,$copyEntity);
        $this->getDoctrine()->getRepository('InventoryBundle:ItemKeyValue')->insertCopyItemKeyValue($entity,$copyEntity);
        $this->getDoctrine()->getRepository('InventoryBundle:GoodsItem')->insertCopySubProduct($entity,$copyEntity);
        return $this->redirect($this->generateUrl('inventory_goods_edit', array('id' => $entity->getId())));


    }

    public function discountSelectAction()
    {
        $getEcommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Discount')->findBy(
            array('ecommerceConfig'=>$getEcommerceConfig,'status'=>1)
        );
        $type = '';
        $items = array();
        $items[]=array('value' => '','text'=> '---add discount---');
        foreach ($entities as $entity):
            if($entity->getType() == "percentage"){
                $type ='%';
            }
            $items[]=array('value' => $entity->getId(),'text'=> $entity->getName().'('.$entity->getDiscountAmount().')'.$type);
        endforeach;
        return new JsonResponse($items);


    }

    public function tagSelectAction()
    {
        $getEcommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Promotion')->getTypeBasePromotion($getEcommerceConfig->getId(),'Promotion');
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('value' => $entity->getId(),'text'=> $entity->getName());
        endforeach;
        return new JsonResponse($items);


    }

    public function inlineItemUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:PurchaseVendorItem')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $setName = 'set'.$data['name'];
        if($data['name'] == 'Discount'){

            $discount = $em->getRepository('EcommerceBundle:Discount')->find($data['value']);
            $discountPrice = $em->getRepository('InventoryBundle:PurchaseVendorItem')->getCulculationDiscountPrice($entity,$discount);
            $entity->setDiscountPrice($discountPrice);
            $em->getRepository('InventoryBundle:GoodsItem')->subItemDiscountPrice($entity,$discount);
            $entity->$setName($discount);
        }elseif($data['name'] == 'Promotion'){

            $setValue = $em->getRepository('EcommerceBundle:Promotion')->find($data['value']);
            $entity->$setName($setValue);

        }else{

            $entity->$setName($data['value']);
            if(!empty($entity->getDiscount())){
                $discountPrice = $em->getRepository('InventoryBundle:PurchaseVendorItem')->getCulculationDiscountPrice($entity,$entity->getDiscount());
                $entity->setDiscountPrice($discountPrice);
                $em->getRepository('InventoryBundle:GoodsItem')->subItemDiscountPrice($entity,$entity->getDiscount());
            }

        }
        $em->persist($entity);
        $em->flush($entity);
        exit;

    }

    public function inlineSubItemUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:GoodsItem')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $setName = 'set'.$data['name'];
        $setValue = $data['value'];
        $entity->$setName($setValue);
        $em->persist($entity);
        $em->flush($entity);
        exit;

    }

    public function keyValueSortedAction(Request $request)
    {
        $data = $request ->request->get('menuItem');
        $this->getDoctrine()->getRepository('InventoryBundle:ItemKeyValue')->setDivOrdering($data);
        exit;

    }


}
