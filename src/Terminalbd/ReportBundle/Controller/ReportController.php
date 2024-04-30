<?php

namespace Terminalbd\ReportBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\AccountingBundle\Entity\Transaction;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Core\UserBundle\Entity\User;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mis")
 * @Security("is_granted('ROLE_DOMAIN') or is_granted('ROLE_REPORT') or is_granted('ROLE_REPORT_ADMIN')")
 */
class ReportController extends Controller
{


    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * @Route("/dashboard", methods={"GET", "POST"}, name="report_dashboard")
     * @Secure(roles="ROLE_REPORT,ROLE_DOMAIN")
     */
    public function indexAction()
    {

        $data = $_REQUEST;
        if(empty($data)){
            $date = new \DateTime("now");
            $start = $date->format('d-m-Y');
            $end = $date->format('d-m-Y');
            $data = array('startDate'=> $start , 'endDate' => $end);
        }
        $todayCustomerSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->dailySalesReceive($this->getUser(),$data);
        $todayVendorSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->dailyPurchasePayment($this->getUser(),$data);
        $todayExpense = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->dailyPurchasePayment($this->getUser(),$data);
        $todayJournal = $this->getDoctrine()->getRepository('AccountingBundle:AccountJournalItem')->dailyJournal($this->getUser(),$data);
        $todayLoan = $this->getDoctrine()->getRepository('AccountingBundle:AccountLoan')->dailyLoan($this->getUser(),$data);
        $transactionMethods = array(1,4);
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionWiseOverview( $this->getUser(),$data);
        $transactionBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionBankCashOverview( $this->getUser(),$data);
        $transactionMobileBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionMobileBankCashOverview( $this->getUser(),$data);
        $transactionAccountHeadCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionAccountHeadCashOverview( $this->getUser(),$data);
        $employees = $this->getDoctrine()->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Default:index.html.twig', array(
            'transactionCashOverviews'                  => $transactionCashOverview,
            'transactionBankCashOverviews'              => $transactionBankCashOverviews,
            'transactionMobileBankCashOverviews'        => $transactionMobileBankCashOverviews,
            'transactionAccountHeadCashOverviews'       => $transactionAccountHeadCashOverviews,
            'todayCustomerSales'       => $todayCustomerSales,
            'todayVendorSales'       => $todayVendorSales,
            'todayExpense'       => $todayExpense,
            'todayJournal'       => $todayJournal,
            'todayLoan'       => $todayLoan,
            'employees'       => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));

    }

    /**
     * @Route("/management-dashboard", methods={"GET", "POST"}, name="report_management_dashboard")
     * @Secure(roles="ROLE_REPORT,ROLE_DOMAIN")
     */
    public function managementAction()
    {

        $data = $_REQUEST;
        if(empty($data)){
            $date = new \DateTime("now");
            $start = $date->format('d-m-Y');
            $end = $date->format('d-m-Y');
            $data = array('startDate'=> $start , 'endDate' => $end);
        }
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $customers = $this->getDoctrine()->getRepository(Customer::class)->getGenderBaseCustomer($globalOption);
        return $this->render('ReportBundle:Management:index.html.twig', array(
            'data' => $customers,
            'option' => $globalOption,
        ));

    }

    /**
     * @Route("/management-active-customer", methods={"GET", "POST"}, name="report_management_active_customer")
     * @Secure(roles="ROLE_REPORT,ROLE_DOMAIN")
     */
    public function managementActiveCustomerAction()
    {

        $data = $_REQUEST;
        if(empty($data)){
            $date = new \DateTime("now");
            $start = $date->format('d-m-Y');
            $end = $date->format('d-m-Y');
            $data = array('startDate'=> $start , 'endDate' => $end);
        }
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('ReportBundle:Customer:apps-customer.html.twig', array(
            'option' => $globalOption,
        ));

    }

    /**
     * @Route("/management-active-customer-ajax", methods={"GET", "POST"}, name="report_management_active_customer_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function  managementActiveCustomerAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        if(isset($_REQUEST['startDate']) and $_REQUEST['startDate'] and isset($_REQUEST['endDate']) and $_REQUEST['endDate'] ){
            $customers = $this->getDoctrine()->getRepository(Customer::class)->getActiveAppsCustomer($globalOption,$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Customer:apps-customer-data.html.twig', array(
                    'searchForm'    => $data,
                    'globalOption'  => $this->getUser()->getGlobalOption(),
                    'entities'     => $customers,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }


    /**
     * @Route("/management-customer", methods={"GET", "POST"}, name="report_management_customer_dashboard")
     * @Secure(roles="ROLE_REPORT,ROLE_DOMAIN")
     */
    public function managementCustomerAction()
    {

        $data = $_REQUEST;
        if(empty($data)){
            $date = new \DateTime("now");
            $start = $date->format('d-m-Y');
            $end = $date->format('d-m-Y');
            $data = array('startDate'=> $start , 'endDate' => $end);
        }
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:Customer')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $customerOrders = $em->getRepository('EcommerceBundle:Order')->customerOrders($globalOption,$pagination);
        return $this->render('ReportBundle:Management:customer.html.twig', array(
            'entities' => $pagination,
            'customerOrders' => $customerOrders,
            'searchForm' => $data,
            'option' => $globalOption,
        ));

    }

    /**
     * @Route("/management-sales", methods={"GET", "POST"}, name="report_management_customer_sales")
     * @Secure(roles="ROLE_REPORT,ROLE_DOMAIN")
     */
    public function managementCustomerSalesAction()
    {

        $data = $_REQUEST;
        if(empty($data)){
            $date = new \DateTime("now");
            $start = $date->format('d-m-Y');
            $end = $date->format('d-m-Y');
            $data = array('startDate'=> $start , 'endDate' => $end);
        }
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $data = $_REQUEST;
        $entities = $em->getRepository('InventoryBundle:Sales')->salesLists( $this->getUser() , $mode='general-sales', $data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('ReportBundle:Management:sales.html.twig', array(
            'entities' => $pagination,
            'config' => $inventoryConfig,
            'transactionMethods' => $transactionMethods,
            'option' => $globalOption,
            'searchForm' => $data,
        ));

    }

     /**
     * @Route("/{id}/management-sales-show", methods={"GET", "POST"}, name="report_management_customer_sales_details")
     * @Secure(roles="ROLE_REPORT,ROLE_DOMAIN")
     */
    public function CustomerSalesDetailsAction(Sales $entity)
    {

        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $globalOption = $this->getUser()->getGlobalOption();
        if ($inventory->getId() == $entity->getInventoryConfig()->getId()) {
            return $this->render('ReportBundle:Management:sales-details.html.twig', array(
                'entity' => $entity,
                'option' => $globalOption,
                'inventoryConfig' => $inventory,
            ));
        } else {
            return $this->redirect($this->generateUrl('inventory_salesonline'));
        }
    }

    /**
     * @Route("/management-stock", methods={"GET", "POST"}, name="report_management_stock")
     * @Secure(roles="ROLE_REPORT,ROLE_DOMAIN")
     */
    public function stockDetailsAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Item')->findWithSearch($inventory,$data);
        $pagination = $this->paginate($entities);
        return $this->render('ReportBundle:Management:stock-details.html.twig', array(
            'entities' => $pagination,
            'config' => $inventory,
            'option' => $globalOption,
            'searchForm' => $data
        ));

    }



    /**
     * @Route("/monthly-statement", methods={"GET", "POST"}, name="accounting_report_monthly_statement")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */
    public function monthlyStatementAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data =$_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $ajaxPath = $this->generateUrl('accounting_report_monthly_statement_ajax');
        return $this->render('ReportBundle:Accounting/Financial:monthly-statement.html.twig', array(
            'option' => $globalOption,
            'ajaxPath' => $ajaxPath,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/monthly-statement-ajax", methods={"GET", "POST"}, name="accounting_report_monthly_statement_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function monthlyStatementAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data = $_REQUEST;
        if(isset($data['month']) and $data['month'] and isset($data['year']) and $data['year'] ) {
            $user = $this->getUser();
            if(empty($data)){
                $compare = new \DateTime();
                $end =  $compare->format('j');
                $data['monthYear'] = $compare->format('Y-m-d');
                $data['month'] =  $compare->format('F');
                $data['year'] = $compare->format('Y');
            }else{
                $month = $data['month'];
                $year = $data['year'];
                $compare = new \DateTime("{$year}-{$month}-01");
                $end =  $compare->format('t');
                $data['monthYear'] = $compare->format('Y-m-d');
            }
            $openingBalance = [];
            for ($i = 1; $end >= $i ; $i++ ){
                $no = sprintf("%s", str_pad($i,2, '0', STR_PAD_LEFT));
                $start =  $compare->format("Y-m-{$no}");
                $day =  $compare->format("{$no}-m-Y");
                $data['startDate'] = $start;
                $openingBalance[$day] = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->openingBalanceGroup($user,'',$data);
            }
            $sales = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Sales',$data);
            $purchase = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Purchase',$data);
            $purchaseCommission = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Purchase-Commission',$data);
            $expenditure = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Expenditure',$data);
            $journal = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Journal',$data);

            $htmlProcess = $this->renderView(
                'ReportBundle:Accounting/Financial:monthly-statement-ajax.html.twig', array(
                    'searchForm' => $data,
                    'globalOption'                  => $this->getUser()->getGlobalOption(),
                    'openingBalanceTrans'           => $openingBalance,
                    'salesTrans'                    => $sales,
                    'purchaseTrans'                 => $purchase,
                    'purchaseCommissionTrans'       => $purchaseCommission,
                    'expenditureTrans'              => $expenditure,
                    'journalTrans'                  => $journal,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }


    /**
     * @Route("/customer-outstanding", methods={"GET", "POST"}, name="accounting_report_sales_outstanding")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */
    public function customerOutstandingAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data =$_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($globalOption,$data);
        return $this->render('ReportBundle:Accounting/Sales:customerOutstanding.html.twig', array(
            'option' => $globalOption,
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }


    /**
     * @Route("/customer-summary", methods={"GET", "POST"}, name="accounting_report_sales_summary")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function customerSummaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSummary($globalOption,$data);
        return $this->render('ReportBundle:Accounting/Sales:customerSummary.html.twig', array(
            'entities' => $entities,
            'option' => $globalOption,
            'searchForm' => $data,
        ));

    }

    /**
     * @Route("/cash-flow", methods={"GET", "POST"}, name="accounting_report_cash_flow")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function cashFlowAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Cash:cashFlow.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/balanch-sheet", methods={"GET", "POST"}, name="accounting_report_balanch_sheet")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function balanchSheetAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Cash:cashFlow.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/trail-balanch", methods={"GET", "POST"}, name="accounting_report_trail_balanch")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function trailBalanchAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Cash:cashFlow.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/account-head", methods={"GET", "POST"}, name="accounting_report_head")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function journalHeadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());
        return $this->render('ReportBundle:Accounting/Financial:account-head.html.twig', array(
            'accountHead' => $accountHead,
            'heads' => $heads,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/account-head-ajax", methods={"GET", "POST"}, name="accounting_report_head_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function journalHeadAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = "";
        if(isset($data['startDate']) and $data['startDate'] and isset($data['endDate']) and $data['endDate'] ) {
            $entities = $em->getRepository(Transaction::class)->reportAccountHead($globalOption,$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Accounting/Financial:account-head-ajax.html.twig', array(
                    'entities' => $entities,
                )
            );
            return new Response($htmlProcess);
        }
        return new Response('Record Does not found');
    }

   /**
     * @Route("/account-head-subhead", methods={"GET", "POST"}, name="accounting_report_subhead")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function journalSubheadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());
        return $this->render('ReportBundle:Accounting/Financial:account-subhead.html.twig', array(
            'accountHead' => $accountHead,
            'heads' => $heads,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/account-subhead-ajax", methods={"GET", "POST"}, name="accounting_report_subhead_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function journalSubHeadAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $entities = "";
        if(isset($data['startDate']) and $data['startDate'] and isset($data['endDate']) and $data['endDate'] ) {
            $entities = $em->getRepository(Transaction::class)->reportAccountHead($globalOption,$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Accounting/Financial:account-head-ajax.html.twig', array(
                    'entities' => $entities,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }

    /**
     * @Route("/daily-profit", methods={"GET", "POST"}, name="accounting_report_daily_profit")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function dailyProfitAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $ajaxPath = $this->generateUrl('accounting_report_daily_profit_ajax');
        return $this->render('ReportBundle:Accounting/Financial:income.html.twig', array(
            'option' => $globalOption,
            'ajaxPath' => $ajaxPath,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/daily-profit-ajax", methods={"GET", "POST"}, name="accounting_report_daily_profit_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function dailyProfitAjaxAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data = $_REQUEST;
        if(isset($data['startDate']) and $data['startDate'] and isset($data['endDate']) and $data['endDate'] ) {
            $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportSalesIncome($this->getUser(),$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Accounting/Financial:income-ajax.html.twig', array(
                    'overview' => $overview,
                    'searchForm' => $data,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }

    /**
     * @Route("/monthly-profit", methods={"GET", "POST"}, name="accounting_report_monthly_profit")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function monthlyProfitAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Cash:cashFlow.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/yearly-profit", methods={"GET", "POST"}, name="accounting_report_yearly_profit")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function yearlyProfitAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Cash:cashFlow.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/stakeholder-profit", methods={"GET", "POST"}, name="accounting_report_stakeholder_profit")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_DOMAIN")
     */

    public function stakeholderProfitAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $accountSubHeads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('globalOption' => $option),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());

        return $this->render('ReportBundle:Accounting/Sales:ledger.html.twig', array(
            'accountHead' => $accountHead,
            'accountSubHeads' => $accountSubHeads,
            'heads' => $heads,
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * @Route("/customer-ledger", methods={"GET", "POST"}, name="accounting_report_sales_customer_ledger")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function customerLedgerAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = '';
        $customer = "";
        $overview = "";
        $customers = $this->getDoctrine()->getRepository("AccountingBundle:AccountSales")->customerOutstanding($globalOption);
        if(isset($data['submit']) and $data['submit'] == 'search' and isset($data['customerId']) and $data['customerId']) {
            $customerId = $data['customerId'];
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'id'=> $customerId));
            $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportCustomerLedger($globalOption->getId(),$data);
            $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($this->getUser(),$data);

        }
        return $this->render('ReportBundle:Accounting/Sales:ledger.html.twig', array(
                'entities' => $entities,
                'overview' => $overview,
                'customer' => $customer,
                'customers' => $customers,
                'option' => $globalOption,
                'searchForm' => $data,
        ));

    }

    /**
     * @Route("/user-sales-receive", methods={"GET", "POST"}, name="accounting_report_sales_user_summary")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function userSummaryAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->userSummary($globalOption,$data);
        return $this->render('ReportBundle:Accounting/Sales:userSummary.html.twig', array(
                'entities' => $entities,
                'option' => $globalOption,
                'searchForm' => $data,
        ));

    }

    /**
     * @Route("/customer-sales-receive", methods={"GET", "POST"}, name="accounting_report_sales_customer_summary")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function userSalesDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        if(isset($data['submit']) and $data['submit'] == 'search' and isset($data['user']) and $data['user']) {
            $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportCustomerDetails($globalOption,$data);
        }
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Sales:customerDetails.html.twig', array(
            'entities' => $entities,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/sales-details", methods={"GET", "POST"}, name="accounting_report_sales_details")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

   public function salesDetailsAction()
   {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $customers = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getSalesCustomers($globalOption);
        $groups = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getProcessModes($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Accounting/Sales:sales.html.twig', array(
            'entities' => $entities,
            'transactionMethods' => $transactionMethods,
            'groups' => $groups,
            'users' => $users,
            'customers' => $customers,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
   }

    /**
     * @Route("/sales-details-load", methods={"GET", "POST"}, name="accounting_report_sales_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function salesDetailsLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        $salesBy = "";
        if(isset($data['startDateTime']) and $data['startDateTime'] and isset($data['endDateTime']) and $data['endDateTime'] ) {
            $entities = $em->getRepository('AccountingBundle:AccountSales')->reportFindWithSearch($globalOption,$data);
            if(isset($data['user']) and !empty($data['user'])){
                $salesBy = $this->getDoctrine()->getRepository(User::class)->find($data['user']);
            }
            $htmlProcess = $this->renderView(
                'ReportBundle:Accounting/Sales:sales-data.html.twig', array(
                    'entities' => $entities,
                    'data' => $data,
                    'salesBy' => $salesBy,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }

    /**
     * @Route("/purchase-details", methods={"GET", "POST"}, name="accounting_report_purchase_details")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function purchaseDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        if(isset($data['submit']) and $data['submit'] == 'search' and isset($data['user']) and $data['user']) {
            $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportCustomerDetails($globalOption,$data);
        }
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findWithSearch($globalOption,$data);
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $groups = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->getProcessModes($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Accounting/Sales:customerDetails.html.twig', array(
            'entities' => $entities,
            'transactionMethods' => $transactionMethods,
            'groups' => $groups,
            'users' => $users,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }


    /* Inventory Report */

    /**
     * @Route("/inv-system-overview", methods={"GET", "POST"}, name="inv_system_overview")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function invSystemOverviewAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $inventory = $globalOption->getInventoryConfig()->getId();
        $purchaseOverview = $em->getRepository('ReportBundle:Report')->invReportPurchaseOverview($inventory,$data);
        $priceOverview = $em->getRepository('ReportBundle:Report')->invReportStockPriceOverview($inventory,$data);
        $salesPurchasePrice = $em->getRepository('ReportBundle:Report')->invReportSalesPurchasePrice($inventory,$data);
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        return $this->render('ReportBundle:Inventory:index.html.twig', array(
            'priceOverview' => $priceOverview[0],
            'stockOverview' => $stockOverview,
            'purchaseOverview' => $purchaseOverview,
            'salesPurchasePrice' => $salesPurchasePrice,
            'option' => $globalOption,
        ));
    }


    /**
     * @Route("/inv-stock-item-price", methods={"GET", "POST"}, name="inv_stock_item_price")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $priceOverview = $em->getRepository('ReportBundle:Report')->invReportStockPriceOverview($inventory,$data);
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);

        return $this->render('ReportBundle:Inventory/Stock:stock.html.twig', array(
            'searchForm' => $data,
            'priceOverview' => $priceOverview[0],
            'stockOverview' => $stockOverview,
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/inv-stock-item-price-ajax-load", methods={"GET", "POST"}, name="inv_stock_item_price_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invStockItemAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('ReportBundle:Report')->invReportStockItemPrice($inventory,$data);
        $htmlProcess = $this->renderView(
            'ReportBundle:Inventory/Stock:stock-item-price-data.html.twig', array(
                'entities' => $entities,
            )
        );
        return new Response($htmlProcess);
    }

    /**
     * @Route("/inv-category-stock-item-price", methods={"GET", "POST"}, name="inv_category_stock_item_price")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invCategoryStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('ReportBundle:Inventory/Stock:category-stock.html.twig', array(
            'searchForm' => $data,
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/inv-category-stock-item-price-ajax-load", methods={"GET", "POST"}, name="inv_category_stock_item_price_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invCategoryStockItemAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('ReportBundle:Report')->invReportCategoryStockItemPrice($inventory,$data);
        $htmlProcess = $this->renderView(
            'ReportBundle:Inventory/Stock:category-stock-item-price-data.html.twig', array(
                'entities' => $entities,
            )
        );
        return new Response($htmlProcess);
    }


    /**
     * @Route("/inv-brand-stock-item-price", methods={"GET", "POST"}, name="inv_brand_stock_item_price")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invBrandStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('ReportBundle:Inventory/Stock:brand-stock.html.twig', array(
            'searchForm' => $data,
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/inv-brand-stock-item-price-ajax-load", methods={"GET", "POST"}, name="inv_brand_stock_item_price_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invBrandStockItemAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('ReportBundle:Report')->invReportBrandStockItemPrice($inventory,$data);
        $htmlProcess = $this->renderView(
            'ReportBundle:Inventory/Stock:brand-stock-item-price-data.html.twig', array(
                'entities' => $entities,
            )
        );
        return new Response($htmlProcess);
    }

    /**
     * @Route("/inv-sales-profit-loss", methods={"GET", "POST"}, name="inv_sales_profit_loss")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $customers = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getSalesCustomers($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Inventory/Sales:index.html.twig', array(
            'transactionMethods' => $transactionMethods,
            'customers' => $customers,
            'searchForm' => $data,
            'users' => $users,
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/inv-sales-profit-loss-ajax-load", methods={"GET", "POST"}, name="inv_sales_profit_loss_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invSalesAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('ReportBundle:Report')->invReportSales($inventory->getId(),$data);
        $htmlProcess = $this->renderView(
            'ReportBundle:Inventory/Sales:ajax-data.html.twig', array(
                'entities' => $entities,
            )
        );
        return new Response($htmlProcess);
    }

    /**
     * @Route("/customer-monthly-sales", methods={"GET", "POST"}, name="inv_customer_monthly_sales")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function customerMonthlySalesAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository(AccountSales::class)->reportSalesCustomers($globalOption,$data);
        return $this->render('ReportBundle:Inventory/CustomerMonthlySales:index.html.twig', array(
            'customers' => $entities['customers'],
            'entities' => $entities['sales'],
            'daylies' => $entities['daylies'],
            'currentMonth' => date('F'),
            'currentYear' => date('Y'),
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/customer-monthly-sales-load", methods={"GET", "POST"}, name="inv_customer_monthly_sales_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function customerMonthlySalesLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data = $_REQUEST;
        if(isset($data['year']) and $data['year'] and isset($data['month']) and $data['month'] ) {
            $data = $_REQUEST;
            $globalOption = $this->getUser()->getGlobalOption();
            $entities = $this->getDoctrine()->getRepository(AccountSales::class)->reportSalesCustomers($globalOption,$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Inventory/CustomerMonthlySales:ajax-data.html.twig', array(
                    'customers' => $entities['customers'],
                    'entities' => $entities['sales'],
                    'daylies' => $entities['daylies'],
                    'currentMonth' => $data['month'],
                    'currentYear' => $data['year'],
                    'option' => $globalOption,
                    'searchForm' => $data,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }


    /**
     * @Route("/inv-salesitem-profit-loss", methods={"GET", "POST"}, name="inv_salesitem_profit_loss")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invSalesItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $customers = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getSalesCustomers($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Inventory/SalesItem:index.html.twig', array(
            'transactionMethods' => $transactionMethods,
            'customers' => $customers,
            'searchForm' => $data,
            'users' => $users,
            'option' => $globalOption,
        ));
    }

    /**
     * @Route("/inv-salesitem-profit-loss-ajax-load", methods={"GET", "POST"}, name="inv_salesitem_profit_loss_ajax_load")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */
    public function invSalesItemAjaxLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('ReportBundle:Report')->invReportSalesItem($inventory->getId(),$data);
        $htmlProcess = $this->renderView(
            'ReportBundle:Inventory/SalesItem:ajax-data.html.twig', array(
                'entities' => $entities,
            )
        );
        return new Response($htmlProcess);
    }


    /**
     * @Route("/customer-order-overview", methods={"GET", "POST"}, name="accounting_report_customer_order_overview")
     * @Secure(roles="ROLE_REPORT_FINANCIAL, ROLE_REPORT, ROLE_DOMAIN")
     */

    public function customerOrderOverviewAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $customers = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getSalesCustomers($globalOption);
        $groups = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getProcessModes($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Customer:customer.html.twig', array(
            'entities' => $entities,
            'transactionMethods' => $transactionMethods,
            'groups' => $groups,
            'users' => $users,
            'customers' => $customers,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/customer-order-overview-load", methods={"GET", "POST"}, name="accounting_report_customer_order_overview_ajax")
     * @Secure(roles="ROLE_REPORT_FINANCIAL,ROLE_REPORT, ROLE_DOMAIN")
     */

    public function customerOrderOverviewLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        $salesBy = "";
        if(isset($data['customerId']) and $data['customerId'] and isset($data['startDateTime']) and $data['startDateTime'] and isset($data['endDateTime']) and $data['endDateTime'] ) {
            $lifeTimeOrder = $this->getDoctrine()->getRepository(Sales::class)->salesCount($data['customerId']);
            $customer = $this->getDoctrine()->getRepository(Customer::class)->find($data['customerId']);
            $overview = $this->getDoctrine()->getRepository(AccountSales::class)->customerLedgerOverview($globalOption->getId(),$data['customerId']);
            $entities = $em->getRepository('AccountingBundle:AccountSales')->customerOrderLedger($globalOption,$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Customer:customer-data.html.twig', array(
                    'customer' => $customer,
                    'lifeTimeOrder' => $lifeTimeOrder,
                    'overview' => $overview,
                    'entities' => $entities,
                    'data' => $data,
                    'salesBy' => $salesBy,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }

}
