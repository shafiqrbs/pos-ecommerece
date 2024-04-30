<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu;
use Setting\Bundle\AppearanceBundle\Form\EcommerceMenuType;

/**
 * EcommerceMenu controller.
 *
 */
class EcommerceMenuController extends Controller
{

    /**
     * Lists all EcommerceMenu entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingAppearanceBundle:EcommerceMenu')->findBy(array('globalOption' => $globalOption ),array('sorting' => 'asc'));
        return $this->render('SettingAppearanceBundle:EcommerceMenu:index.html.twig', array(
            'entities' => $entities,
        ));
    }


    /**
     * Creates a new EcommerceMenu entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new EcommerceMenu();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('ecommercemenu_edit', array('id' => $entity->getId())));
        }

        return $this->render('SettingAppearanceBundle:EcommerceMenu:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a EcommerceMenu entity.
     *
     * @param EcommerceMenu $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EcommerceMenu $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new EcommerceMenuType($globalOption,$em), $entity, array(
            'action' => $this->generateUrl('ecommercemenu_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new EcommerceMenu entity.
     *
     */
    public function newAction()
    {
        $entity = new EcommerceMenu();
        $form   = $this->createCreateForm($entity);
        return $this->render('SettingAppearanceBundle:EcommerceMenu:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing EcommerceMenu entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:EcommerceMenu')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EcommerceMenu entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingAppearanceBundle:EcommerceMenu:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a EcommerceMenu entity.
     *
     * @param EcommerceMenu $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(EcommerceMenu $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new EcommerceMenuType($globalOption,$em), $entity, array(
            'action' => $this->generateUrl('ecommercemenu_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing EcommerceMenu entity.
     *
     */
    public function updateAction(Request $request, EcommerceMenu $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('ecommercemenu_edit', array('id' => $entity->getId())));
        }

        return $this->render('SettingAppearanceBundle:EcommerceMenu:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a EcommerceMenu entity.
     *
     */
    public function deleteAction(EcommerceMenu $megamenu )
    {

            $em = $this->getDoctrine()->getManager();
            if (!$megamenu) {
                throw $this->createNotFoundException('Unable to find EcommerceMenu entity.');
            }
            $em->remove($megamenu);
            $em->flush();
            return $this->redirect($this->generateUrl('ecommercemenu'));
    }


    public function sortedAction(Request $request){

        $data = $request->request->get('menuItem');
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:EcommerceMenu')->setOrdering($data);
        exit;

    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(EcommerceMenu $entity)
    {
        $em = $this->getDoctrine()->getManager();
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
        return $this->redirect($this->generateUrl('ecommercemenu'));
    }


}
