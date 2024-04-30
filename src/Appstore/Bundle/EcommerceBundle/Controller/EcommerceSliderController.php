<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\EcommerceSlider;
use Appstore\Bundle\EcommerceBundle\Form\EcommerceSliderType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Symfony\Component\HttpFoundation\Request;


/**
 * EcommerceSlider controller.
 *
 */
class EcommerceSliderController extends Controller
{

    /**
     * Lists all EcommerceSlider entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('EcommerceBundle:EcommerceSlider')->findBy(array('ecommerceConfig'=> $globalOption->getEcommerceConfig()));

        return $this->render('EcommerceBundle:EcommerceSlider:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new EcommerceSlider entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new EcommerceSlider();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $entity->setEcommerceConfig($user->getGlobalOption()->getEcommerceConfig());
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ecommerceslider'));
        }

        return $this->render('EcommerceBundle:EcommerceSlider:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a EcommerceSlider entity.
     *
     * @param EcommerceSlider $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EcommerceSlider $entity)
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new EcommerceSliderType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('ecommerceslider_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new EcommerceSlider entity.
     *
     */
    public function newAction()
    {
        $entity = new EcommerceSlider();
        $form   = $this->createCreateForm($entity);

        return $this->render('EcommerceBundle:EcommerceSlider:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a EcommerceSlider entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:EcommerceSlider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EcommerceSlider entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EcommerceBundle:EcommerceSlider:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing EcommerceSlider entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:EcommerceSlider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EcommerceSlider entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EcommerceBundle:EcommerceSlider:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a EcommerceSlider entity.
     *
     * @param EcommerceSlider $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EcommerceSlider $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new EcommerceSlider($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('ecommerceslider_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing EcommerceSlider entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:EcommerceSlider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EcommerceSlider entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload()){
                $entity->removeUpload();
            }

            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('ecommerceslider_edit', array('id' => $id)));
        }

        return $this->render('EcommerceBundle:EcommerceSlider:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a EcommerceSlider entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('EcommerceBundle:EcommerceSlider')->findOneBy(array('globalOption'=>$globalOption,'id'=>$id));
        if (!empty($entity)) {

            $entity->removeUpload();
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Status has been deleted successfully"
            );
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',"Sorry! Data not deleted"
            );
        }
        return $this->redirect($this->generateUrl('ecommerceslider'));
    }

    /**
     * Creates a form to delete a EcommerceSlider entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ecommerceslider_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    /**
     * Status a news entity.
     *
     */
    public function statusAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:EcommerceSlider')->find($id);

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
            'error',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('ecommerceslider'));
    }

    public function sortingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $entities = $em->getRepository('EcommerceBundle:EcommerceSlider')->findBy(array('user'=>$user),array('sorting'=>'asc'));

        return $this->render('EcommerceBundle:EcommerceSlider:sorting.html.twig', array(
            'entities' => $entities,

        ));

    }

    public function sortedAction(Request $request)
    {
        $data = $request ->request->get('menuItem');
        $this->getDoctrine()->getRepository('EcommerceBundle:EcommerceSlider')->setMenuOrdering($data);
        exit;

    }
}
