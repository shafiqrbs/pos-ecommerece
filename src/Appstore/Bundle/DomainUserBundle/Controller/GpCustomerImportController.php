<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Entity\GpCustomerImport;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\DomainUserBundle\Form\GpCustomerImportType;

/**
 * GpCustomerImport controller.
 *
 */
class GpCustomerImportController extends Controller
{

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_MANAGER")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new GpCustomerImport();
        $form   = $this->createCreateForm($entity);
        $option = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:GpCustomerImport')->findBy(array('globalOption' => $option),array('updated'=>'DESC'));
        return $this->render('DomainUserBundle:GpCustomerImport:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_MANAGER")
     */

    public function createAction(Request $request)
    {
        $entity = new GpCustomerImport();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        if ($form->isValid()) {

            $entity->setGlobalOption($option);
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('domain_gp_customer_import'));
        }
        $entities = $em->getRepository('DomainUserBundle:GpCustomerImport')->findBy(array('globalOption' => $option),array('updated'=>'DESC'));
        return $this->render('DomainUserBundle:GpCustomerImport:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a GpCustomerImport entity.
     *
     * @param GpCustomerImport $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(GpCustomerImport $entity)
    {
        $form = $this->createForm(new GpCustomerImportType(), $entity, array(
            'action' => $this->generateUrl('domain_gp_customer_import_create'),
            'method' => 'POST',
        ));
        return $form;
    }

   
    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_MANAGER")
     */

    public function excelDataImportAction(GpCustomerImport $GpCustomerImport)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $importer = $this->get('appstore_customer_import');
        $importer->setGlobalOption($globalOption);
        $reader = $this->get('appstore.importer.customer_excel_data_reader');
        $file =  realpath($GpCustomerImport->getAbsolutePath());
        $importer->import($reader->getData($file));
        $GpCustomerImport->setProgress('migrated');
        $em->flush();
      //  $this->getDoctrine()->getRepository(Customer::class)->processUser($globalOption);
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been migration successfully"
        );
        return $this->redirect($this->generateUrl('domain_gp_customer_import'));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_MANAGER")
     */

    public function excelCustomerDataImportAction(GpCustomerImport $GpCustomerImport)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $importer = $this->get('appstore_customer_import');
        $importer->setGlobalOption($GpCustomerImport->getGlobalOption());
        $reader = $this->get('appstore.importer.customer_excel_data_reader');
        $file =  realpath($GpCustomerImport->getAbsolutePath());
        $importer->import($reader->getData($file));
        $GpCustomerImport->setProgress('migrated');
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been migration successfully"
        );
        return $this->redirect($this->generateUrl('domain_gp_customer_import'));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_MANAGER")
     */

    public function deleteAction(GpCustomerImport $salesImport)
    {
        $em = $this->getDoctrine()->getManager();
        if ($salesImport) {
            $salesImport->removeUpload();
            $em->remove($salesImport);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('domain_gp_customer_import'));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_MANAGER")
     */

    public function processImportAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $this->getDoctrine()->getRepository(Customer::class)->processUser($globalOption);
        return $this->redirect($this->generateUrl('domain_gp_customer_import'));
    }



}
