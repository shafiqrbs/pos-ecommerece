<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Setting\Bundle\AppearanceBundle\Entity\FeatureBrand;
use Setting\Bundle\AppearanceBundle\Form\FeatureBrandType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;

/**
 * FeatureBrand controller.
 *
 */
class FeatureBrandController extends Controller
{

    /**
     * Lists all FeatureBrand entities.
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingAppearanceBundle:FeatureBrand')->findBy(array('globalOption'=> $globalOption));

        return $this->render('SettingAppearanceBundle:FeatureBrand:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new FeatureBrand entity.
     * @Secure(roles="ROLE_DOMAIN_ECOMMERCE_CONFIG")
     */

    public function createAction(Request $request)
    {
        $entity = new FeatureBrand();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $entity->setGlobalOption($user->getGlobalOption());
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('featurebrand'));
        }

        return $this->render('SettingAppearanceBundle:FeatureBrand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a FeatureBrand entity.
     *
     * @param FeatureBrand $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FeatureBrand $entity)
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new FeatureBrandType($globalOption), $entity, array(
            'action' => $this->generateUrl('featurebrand_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new FeatureBrand entity.
     * @Secure(roles="ROLE_DOMAIN_ECOMMERCE_CONFIG")
     */
    public function newAction()
    {
        $entity = new FeatureBrand();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingAppearanceBundle:FeatureBrand:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a FeatureBrand entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:FeatureBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureBrand entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingAppearanceBundle:FeatureBrand:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing FeatureBrand entity.
     * @Secure(roles="ROLE_DOMAIN_ECOMMERCE_CONFIG")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:FeatureBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureBrand entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingAppearanceBundle:FeatureBrand:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a FeatureBrand entity.
     *
     * @param FeatureBrand $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(FeatureBrand $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new FeatureBrandType($globalOption), $entity, array(
            'action' => $this->generateUrl('featurebrand_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing FeatureBrand entity.
     * @Secure(roles="ROLE_DOMAIN_ECOMMERCE_CONFIG")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingAppearanceBundle:FeatureBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureBrand entity.');
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
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('featurebrand'));
        }

        return $this->render('SettingAppearanceBundle:FeatureBrand:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a FeatureBrand entity.
     * @Secure(roles="ROLE_DOMAIN_ECOMMERCE_CONFIG")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('AppearanceBundle:FeatureBrand')->findOneBy(array('globalOption'=>$globalOption,'id'=>$id));
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
        return $this->redirect($this->generateUrl('featurebrand'));
    }

    /**
     * Creates a form to delete a FeatureBrand entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('featurebrand_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
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
        $entity = $em->getRepository('SettingAppearanceBundle:FeatureBrand')->find($id);

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
        return $this->redirect($this->generateUrl('featurebrand'));
    }



}
