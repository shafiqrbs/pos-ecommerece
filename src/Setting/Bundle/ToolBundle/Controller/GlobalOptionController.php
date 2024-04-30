<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\SignupType;
use Setting\Bundle\ToolBundle\Form\GlobalOptionModifyType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Setting\Bundle\Tool\Service\RequestManager;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Form\GlobalOptionType;

/**
 * GlobalOption controller.
 *
 */
class GlobalOptionController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            20  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Lists all GlobalOption entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->findAll();
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:GlobalOption:index.html.twig', array(
            'pagination' => $entities,
        ));
    }
    /**
     * Creates a new GlobalOption entity.
     *
     */
    public function createAction(Request $request)
    {

        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $intlMobile = $entity->getProfile()->getMobile();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
        $entity->getProfile()->setMobile($mobile);
        $data = $request->request->all();
        $exist = $this->getDoctrine()->getRepository('UserBundle:User')->checkExistingUser($mobile);
        if ($form->isValid() and $exist == false) {
            $user = $this->getUser();
            $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->createGlobalOption($mobile,$data,$user);
            $entity->setPlainPassword("*148148#");
            $entity->setGlobalOption($globalOption);
            $entity->setEnabled(true);
            $entity->setDomainOwner(true);
            $entity->setUsername($mobile);
            $entity->setEmail($mobile.'@gmail.com');
            $entity->setRoles(array('ROLE_DOMAIN'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->initialSetup($entity);
            $this->get('settong.toolManageRepo')->createDirectory($entity->getGlobalOption()->getId());
          //  $dispatcher = $this->container->get('event_dispatcher');
        //    $dispatcher->dispatch('setting_tool.post.user_signup_msg', new \Setting\Bundle\ToolBundle\Event\UserSignup($entity));
            return $this->redirect($this->generateUrl('globaloption_edit', array('id' => $globalOption->getId())));
        }

        $this->get('session')->getFlashBag()->add(
            'notice',"The mobile no have to be unique"
        );

        return $this->render('SettingToolBundle:GlobalOption:signup.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

    }

    private function createCreateForm(User $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $em = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $form = $this->createForm(new SignupType($em,$location), $entity, array(
            'action' => $this->generateUrl('globaloption_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'id' => 'signup',
                'class' => 'form-horizontal  registration signupForm',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }


    /**
     * Displays a form to create a new GlobalOption entity.
     *
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);
        return $this->render('SettingToolBundle:GlobalOption:signup.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a GlobalOption entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:GlobalOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GlobalOption entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:GlobalOption:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing GlobalOption entity.
     *
     */
    public function editAction(GlobalOption $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GlobalOption entity.');
        }
        $this->get('settong.toolManageRepo')->createDirectory($entity->getId());
        $this->getDoctrine()->getRepository('SettingToolBundle:SiteSetting')->globalOptionSetting($entity);

        $subSyndicates = $this->getSubSyndicateUnderVendor($entity);
        $editForm = $this->createEditForm($entity);

        return $this->render('SettingToolBundle:GlobalOption:new.html.twig', array(
            'entity'      => $entity,
            'subSyndicates'      => $subSyndicates,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a GlobalOption entity.
    *
    * @param GlobalOption $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(GlobalOption $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new GlobalOptionType($location), $entity, array(
            'action' => $this->generateUrl('globaloption_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;

    }



    /**
     * Edits an existing GlobalOption entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:GlobalOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GlobalOption entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->upload();
            $em->flush();
            //$this->getDoctrine()->getRepository('SettingContentBundle:HomePage')->globalOptionHome($user);
            //$this->getDoctrine()->getRepository('SettingContentBundle:ContactPage')->globalOptionContact($user);
            $this->getDoctrine()->getRepository('SettingToolBundle:SiteSetting')->updateSettingMenu($entity);
            $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->systemConfigUpdate($entity);
            $this->get('session')->getFlashBag()->add('success',"Data has been updated successfully");

            /*$referer = $request->headers->get('referer');
            return new RedirectResponse($referer);*/
            return $this->redirect($this->generateUrl('globaloption_edit', array('id' => $entity->getId())));


        }
        return $this->render('SettingToolBundle:GlobalOption:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));


    }
    /**
     * Deletes a GlobalOption entity.
     *
     */
    public function deleteAction(GlobalOption $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GlobalOption entity.');
        }

        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add('error',"Data has been deleted successfully");
        return $this->redirect($this->generateUrl('tools_domain'));
    }

    /**
     * Creates a form to delete a GlobalOption entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('globaloption_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Edits an existing GlobalOption entity.
     *
     */
    public function modifyUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:GlobalOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GlobalOption entity.');
        }

        $editForm = $this->createModifyForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->upload();
            $em->flush();
            //$this->getDoctrine()->getRepository('SettingContentBundle:HomePage')->globalOptionHome($user);
            //$this->getDoctrine()->getRepository('SettingContentBundle:ContactPage')->globalOptionContact($user);
            $this->getDoctrine()->getRepository('SettingToolBundle:SiteSetting')->updateSettingMenu($entity);
            $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->systemConfigUpdate($entity);
            $this->get('session')->getFlashBag()->add('success',"Data has been updated successfully");

            /*$referer = $request->headers->get('referer');
            return new RedirectResponse($referer);*/
            return $this->redirect($this->generateUrl('globaloption_edit', array('id' => $entity->getId())));


        }
        return $this->render('SettingToolBundle:GlobalOption:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));


    }

    /**
     * Creates a form to edit a GlobalOption entity.
     *
     * @param GlobalOption $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createModifyForm(GlobalOption $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new GlobalOptionModifyType($location), $entity, array(
            'action' => $this->generateUrl('globaloption_modify_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;

    }


    /**
     * Displays a form to edit an existing GlobalOption entity.
     *
     */
    public function modifyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getUser()->getGlobalOption();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GlobalOption entity.');
        }

        $this->get('settong.toolManageRepo')->createDirectory($entity->getId());
        $this->getDoctrine()->getRepository('SettingToolBundle:SiteSetting')->globalOptionSetting($entity);
        $editForm = $this->createModifyForm($entity);

        $subSyndicates = $this->getSubSyndicateUnderVendor($entity);

        return $this->render('SettingToolBundle:GlobalOption:new.html.twig', array(
            'entity'        => $entity,
            'edit_form'     => $editForm->createView(),
            'subSyndicates' => $subSyndicates
        ));
    }

    public function getSubSyndicateUnderVendor($entity)
    {
        $em = $this->getDoctrine()->getManager();
        $parentId       = $entity->getSyndicate()->getId();
        $syndicates     = $em->getRepository('SettingToolBundle:Syndicate')->findBy(array('status' => 1,'parent' => $parentId),array('name' => 'asc'));
        return $subSyndicates  = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate')->getSelectedSubSyndicates($syndicates,$entity);

    }

    public function webmailAction()
    {
        return $this->render('SettingToolBundle:GlobalOption:webmail.html.twig', array(
            'entity'      => $this->getUser()->getGlobalOption(),

        ));
    }

    public function vendorSearchAction()
    {
        echo 'Test';
    }
}
