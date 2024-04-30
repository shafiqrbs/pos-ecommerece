<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;


use Appstore\Bundle\EcommerceBundle\Form\CustomCategoryType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Product\Bundle\ProductBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Category controller.
 *
 */
class CustomCategoryController extends Controller
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
     *
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_MANAGER,ROLE_DOMAIN")
     */


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('ProductProductBundle:Category')->findWithEcommerceSearch($config,$data);
        $pagination = $this->paginate($entities);
        return $this->render('EcommerceBundle:CustomCategory:index.html.twig', array(
            'pagination' => $pagination,
            'searchForm' => $data,
        ));

    }

    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_MANAGER,ROLE_DOMAIN")
     */

    public function createAction(Request $request)
    {
        $entity = new Category();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config =  $this->getUser()->getGlobalOption()->getEcommerceConfig();
            $entity->setEcommerceConfig($config);
            $invent =  $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity->setInventoryConfig($invent);
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $entity->setInventoryConfig($this->getUser()->getGlobalOption()->getInventoryConfig());
            $entity->setStatus(true);
            $entity->setPermission('private');
            $entity->upload();
            $em->persist($entity);
            try {
                $em->flush();
                return $this->redirect($this->generateUrl('ecommerce_category_new'));

            }catch (ForeignKeyConstraintViolationException $e) {
                $this->get('session')->getFlashBag()->add(
                'notice',"This category {$entity->getName()} is already exist"
                );
            }catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add(
            'notice', "This category {$entity->getName()} is already exist"
                );
            }
            return $this->render('EcommerceBundle:CustomCategory:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
            ));

        }

        return $this->render('EcommerceBundle:CustomCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Category entity.
     *
     * @param Category $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Category $entity)
    {

        $config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new CustomCategoryType($config,$em), $entity, array(
            'action' => $this->generateUrl('ecommerce_category_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_MANAGER,ROLE_DOMAIN")
     */

    public function newAction()
    {
        $entity = new Category();
        $form   = $this->createCreateForm($entity);
        return $this->render('EcommerceBundle:CustomCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Category entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProductProductBundle:Category')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        return $this->render('EcommerceBundle:CustomCategory:show.html.twig', array(
            'entity'      => $entity
        ));
    }

    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_MANAGER,ROLE_DOMAIN")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProductProductBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('EcommerceBundle:CustomCategory:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Category entity.
     *
     * @param Category $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Category $entity)
    {

        $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new CustomCategoryType($inventory,$em), $entity, array(
            'action' => $this->generateUrl('ecommerce_category_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_MANAGER,ROLE_DOMAIN")
     */

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProductProductBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload()){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();
            return $this->redirect($this->generateUrl('ecommerce_category'));
        }

        return $this->render('EcommerceBundle:CustomCategory:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_MANAGER,ROLE_DOMAIN")
     */

    public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		$entity = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->findOneBy(array('ecommerceConfig' => $config,'id' => $id));

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Brand entity.');
		}

		try {

			$em->remove($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'error',"Data has been deleted successfully"
			);

		} catch (ForeignKeyConstraintViolationException $e) {
			$this->get('session')->getFlashBag()->add(
				'notice',"Data has been relation another Table"
			);
		}catch (\Exception $e) {
			$this->get('session')->getFlashBag()->add(
				'notice', 'Please contact system administrator further notification.'
			);
		}

		return $this->redirect($this->generateUrl('ecommerce_category'));
	}

    public function statusAction(Category $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $em = $this->getDoctrine()->getManager();
        $status = $entity->getStatus();
        if ($inventory != $entity->getEcommerceConfig()) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        if($status == 1){
            $entity->setStatus(0);
            $this->getDoctrine()->getRepository('EcommerceBundle:Item')->updateCategoryItem($entity,0);
        } else{
            $this->getDoctrine()->getRepository('EcommerceBundle:Item')->updateCategoryItem($entity,1);
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('ecommerce_category'));
    }

    public function featureAction(Category $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $em = $this->getDoctrine()->getManager();
        $status = $entity->isFeature();
        if ($inventory != $entity->getEcommerceConfig()) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        if($status == 1){
            $entity->setFeature(0);
        } else{
            $entity->setFeature(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('ecommerce_category'));
    }




}
