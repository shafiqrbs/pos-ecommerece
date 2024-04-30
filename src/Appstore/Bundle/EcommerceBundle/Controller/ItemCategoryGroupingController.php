<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\ItemCategoryGrouping;
use Appstore\Bundle\EcommerceBundle\Form\ItemCategoryGroupingType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * ItemTypeGrouping controller.
 *
 */
class ItemCategoryGroupingController extends Controller
{


    /**
     * Creates a new ItemCategoryGrouping entity.
     *
     */
    public function createAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        if($globalOption){
            $entity = $globalOption->getEcommerceConfig()->getCategoryGrouping();
        }else{
            $entity = new ItemCategoryGrouping();
        }

        $data = $request->request->get('categories');
        $array = array();
        foreach($data as $row){
            $array[] = $em->getRepository('ProductProductBundle:Category')->find($row);
        }

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $entity->setEcommerceConfig($this->getUser()->getGlobalOption()->getEcommerceConfig());
            $entity->setCategories($array);
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('ecommerce_grouping'));
        }
    }

    /**
     * Creates a form to create a ItemCategoryGrouping entity.
     *
     * @param ItemCategoryGrouping $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ItemCategoryGrouping $entity)
    {
        $form = $this->createForm(new ItemCategoryGroupingType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_grouping_create'),
            'method' => 'POST',
        ));
        return $form;
    }



    /**
     * Displays a form to edit an existing ItemTypeGrouping entity.
     *
     */
    public function editAction()
    {
        $em = $this->getDoctrine()->getManager();
	    $config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
	    $entity = new ItemCategoryGrouping();
        $array = array();
        $grouping =  $config->getCategoryGrouping();
        if($grouping){
            $groups = $grouping->getCategories();
            foreach($groups as $row ){
                $array[] = $row->getId();
            }
        }else{
            $entity->setEcommerceConfig($config);
            $em->persist($entity);
            $em->flush();
        }


        $categories = $em->getRepository('ProductProductBundle:Category')->findBy(array('status' => 1,'inventoryConfig'=>NULL,'parent'=>NULL),array('name'=>'asc'));
        if($categories)
        $entities = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getGroupCategories($categories,$array);
        $form   = $this->createCreateForm($entity);
        return $this->render('EcommerceBundle:ItemCategoryGrouping:edit.html.twig', array(
            'entities' => $entities,
            'form'   => $form->createView(),
        ));

    }


}
