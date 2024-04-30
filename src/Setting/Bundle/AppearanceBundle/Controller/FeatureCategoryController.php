<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Setting\Bundle\AppearanceBundle\Entity\FeatureCategory;
use Setting\Bundle\AppearanceBundle\Form\FeatureCategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * FeatureCategory controller.
 *
 */
class FeatureCategoryController extends Controller
{

    /**
     * Lists all FeatureCategory entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingAppearanceBundle:FeatureCategory')->findBy(array('globalOption'=> $globalOption));

        return $this->render('SettingAppearanceBundle:FeatureCategory:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new FeatureCategory entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new FeatureCategory();
        $global = $this->getUser()->getGlobalOption();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $category = $entity->getCategory();
        $exist = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureCategory')->findOneBy(array('globalOption'=>$category,'category'=>$category));
        if ($form->isValid() and empty($exist) ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($global);
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('featurecategory'));
        }

        return $this->render('SettingAppearanceBundle:FeatureCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a FeatureCategory entity.
     *
     * @param FeatureCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FeatureCategory $entity)
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new FeatureCategoryType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('featurecategory_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new FeatureCategory entity.
     *
     */
    public function newAction()
    {
        $entity = new FeatureCategory();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingAppearanceBundle:FeatureCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a FeatureCategory entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:FeatureCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingAppearanceBundle:FeatureCategory:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing FeatureCategory entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:FeatureCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureCategory entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingAppearanceBundle:FeatureCategory:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a FeatureCategory entity.
     *
     * @param FeatureCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(FeatureCategory $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new FeatureCategoryType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('featurecategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing FeatureCategory entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingAppearanceBundle:FeatureCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureCategory entity.');
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
            return $this->redirect($this->generateUrl('featurecategory'));
        }

        return $this->render('SettingAppearanceBundle:FeatureCategory:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a FeatureCategory entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('AppearanceBundle:FeatureCategory')->findOneBy(array('globalOption'=>$globalOption,'id'=>$id));
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
        return $this->redirect($this->generateUrl('featurecategory'));
    }

    /**
     * Creates a form to delete a FeatureCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('featurecategory_delete', array('id' => $id)))
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
        $entity = $em->getRepository('SettingAppearanceBundle:FeatureCategory')->find($id);

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
        return $this->redirect($this->generateUrl('featurecategory'));
    }


    public function sortedAction(Request $request)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureCategory')->setDivOrdering($data);
        exit;

    }

    public function resizeAction(Request $request)
    {
        $data = $request ->request->all();
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureCategory')->setDivResize($data);
        exit;
    }
}
