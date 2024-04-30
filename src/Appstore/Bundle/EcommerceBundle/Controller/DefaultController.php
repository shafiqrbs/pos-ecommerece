<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function indexAction()
    {
        /* @var GlobalOption $globalOption */

        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $datetime = new \DateTime("now");
        $data['startDate'] = $datetime->format('Y-m-d');
        $data['endDate'] = $datetime->format('Y-m-d');

        $user = $this->getUser();
        $salesCashOverview = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->reportSalesOverview($user,$data);
        $purchaseCashOverview = $this->getDoctrine()->getRepository('InventoryBundle:Purchase')->reportPurchaseOverview($user,$data);
        $transactionMethods = array(1);
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
        $expenditureOverview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);
        $salesUserReport = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->salesUserReport($user,array('startDate'=>$data['startDate'],'endDate'=>$data['endDate']));
        //   $userEntities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,$data);
        $startMonthDate = $datetime->format('Y-m-01 00:00:00');
        $endMonthDate = $datetime->format('Y-m-t 23:59:59');
        $medicineSalesMonthly = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->inventorySalesMonthly($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
        $medicinePurchaseMonthly = $this->getDoctrine()->getRepository('InventoryBundle:Purchase')->inventoryPurchaseMonthly($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
        $shortMedicineCount = $this->getDoctrine()->getRepository('InventoryBundle:Item')->inventoryShortListCount($user);

        $employees = $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());
        $entities = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->currentMonthSales($user,$data);
        $userSalesAmount = array();
        foreach($entities as $row) {
            $userSalesAmount[$row['salesBy'].$row['month']] = $row['total'];
        }

        $medicineSalesMonthlyArr = array();
        foreach($medicineSalesMonthly as $row) {
            $medicineSalesMonthlyArr[$row['month']] = $row['total'];
        }
        $medicinePurchaseMonthlyArr = array();
        foreach($medicinePurchaseMonthly as $row) {
            $medicinePurchaseMonthlyArr[$row['month']] = $row['total'];
        }


        $entities = $em->getRepository('EcommerceBundle:Order')->todayOrderDashboard($globalOption->getId());


        return $this->render('EcommerceBundle:Default:index.html.twig', array(

            'option'                    => $user->getGlobalOption() ,
            'globalOption'              => $globalOption,
            'transactionCashOverviews'  => $transactionCashOverview,
            'expenditureOverview'       => $expenditureOverview ,
            'salesCashOverview'         => $salesCashOverview ,
            'purchaseCashOverview'      => $purchaseCashOverview ,
            'medicineSalesMonthly'      => $medicineSalesMonthlyArr ,
            'medicinePurchaseMonthly'   => $medicinePurchaseMonthlyArr ,
            'salesUserReport'           => $salesUserReport ,
            'employees'                 => $employees ,
            'shortMedicineCount'        => $shortMedicineCount ,
            'entities'                  => $entities ,
            'expiryMedicineCount'       => '' ,
            'medicineSalesDaily'        => '' ,
            'medicineSalesHourly'       => '' ,
            'searchForm'                => $data ,

        ));

    }

    public function categoryDeleteAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();
        $StockItem = $em->createQuery('DELETE ProductProductBundle:Category e WHERE e.ecommerceConfig = '.$config);
        $StockItem->execute();
        return $this->redirect($this->generateUrl('ecommerce_item'));
    }

    public function itemDeleteAction()
    {
        $config = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();
        $em = $this->getDoctrine()->getManager();
        $StockItem = $em->createQuery('DELETE EcommerceBundle:Item e WHERE e.ecommerceConfig = '.$config);
        $StockItem->execute();
        return $this->redirect($this->generateUrl('ecommerce_item'));

    }
}
