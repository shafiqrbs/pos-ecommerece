<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Coupon;
use Appstore\Bundle\EcommerceBundle\Form\CouponType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;


/**
 * ItemSize controller.
 *
 */
class CouponController extends Controller
{

    /**
     * Lists all ItemSize entities.
     * @Secure(roles="ROLE_DOMAIN_ECOMMERCE_PRODUCT,ROLE_DOMAIN,ROLE_DOMAIN")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('EcommerceBundle:Coupon')->findBy(array('ecommerceConfig' => $inventory),array('updated'=>'desc'));
        return $this->render('EcommerceBundle:Coupon:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ItemSize entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Coupon();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
            $entity->setEcommerceConfig($inventory);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_coupon'));
        }

        return $this->render('EcommerceBundle:Coupon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ItemSize entity.
     *
     * @param  $entity Coupon entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Coupon $entity)
    {
        $form = $this->createForm(new CouponType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_coupon_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ItemSize entity.
     * @Secure(roles="ROLE_DOMAIN_ECOMMERCE_PRODUCT,ROLE_DOMAIN,ROLE_DOMAIN")
     */

    public function newAction()
    {
        $entity = new Coupon();
        $form   = $this->createCreateForm($entity);

        return $this->render('EcommerceBundle:Coupon:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ItemSize entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemSize entity.');
        }

        return $this->render('EcommerceBundle:Coupon:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing ItemSize entity.
     * @Secure(roles="ROLE_DOMAIN_ECOMMERCE_PRODUCT,ROLE_DOMAIN")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemSize entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('EcommerceBundle:Coupon:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a ItemSize entity.
     *
     * @param ItemSize $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Coupon $entity)
    {
        $form = $this->createForm(new CouponType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_coupon_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing ItemSize entity.
     * @Secure(roles="ROLE_DOMAIN_ECOMMERCE_PRODUCT,ROLE_DOMAIN")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemSize entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_coupon_edit', array('id' => $id)));
        }

        return $this->render('EcommerceBundle:Coupon:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a ItemSize entity.
     * @Secure(roles="ROLE_DOMAIN_ECOMMERCE_PRODUCT,ROLE_DOMAIN")
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemSize entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('ecommerce_coupon'));
    }

  
}
