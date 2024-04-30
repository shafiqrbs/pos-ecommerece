<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Setting\Bundle\AppearanceBundle\Entity\FeatureWidget;
use Setting\Bundle\AppearanceBundle\Form\FeatureWidgetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;


/**
 * FeatureWidget controller.
 *
 */
class FeatureWidgetController extends Controller
{

    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_WEDGET,ROLE_DOMAIN_WEBSITE_WEDGET,ROLE_DOMAIN")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingAppearanceBundle:FeatureWidget')->findBy(array('globalOption'=> $globalOption,'widgetFor'=>'e-commerce'));
        return $this->render('SettingAppearanceBundle:FeatureWidget:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all FeatureWidget entities.
     *
     */
    public function sortingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingAppearanceBundle:FeatureWidget')->findBy(array('globalOption'=> $globalOption,'widgetFor'=>'e-commerce'),array('sorting'=>'ASC'));
        return $this->render('SettingAppearanceBundle:FeatureWidget:sorting.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all FeatureWidget entities.
     *
     */
    public function sortedListAction(Request $request)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->setListOrdering($data);
        exit;
    }


    /**
     * Creates a new FeatureWidget entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new FeatureWidget();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $entity->setGlobalOption($user->getGlobalOption());
            $entity->setPageName($entity->getMenu()->getMenu());
            $name = $entity->getPageName().'-'.$entity->getPosition();
            $entity->setName($name);
            $entity->setWidgetFor('e-commerce');
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidgetItem')->insert($entity,$data);
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            if(!empty($entity->getJsFeature()) and $entity->getJsFeature()->getSlug() == 'feature'){
                return $this->redirect($this->generateUrl('appearancefeaturewidget_feature',array('id'=>$entity->getId())));
            }else{
                return $this->redirect($this->generateUrl('appearancefeaturewidget'));
            }

        }
        $global = $this->getUser()->getGlobalOption();
        $features = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Feature')->findBy(array('globalOption' => $global));
        return $this->render('SettingAppearanceBundle:FeatureWidget:new.html.twig', array(
            'entity' => $entity,
            'features' => $features,
            'featureIds'      => '',
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a FeatureWidget entity.
     *
     * @param FeatureWidget $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FeatureWidget $entity)
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new FeatureWidgetType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('appearancefeaturewidget_create'),
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
        $entity = new FeatureWidget();
        $form   = $this->createCreateForm($entity);
        $global = $this->getUser()->getGlobalOption();
        $features = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Feature')->findBy(array('globalOption' => $global));
        return $this->render('SettingAppearanceBundle:FeatureWidget:new.html.twig', array(
            'entity'    => $entity,
            'features'  => $features,
            'featureIds'      => '',
            'form'      => $form->createView(),
        ));
    }

    /**
     * Finds and displays a FeatureWidget entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:FeatureWidget')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureWidget entity.');
        }

        return $this->render('SettingAppearanceBundle:FeatureWidget:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_WEDGET,ROLE_DOMAIN_WEBSITE_WEDGET,ROLE_DOMAIN")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:FeatureWidget')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureWidget entity.');
        }
        $global = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity);
        $features = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Feature')->findBy(array('globalOption' => $global));
        $featureIds = array();
        foreach ($entity->getFeatureWidgetItems() as $row ){
            $featureIds[] = $row->getFeature()->getId();;
        }
        return $this->render('SettingAppearanceBundle:FeatureWidget:new.html.twig', array(
            'entity'      => $entity,
            'features'      => $features,
            'featureIds'      => $featureIds,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a FeatureWidget entity.
     *
     * @param FeatureWidget $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(FeatureWidget $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new FeatureWidgetType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('appearancefeaturewidget_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing FeatureWidget entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingAppearanceBundle:FeatureWidget')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureWidget entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {

            $entity->setPageName($entity->getMenu()->getMenu());
            $name = $entity->getPageName().'-'.$entity->getPosition();
            $entity->setName($name);
            $em->flush();
            $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidgetItem')->update($entity,$data);
            return $this->redirect($this->generateUrl('appearancefeaturewidget_edit', array('id' => $id)));

        }
        $global = $this->getUser()->getGlobalOption();
        $features = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Feature')->findBy(array('globalOption' => $global));


        return $this->render('SettingAppearanceBundle:FeatureWidget:new.html.twig', array(
            'entity'      => $entity,
            'features'      => $features,
            'featureIds'      => '',
            'form'   => $editForm->createView(),

        ));
    }

    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_WEDGET,ROLE_DOMAIN_WEBSITE_WEDGET,ROLE_DOMAIN")
     */

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('SettingAppearanceBundle:FeatureWidget')->findOneBy(array('globalOption'=>$globalOption,'id'=>$id));
        if (!empty($entity)) {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',"Sorry! Data not deleted"
            );
        }
        return $this->redirect($this->generateUrl('appearancefeaturewidget'));
    }


    /**
     * Status a news entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingAppearanceBundle:FeatureWidget')->find($id);

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
        return $this->redirect($this->generateUrl('appearancefeaturewidget'));
    }

    public function  featureAction(FeatureWidget $entity){

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeatureWidget entity.');
        }
        return $this->render('SettingAppearanceBundle:FeatureWidget:feature.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function sortedAction(Request $request)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->setDivOrdering($data);
        exit;

    }

    public function resizeAction(Request $request)
    {
        $data = $request ->request->all();
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->setDivResize($data);
        exit;
    }
}
