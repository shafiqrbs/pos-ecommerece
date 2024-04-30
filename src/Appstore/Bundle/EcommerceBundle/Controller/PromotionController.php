<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Promotion;
use Appstore\Bundle\EcommerceBundle\Form\PromotionType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Promotion controller.
 *
 */
class PromotionController extends Controller
{

    /**
     * Lists all Promotion entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('EcommerceBundle:Promotion')->findBy(array('ecommerceConfig'=> $ecommerceConfig),array('name'=>'asc'));

        $entity = new Promotion();
        $form   = $this->createCreateForm($entity);

        return $this->render('EcommerceBundle:Promotion:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Creates a new Promotion entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Promotion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
            $entity->setEcommerceConfig($inventory);
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_promotion'));
        }
        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('EcommerceBundle:Promotion')->findBy(array('ecommerceConfig'=>$ecommerceConfig),array('name'=>'asc'));

        return $this->render('EcommerceBundle:Promotion:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Promotion entity.
     *
     * @param Promotion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Promotion $entity)
    {
        $form = $this->createForm(new PromotionType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_promotion_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing PreOrder entity.
     *
     */
    public function editAction(Promotion $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }

        $editForm = $this->createEditForm($entity);
        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('EcommerceBundle:Promotion')->findBy(array('ecommerceConfig'=>$ecommerceConfig),array('name'=>'asc'));

        return $this->render('EcommerceBundle:Promotion:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a PreOrder entity.
     *
     * @param Promotion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Promotion $entity)
    {
        $form = $this->createForm(new PromotionType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_promotion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Edits an existing PreOrder entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:Promotion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }


        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload()){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('ecommerce_promotion'));
        }

        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('EcommerceBundle:Promotion')->findBy(array('ecommerceConfig'=>$ecommerceConfig),array('name'=>'asc'));

        return $this->render('EcommerceBundle:Promotion:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        ));
    }



    /**
     * Deletes a Promotion entity.
     *
     */
    public function deleteAction( Promotion $entity)
    {

        $em = $this->getDoctrine()->getManager();
        try {
            $entity->removeUpload();
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }
        return $this->redirect($this->generateUrl('ecommerce_promotion'));

    }

    public function statusAction(Promotion $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $em = $this->getDoctrine()->getManager();
        $status = $entity->getStatus();
        if ($inventory != $entity->getEcommerceConfig()) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        if($entity->getStatus() != 1){
            $this->getDoctrine()->getRepository('EcommerceBundle:Item')->removePromotion($inventory->getId(),$entity->getId());
        }
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('ecommerce_promotion'));
    }


    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('EcommerceBundle:Promotion')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchPromotionNameAction($promotion)
    {
        return new JsonResponse(array(
            'id'=>$promotion,
            'text'=>$promotion
        ));
    }
}
