<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Appstore\Bundle\EcommerceBundle\Entity\ItemSub;
use Appstore\Bundle\EcommerceBundle\Entity\ItemGallery;
use Appstore\Bundle\EcommerceBundle\Entity\ItemKeyValue;
use Appstore\Bundle\EcommerceBundle\Form\EcommerceProductEditType;
use Appstore\Bundle\EcommerceBundle\Form\EcommerceProductSubItemType;
use Appstore\Bundle\EcommerceBundle\Form\EcommerceProductType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;

/**
 * Item controller.
 *
 */
class EcommerceItemController extends Controller
{

	public function paginate($entities)
	{
		$paginator = $this->get('knp_paginator');
		$pagination = $paginator->paginate(
			$entities,
			$this->get('request')->query->get('page', 1)/*page number*/,
			25  /*limit per page*/
		);
		$pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
		return $pagination;
	}


	/**
	 * Lists all Item entities.
	 *
	 * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE,ROLE_DOMAIN")
	 */

	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		$entities = $em->getRepository('EcommerceBundle:Item')->findGoodsWithSearch($config,$data);
		$pagination = $this->paginate($entities);
		$promotions = $this->getDoctrine()->getRepository('EcommerceBundle:Promotion')->findBy(array('ecommerceConfig'=>$config,'status'=>1,'type'=>'Promotion'));
		return $this->render('EcommerceBundle:Item:index.html.twig', array(
			'promotions' => $promotions,
			'entities' => $pagination,
		));
	}

	/**
	 * Creates a form to create a Item entity.
	 *
	 * @param Item $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createCreateForm(Item $entity)
	{
		$config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		$em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
		$form = $this->createForm(new EcommerceProductType($em,$config), $entity, array(
			'action' => $this->generateUrl('ecommerce_item_create'),
			'method' => 'POST',
			'attr' => array(
				'class' => 'action',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}


	/**
	 * Displays a form to create a new Item entity.
	 * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE,ROLE_DOMAIN")
	 */
	public function newAction()
	{

		$entity = new Item();
		$form   = $this->createCreateForm($entity);
		$config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		return $this->render('EcommerceBundle:Item:new.html.twig', array(
			'entity' => $entity,
			'ecommerceConfig' => $config,
			'form'   => $form->createView(),
		));
	}


	/**
	 * Displays a form to edit an existing Item entity.
	 * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE,ROLE_DOMAIN")
	 */

	public function editAction(Item $entity)
	{
		$em = $this->getDoctrine()->getManager();
		$goodsItem = new ItemSub();
		$goodsItemForm = $this->createSubItemForm($goodsItem,$entity);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Item entity.');
		}

		$editForm = $this->createEditForm($entity);
		$config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		return $this->render('EcommerceBundle:Item:inventoryEdit.html.twig', array(
			'entity'        => $entity,
			'ecommerceConfig' => $config,
			'form'          => $editForm->createView(),
			'goodsItemForm'          => $goodsItemForm->createView(),
		));
	}

	/**
	 * Creates a form to edit a Item entity.
	 *
	 * @param Item $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createEditForm(Item $entity)
	{
		$inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		$em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
		$form = $this->createForm(new EcommerceProductEditType($em,$inventory), $entity, array(
			'action' => $this->generateUrl('ecommerce_item_update', array('id' => $entity->getId())),
			'method' => 'PUT',
			'attr' => array(
				'class' => 'action',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}

	/**
	 * Deletes a Item entity.
	 * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE,ROLE_DOMAIN")
	 */
	public function deleteAction(Item $vendorItem)
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





}
