<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\DeliveryLocation;
use Appstore\Bundle\EcommerceBundle\Form\DeliveryLocationType;

/**
 * DeliveryLocation controller.
 *
 */
class DeliveryLocationController extends Controller
{

    /**
     * Lists all DeliveryLocation entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
	    $config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('EcommerceBundle:DeliveryLocation')->findBy(array('ecommerceConfig' => $config),array('name'=>'ASC'));
        return $this->render('EcommerceBundle:DeliveryLocation:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new DeliveryLocation entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new DeliveryLocation();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
	    $config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setEcommerceConfig($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_location'));
        }

        return $this->render('EcommerceBundle:DeliveryLocation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a DeliveryLocation entity.
     *
     * @param DeliveryLocation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DeliveryLocation $entity)
    {

	    $form = $this->createForm(new DeliveryLocationType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_location_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new DeliveryLocation entity.
     *
     */
    public function newAction()
    {
        $entity = new DeliveryLocation();
        $form   = $this->createCreateForm($entity);

        return $this->render('EcommerceBundle:DeliveryLocation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing DeliveryLocation entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DeliveryLocation entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('EcommerceBundle:DeliveryLocation:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a DeliveryLocation entity.
    *
    * @param DeliveryLocation $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(DeliveryLocation $entity)
    {

	    $form = $this->createForm(new DeliveryLocationType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_location_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing DeliveryLocation entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DeliveryLocation entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_location'));
        }

        return $this->render('EcommerceBundle:DeliveryLocation:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a DeliveryLocation entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
	    $em = $this->getDoctrine()->getManager();
	    $entity = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($id);

	    if (!$entity) {
		    throw $this->createNotFoundException('Unable to find DeliveryLocation entity.');
	    }

	    $em->remove($entity);
	    $em->flush();

        return $this->redirect($this->generateUrl('ecommerce_location'));
    }

    

    /**
     * Status a DeliveryLocation entity.
     *
     */
    public function statusAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($id);

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
        return $this->redirect($this->generateUrl('ecommerce_location'));
    }

}
