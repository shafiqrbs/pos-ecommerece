<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Appstore\Bundle\EcommerceBundle\Form\EcommerceConfigType;

/**
 * EcommerceConfig controller.
 *
 */
class EcommerceConfigController extends Controller
{



    /**
    * Creates a form to edit a EcommerceConfig entity.
    *
    * @param EcommerceConfig $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EcommerceConfig $entity)
    {
        $form = $this->createForm(new EcommerceConfigType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_config_update'),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing EcommerceConfig entity.
     *
     */
    public function updateAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        /* @var $entity EcommerceConfig */
        $entity = $this->getUser()->getGlobalOption()->getEcommerceConfig();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EcommerceConfig entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload()){
                $entity->removeUpload();
                $entity->upload();
            }else{
                $entity->upload();
            }
            $em->flush();

            return $this->redirect($this->generateUrl('ecommerce_config_modify'));
        }

        return $this->render('EcommerceBundle:EcommerceConfig:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }
    /**
     * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE_CONFIG,ROLE_DOMAIN")
     */

    public function modifyAction()
    {

        $entity = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EcommerceConfig entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('EcommerceBundle:EcommerceConfig:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
}
