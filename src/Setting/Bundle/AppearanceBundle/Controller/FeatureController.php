<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Setting\Bundle\AppearanceBundle\Entity\Feature;
use Setting\Bundle\AppearanceBundle\Form\FeatureType;
use Setting\Bundle\AppearanceBundle\Form\PageFeatureType;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;


/**
 * Feature controller.
 *
 */
class FeatureController extends Controller
{

    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_WEDGET,ROLE_DOMAIN_WEBSITE_WEDGET,ROLE_DOMAIN")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingAppearanceBundle:Feature')->findBy(array('globalOption'=> $globalOption));

        return $this->render('SettingAppearanceBundle:Feature:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Feature entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Feature();
        $globalOption = $this->getUser()->getGlobalOption();
        $appModules = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getAppmoduleArray($globalOption);
        $result = array_intersect($appModules, array('Ecommerce'));
        if (!empty($result)) {
            $form   = $this->createCreateForm($entity);
            $twig ='new';
        }else{
            $form   = $this->createPageCreateForm($entity);
            $twig ='page';
        }
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $entity->setGlobalOption($user->getGlobalOption());
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('appearancefeature'));
        }
        return $this->render('SettingAppearanceBundle:Feature:'.$twig.'.html.twig', array(

            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Feature entity.
     *
     * @param Feature $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Feature $entity)
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new FeatureType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('appearancefeature_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Creates a form to create a Feature entity.
     *
     * @param Feature $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createPageCreateForm(Feature $entity)
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PageFeatureType($globalOption), $entity, array(
            'action' => $this->generateUrl('appearancefeature_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_WEDGET,ROLE_DOMAIN_WEBSITE_WEDGET,ROLE_DOMAIN")
     */

    public function newAction()
    {
        $entity = new Feature();

        $globalOption = $this->getUser()->getGlobalOption();

        $appModules = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getAppmoduleArray($globalOption);
        $result = array_intersect($appModules, array('Ecommerce'));

        if (!empty($result)) {
            $form   = $this->createCreateForm($entity);
            $twig ='new';

        }else{

            $form   = $this->createPageCreateForm($entity);
            $twig ='page';
        }

        return $this->render('SettingAppearanceBundle:Feature:'. $twig .'.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Feature entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:Feature')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feature entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingAppearanceBundle:Feature:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_WEDGET,ROLE_DOMAIN_WEBSITE_WEDGET,ROLE_DOMAIN")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:Feature')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feature entity.');
        }
        $globalOption = $this->getUser()->getGlobalOption();
        $appModules = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getAppmoduleArray($globalOption);
        $result = array_intersect($appModules, array('Ecommerce'));
        if (!empty($result)) {
            $form   = $this->createEditForm($entity);
            $twig ='new';

        }else{

            $form   = $this->createPageEditForm($entity);
            $twig ='page';
        }
        return $this->render('SettingAppearanceBundle:Feature:'.$twig.'.html.twig', array(
            'entity'        => $entity,
            'form'          => $form->createView(),
        ));
    }


    /**
     * Creates a form to edit a Feature entity.
     *
     * @param Feature $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Feature $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new FeatureType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('appearancefeature_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Creates a form to edit a Feature entity.
     *
     * @param Feature $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createPageEditForm(Feature $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PageFeatureType($globalOption), $entity, array(
            'action' => $this->generateUrl('appearancefeature_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Feature entity.
     *
     */
    public function updateAction(Request $request, Feature $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feature entity.');
        }

        $globalOption = $this->getUser()->getGlobalOption();
        $appModules = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->getAppmoduleArray($globalOption);
        $result = array_intersect($appModules, array('Ecommerce'));
        if (!empty($result)) {
            $form   = $this->createEditForm($entity);
            $twig ='new';

        }else{

            $form   = $this->createPageEditForm($entity);
            $twig ='page';
        }
        $form->handleRequest($request);

        if ($form->isValid()) {

            if($entity->upload()){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('appearancefeature'));
        }
        return $this->render('SettingAppearanceBundle:Feature:'.$twig.'.html.twig', array(

            'entity'      => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Deletes a Feature entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('SettingAppearanceBundle:Feature')->findOneBy(array('globalOption' => $globalOption,'id'=>$id));
        if (!empty($entity)) {
            if($entity->getFile()){
                $entity->removeUpload();
            }
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been deleted successfully"
            );
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',"Sorry! Data not deleted"
            );
        }
        return $this->redirect($this->generateUrl('appearancefeature'));
    }

    /**
     * Creates a form to delete a Feature entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('appearancefeature_delete', array('id' => $id)))
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
        $entity = $em->getRepository('SettingAppearanceBundle:Feature')->find($id);

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
        return $this->redirect($this->generateUrl('appearancefeature'));
    }


    public function sortedAction(Request $request)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:Feature')->setDivOrdering($data);
        exit;

    }

    public function resizeAction(Request $request)
    {
        $data = $request ->request->all();
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:Feature')->setDivResize($data);
        exit;
    }
}
