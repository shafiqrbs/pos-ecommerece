<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 3/4/15
 * Time: 3:36 PM
 */

namespace Setting\Bundle\AppearanceBundle\Menu;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu;
use Setting\Bundle\AppearanceBundle\Entity\MegaMenu;
use Setting\Bundle\ToolBundle\Entity\Branding;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\DependencyInjection\ContainerAware;


class Builder extends ContainerAware
{

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $globalOption = $securityContext->getGlobalOption();

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'page-sidebar-menu');
        $menu = $this->dashboardMenu($menu);

            $modules = "";
            if($globalOption->getSiteSetting()){
                $modules = $globalOption->getSiteSetting()->getAppModules();
            }
            $arrSlugs = array();
            $menuName = array();
            if (!empty($globalOption->getSiteSetting()) and !empty($modules)) {
                foreach ($globalOption->getSiteSetting()->getAppModules() as $mod) {
                    if (!empty($mod->getModuleClass())) {
                        $menuName[] = $mod->getModuleClass();
                        $arrSlugs[] = $mod->getSlug();
                    }
                }
            }

            $result = array_intersect($menuName, array('Inventory'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_INVENTORY')){
                    if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES')) {
                        $menu = $this->InventorySalesMenu($menu);
                    }
                    if ($securityContext->isGranted('ROLE_INVENTORY')) {
                        $menu = $this->InventoryMenu($menu);
                    }
                }
            }

            $result = array_intersect($menuName, array('Ecommerce'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_ECOMMERCE')){
                    $menu = $this->EcommerceMenu($menu,$arrSlugs);
                }
            }

            $result = array_intersect($menuName, array('Accounting'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_ACCOUNTING')){
                    $menu = $this->AccountingMenu($menu);
                }
            }

            $result = array_intersect($menuName, array('Payroll'));
            if (!empty($result)) {
                if ($securityContext->isGranted('ROLE_HR') || $securityContext->isGranted('ROLE_PAYROLL')){
                    $menu = $this->PayrollMenu($menu);
                }
            }

		    $result = array_intersect($menuName, array('Website','Ecommerce'));
		    if (!empty($result)) {
			    if ($securityContext->isGranted('ROLE_WEBSITE') || $securityContext->isGranted('ROLE_ECOMMERCE')){
				    $menu = $this->WebsiteMenu($menu,$menuName);
			    }
		    }


	    if ($securityContext->isGranted('ROLE_DOMAIN') || $securityContext->isGranted('ROLE_SMS')) {
               // $menu = $this->manageDomainInvoiceMenu($menu);
        }
        return $menu;
    }

    public function dashboardMenu($menu)
    {
        $menu
            ->addChild('Dashboard', array('route' => 'homepage'))
            ->setAttribute('icon', 'fa fa-home');
        return $menu;
    }

    public function WebsiteMenu($menu,$menuName)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $option = $securityContext->getGlobalOption();

        if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_MANAGER')) {

            $menu
                ->addChild('Manage Website')
                ->setAttribute('icon', 'fa fa-book')
                ->setAttribute('dropdown', true);

            $menu['Manage Website']->addChild('Page', array('route' => 'page'));
            if ($option->getSiteSetting()) {
                $syndicateModules = $option->getSiteSetting()->getSyndicateModules();
                if (!empty($syndicateModules)) {
                    foreach ($option->getSiteSetting()->getSyndicateModules() as $syndmod) {
                        $menu['Manage Website']->addChild($syndmod->getName(), array('route' => strtolower($syndmod->getModuleClass())));
                    }
                }

                $modules = $option->getSiteSetting()->getModules();
                if (!empty($modules)) {
                    foreach ($option->getSiteSetting()->getModules() as $mod) {
                        $menu['Manage Website']->addChild($mod->getName(), array('route' => strtolower($mod->getModuleClass())));
                    }
                }
                if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_WEDGET') && $securityContext->isGranted('ROLE_WEBSITE')){
                    $menu['Manage Website']->addChild('Page Feature')->setAttribute('dropdown', true);
                    $menu['Manage Website']['Page Feature']->addChild('Widget', array('route' => 'appearancewebsitewidget'));
                    $menu['Manage Website']['Page Feature']->addChild('Feature', array('route' => 'appearancefeature'));
                }
            }
            $menu
                ->addChild('Media')
                ->setAttribute('icon', 'fa fa-picture-o')
                ->setAttribute('dropdown', true);

            $menu['Manage Website']->addChild('Contact', array('route' => 'contactpage_modify'));
            $menu['Media']->addChild('Galleries', array('route' => 'gallery'));
        }

        if ($securityContext->isGranted('ROLE_DOMAIN_WEBSITE_SETTING') OR $securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_ADMIN')) {

            $result = array_intersect($menuName, array('Ecommerce'));
            $website = array_intersect($menuName, array('Website'));
            $menu
                ->addChild('Manage Appearance')
                ->setAttribute('icon', 'fa fa-cogs')
                ->setAttribute('dropdown', true);

            $menu['Manage Appearance']->addChild( 'Customize Template', array( 'route'=> 'templatecustomize_edit'));

            if($website and $option->getMainApp()->getSlug() == "website"){
                $menu['Manage Appearance']->addChild('Website')->setAttribute('dropdown', true);
                $menu['Manage Appearance']['Website']->addChild('Website Widget', array('route' => 'appearancewebsitewidget'));
                $menu['Manage Appearance']['Website']->addChild('Feature', array('route' => 'appearancefeature'));

            }
            if (!empty($result) and $securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_MENU') && $securityContext->isGranted('ROLE_ECOMMERCE')) {
                $menu['Manage Appearance']->addChild('E-commerce Menu', array('route' => 'ecommercemenu'));
            }
            $menu['Manage Appearance']->addChild('Website Menu', array('route' => 'menu_manage'));
            if($website) {
                $menu['Manage Appearance']->addChild('Menu Grouping', array('route' => 'menugrouping'));
            }
        }

        return $menu;
    }

    public function AccountingMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        /* @var  $globalOption GlobalOption */

        $globalOption = $securityContext->getGlobalOption();

        $modules = $globalOption->getSiteSetting()->getAppModules();
        $arrSlugs = [];
        if (!empty($globalOption->getSiteSetting()) and !empty($modules)) {
            foreach ($globalOption->getSiteSetting()->getAppModules() as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $arrSlugs[] = $mod->getSlug();
                }
            }
        }

        $menu
            ->addChild('Accounting')
            ->setAttribute('icon', 'fa fa-building-o')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES') ||  ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR'))) {
	    $menu['Accounting']->addChild('Manage Sales', array('route' => ''))
	                       ->setAttribute('dropdown', true);

            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES')) {
                $menu['Accounting']['Manage Sales']->addChild('Sales', array('route' => 'account_sales'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES') ||  $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR')) {
                $menu['Accounting']['Manage Sales']->addChild('Add Receive', array('route' => 'account_sales_new'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES_ADJUSTMENT')) {
                $menu['Accounting']['Manage Sales']->addChild('Sales Adjustment', array('route' => 'account_salesadjustment'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_SALES_REPORT')) {
                $menu['Accounting']['Manage Sales']->addChild('Reports', array('route' => ''))->setAttribute('dropdown', true);
                $menu['Accounting']['Manage Sales']['Reports']->addChild('Outstanding', array('route' => 'report_customer_outstanding'));
                $menu['Accounting']['Manage Sales']['Reports']->addChild('Summary', array('route' => 'report_customer_summary'));
                $menu['Accounting']['Manage Sales']['Reports']->addChild('Ledger', array('route' => 'report_customer_ledger'));
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE') || $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR')) {
	    $menu['Accounting']->addChild('Manage Purchase', array('route' => ''))

	                       ->setAttribute('dropdown', true);
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')){
                $menu['Accounting']['Manage Purchase']->addChild('Purchase', array('route' => 'account_purchase'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE') || $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR')) {
                $menu['Accounting']['Manage Purchase']->addChild('Add Payment', array('route' => 'account_purchase_new'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE')) {
                $menu['Accounting']['Manage Purchase']->addChild('Commission', array('route' => 'account_purchasecommission'));
                if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_PURCHASE_REPORT')) {
                    $menu['Accounting']['Manage Purchase']->addChild('Reports', array('route' => ''))->setAttribute('dropdown', true);
                    $menu['Accounting']['Manage Purchase']['Reports']->addChild('Outstanding', array('route' => 'report_vendor_outstanding'));
                    $menu['Accounting']['Manage Purchase']['Reports']->addChild('Summary', array('route' => 'report_vendor_summary'));
                    $menu['Accounting']['Manage Purchase']['Reports']->addChild('Ledger', array('route' => 'report_vendor_ledger'));
                }
            }
	    }
	    if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE') || $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR')){
		    $menu['Accounting']->addChild('Bill & Expenditure', array('route' => ''))
                
		                       ->setAttribute('dropdown', true);
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE')) {
                $menu['Accounting']['Bill & Expenditure']->addChild('Expense', array('route' => 'account_expenditure'));
            }
		    if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE') || $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_OPERATOR')) {
                $menu['Accounting']['Bill & Expenditure']->addChild('Add Expense', array('route' => 'account_expenditure_new'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE')) {
                $menu['Accounting']['Bill & Expenditure']->addChild('Android Process', array('route' => 'account_expenditure_android'));
                $menu['Accounting']['Bill & Expenditure']->addChild('Expense Category', array('route' => 'expensecategory'));
                if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_EXPENDITURE_PURCHASE')) {
                    $menu['Accounting']['Bill & Expenditure']->addChild('Bill Voucher', array('route' => 'account_expense_purchase'));
                    $menu['Accounting']['Bill & Expenditure']->addChild('Account Vendor', array('route' => 'account_vendor'));

                }
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_REPORT')) {
                $menu['Accounting']['Bill & Expenditure']->addChild('Reports', array('route' => ''))->setAttribute('dropdown', true);
                $menu['Accounting']['Bill & Expenditure']['Reports']->addChild('Account Head', array('route' => 'report_expenditure_summary'));
                $menu['Accounting']['Bill & Expenditure']['Reports']->addChild('Category', array('route' => 'report_expenditure_category'));
                $menu['Accounting']['Bill & Expenditure']['Reports']->addChild('Details', array('route' => 'report_expenditure_details'));
            }

        }

        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_TRANSACTION') || $securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_CASH')) {

            $menu['Accounting']->addChild('Cash', array('route' => ''))
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Cash']->addChild('Cash Overview', array('route' => 'account_transaction_cash_overview'));
            if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_RECONCILIATION')) {
                $menu['Accounting']['Cash']->addChild('Cash Reconciliation', array('route' => 'account_cashreconciliation'));
            }
            $menu['Accounting']['Cash']->addChild('Collection & Payment', array('route' => 'account_transaction_cash_summary'));
            $menu['Accounting']['Cash']->addChild('All Cash Flow', array('route' => 'account_transaction_accountcash'));
            $menu['Accounting']['Cash']->addChild('Cash Transaction', array('route' => 'account_transaction_cash'));
            $menu['Accounting']['Cash']->addChild('Bank Transaction', array('route' => 'account_transaction_bank'));
            $menu['Accounting']['Cash']->addChild('Mobile Transaction', array('route' => 'account_transaction_mobilebank'));
            $menu['Accounting']['Cash']->addChild('Reports', array('route' => ''))->setAttribute('dropdown', true);
            $menu['Accounting']['Cash']['Reports']->addChild('Purchase & Expense',array('route' => 'account_transaction_purchase_expense'));
            $menu['Accounting']['Cash']['Reports']->addChild('Monthly Cash',array('route' => 'account_transaction_monthly'));
            $menu['Accounting']['Cash']['Reports']->addChild('Yearly Cash',array('route' => 'account_transaction_yearly'));
        }
        if($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_LOAN')){
            $menu['Accounting']->addChild('Manage Loan', array('route' => 'account_loan'))
                
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Manage Loan']->addChild('Loan', array('route' => 'account_loan'));
            $menu['Accounting']['Manage Loan']->addChild('Loan New', array('route' => 'account_loan_new'));
        }
         if($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_CONDITION')){
            $menu['Accounting']->addChild('Condition Account', array('route' => 'account_condition'))
                
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Condition Account']->addChild('New Ledger Voucher', array('route' => 'account_condition_ledger_new'));
            $menu['Accounting']['Condition Account']->addChild('Condition Ledger', array('route' => 'account_condition_ledger'));
            $menu['Accounting']['Condition Account']->addChild('Condition Account', array('route' => 'account_condition'));
         }

        if($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_JOURNAL')){
            $menu['Accounting']->addChild('Journal', array('route' => 'account_transaction'))
                ->setAttribute('dropdown', true);
            $result = array_intersect($arrSlugs, array('accounting'));
            if (!empty($result)) {
                $menu['Accounting']['Journal']->addChild('Journal Voucher', array('route' => 'journal_voucher'));
            }else{
                $menu['Accounting']['Journal']->addChild('Double Entry', array('route' => 'account_double_entry'));
            }
            $menu['Accounting']['Journal']->addChild('Journal', array('route' => 'account_journal'));
            $menu['Accounting']['Journal']->addChild('Contra Account', array('route' => 'account_balancetransfer'));
            $menu['Accounting']['Journal']->addChild('Profit Withdrawal', array('route' => 'account_profit_withdrawal'));
            $menu['Accounting']['Journal']->addChild('Profit Generate', array('route' => 'account_profit'));
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_REPORT')) {

            $menu['Accounting']->addChild('Financial Report', array('route' => 'account_transaction'))
                
                ->setAttribute('dropdown', true);
            $accounting = array('inventory');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'report_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income',        array('route' => 'report_monthly_income'));
            }
            $accounting = array('e-commerce');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'report_income'));
                /* $menu['Accounting']['Financial Report']->addChild('Monthly Income',        array('route' => 'report_monthly_income'));*/
            }
            $restaurant = array('restaurant');
            $result = array_intersect($arrSlugs, $restaurant);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Daily Income', array('route' => 'report_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income', array('route' => 'report_monthly_income'));
            }

            $accounting = array('hms');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'hms_report_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income',array('route' => 'hms_report_monthly_income'));
            }
            $accounting = array('miss');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'account_medicine_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income', array('route' => 'account_medicine_income_monthly'));
            }
            $accounting = array('business');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'account_business_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income',        array('route' => 'account_business_income_monthly'));

            }
            $accounting = array('hotel');
            $result = array_intersect($arrSlugs, $accounting);
            if (!empty($result)) {
                $menu['Accounting']['Financial Report']->addChild('Income', array('route' => 'account_business_income'));
                $menu['Accounting']['Financial Report']->addChild('Monthly Income',        array('route' => 'account_business_income_monthly'));

            }
            $menu['Accounting']['Financial Report']->addChild('Trail Balance', array('route' => 'account_trail_balance'));
            $menu['Accounting']['Financial Report']->addChild('Balance Sheet', array('route' => 'account_balance_sheet'));

        }

        if ($securityContext->isGranted('ROLE_DOMAIN_ACCOUNTING_CONFIG')) {
            $menu['Accounting']->addChild('Master Data', array('route' => ''))
                
                ->setAttribute('dropdown', true);
            $menu['Accounting']['Master Data']->addChild('Account User', array('route' => 'account_user'));
            $menu['Accounting']['Master Data']->addChild('Bank Account', array('route' => 'accountbank'));
            $menu['Accounting']['Master Data']->addChild('Mobile Account', array('route' => 'accountmobilebank'));
            $menu['Accounting']['Master Data']->addChild('Configuration', array('route' => 'account_config_manage'));
            $menu['Accounting']['Master Data']->addChild('Account Head', array('route' => 'accounthead'));
        }

        return $menu;

    }

    public function InventorySalesMenu($menu)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $inventory = $securityContext->getGlobalOption()->getInventoryConfig();
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_SALES')) {

            $menu
            ->addChild('Sales')
            ->setAttribute('icon', 'fa fa-shopping-bag')
            ->setAttribute('dropdown', true);
            if($inventory->isPos() == 1 and $securityContext->isGranted('ROLE_POS')){
                $menu['Sales']->addChild('Pos', array('route' => 'pos_desktop'));
            }
            $deliveryProcess = $inventory->getDeliveryProcess();
            if ($inventory->isInvoice() == 1) {
                $menu['Sales']->addChild('New Invoice', array('route' => 'inventory_salesonline_new'));
            }
            $menu['Sales']->addChild('Sales', array('route' => 'inventory_salesonline'));
            if($inventory->isInvoice() == 1 and $securityContext->isGranted('ROLE_POS_ANDROID')){
                $menu['Sales']->addChild('Android Sales', array('route' => 'pos_android_sales'));
            }
           $menu['Sales']->addChild('Sales Return', array('route' => 'inventory_salesreturn'));
           $menu['Sales']->addChild('Sales Import', array('route' => 'inventory_salesimport'));

            if ($securityContext->isGranted('ROLE_CRM') or $securityContext->isGranted('ROLE_DOMAIN')) {
                $menu['Sales']->addChild('Customer', array('route' => 'domain_customer'));
            }
            if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_REPORT')) {
                $menu['Sales']->addChild('Reports')
                    ->setAttribute('dropdown', true);
                $menu['Sales']['Reports']->addChild('Sales Overview', array('route' => 'inventory_report_sales_overview'));
                $menu['Sales']['Reports']->addChild('Sales with price', array('route' => 'inventory_report_sales'));
                $menu['Sales']['Reports']->addChild('Sales Item Details', array('route' => 'inventory_report_sales_item_details'));
                $menu['Sales']['Reports']->addChild('Daily Sales', array('route' => 'inventory_report_daily_sales'));
                $menu['Sales']['Reports']->addChild('Monthly Sales', array('route' => 'inventory_report_monthly_sales'));
                $menu['Sales']['Reports']->addChild('Daily  Sales & Profit', array('route' => 'inventory_report_daily_sales_profit'));
                $menu['Sales']['Reports']->addChild('Monthly  Sales & Profit', array('route' => 'inventory_report_monthly_sales_profit'));
                $menu['Sales']['Reports']->addChild('Sales with price', array('route' => 'inventory_report_sales'));
                $menu['Sales']['Reports']->addChild('Periodic Sales Item', array('route' => 'inventory_report_sales_item'));
                $menu['Sales']['Reports']->addChild('Sales by User', array('route' => 'inventory_report_sales_user'));
                $menu['Sales']['Reports']->addChild('User Sales Target', array('route' => 'inventory_report_sales_user_target'));
            }
        }
        return $menu;

    }

    public function InventoryMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $inventory = $securityContext->getGlobalOption()->getInventoryConfig();
        $menu
            ->addChild('Inventory')
            ->setAttribute('icon', 'icon-archive')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')) {

            $menu['Inventory']->addChild('Manage Purchase', array('route' => 'purchase'))
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Purchase']->addChild('Purchase', array('route' =>'inventory_purchasesimple'));
            $menu['Inventory']['Manage Purchase']->addChild('Add Purchase', array('route' =>'inventory_purchasesimple_new'))
                ;
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Return', array('route' => 'inventory_purchasereturn'))
                ;
            $menu['Inventory']['Manage Purchase']->addChild('Purchase Import', array('route' => 'inventory_excelimproter'))
                ;
            $menu['Inventory']['Manage Purchase']->addChild('Vendor', array('route' => 'inventory_vendor'));
            $menu['Inventory']['Manage Purchase']->addChild('Pre-purchase', array('route' => 'prepurchaseitem'));
	        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_REPORT')) {
            $menu['Inventory']['Manage Purchase']->addChild('Reports')

                ->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Purchase']['Reports']->addChild('Purchase with price', array('route' => 'inventory_report_purchase'));
            }
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_STOCK')) {

            $menu['Inventory']->addChild('Manage Stock')->setAttribute('dropdown', true);
            $menu['Inventory']['Manage Stock']->addChild('Add Item', array('route' => 'item_new'))
                ;
            $menu['Inventory']['Manage Stock']->addChild('Stock Item', array('route' => 'inventory_item'))
                ;
            $menu['Inventory']['Manage Stock']->addChild('Category', array('route' => 'inventory_category'));
            $menu['Inventory']['Manage Stock']->addChild('Purchase Item', array('route' => 'inventory_purchaseitem'));
            $menu['Inventory']['Manage Stock']->addChild('Barcode wise Stock', array('route' => 'inventory_barcode_branch_stock'));
            $menu['Inventory']['Manage Stock']->addChild('Barcode Stock Details', array('route' => 'inventory_barcode_stock'));
            $menu['Inventory']['Manage Stock']->addChild('Stock Item Details', array('route' => 'inventory_stockitem'));
            $menu['Inventory']['Manage Stock']->addChild('Stock Short List', array('route' => 'inventory_stockitem_short_list'));
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_MANAGER')) {
            if ($inventory->getBarcodePrint() == 1) {
                $menu['Inventory']['Manage Stock']->addChild('Barcode Print', array('route' => 'inventory_barcode'))
                    ;
            }
            $menu['Inventory']['Manage Stock']->addChild('Stock Adjustment', array('route' => 'inv_stock_adjustment'));
            $menu['Inventory']['Manage Stock']->addChild('Damage', array('route' => 'inventory_damage'));

            $menu['Inventory']->addChild('Master Data', array('route' => ''))
                ->setAttribute('dropdown', true);
            $menu['Inventory']['Master Data']->addChild('Master Item', array('route' => 'inventory_product'));
            $menu['Inventory']['Master Data']->addChild('Item Import', array('route' => 'inventory_excelimproter'));
           // $menu['Inventory']['Master Data']->addChild('Item category', array('route' => 'itemtypegrouping_edit', 'routeParameters' => array('id' => $inventory->getId())));
            $menu['Inventory']['Master Data']->addChild('Brand', array('route' => 'itembrand'));;
            $menu['Inventory']['Master Data']->addChild('Size Group', array('route' => 'itemsize_group'));;
            if ($inventory->getIsBranch() == 1) {
                $menu['Inventory']['Master Data']->addChild('Branches')->setAttribute('icon', 'icon-building')->setAttribute('dropdown', true);
                $menu['Inventory']['Master Data']['Branches']->addChild('Branch Shop', array('route' => 'appsetting_branchshop'));
            }
        }

        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_CONFIG')) {

            $menu['Inventory']->addChild('Configuration', array('route' => 'inventoryconfig_edit'))
                ;
            $menu['Inventory']->addChild('User Sales Setup', array('route' => 'inventory_sales_user'))
                ;
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_INVENTORY_REPORT')) {

            $menu['Inventory']->addChild('Reports')

                ->setAttribute('dropdown', true);
            $menu['Inventory']['Reports']->addChild('System Overview', array('route' => 'inventory_report_overview'));
            $menu['Inventory']['Reports']->addChild('Item Overview', array('route' => 'inventory_report_stock_item'));
            $menu['Inventory']['Reports']->addChild('Till Stock', array('route' => 'inventory_report_till_stock'));
            $menu['Inventory']['Reports']->addChild('Periodic Stock', array('route' => 'inventory_report_periodic_stock'));
            $menu['Inventory']['Reports']->addChild('Operational Stock', array('route' => 'inventory_report_operational_stock'));
            $menu['Inventory']['Reports']->addChild('Group Stock', array('route' => 'inventory_report_group_stock'));
            $menu['Inventory']['Reports']->addChild('Purchase with price', array('route' => 'inventory_report_purchase'));

        }
        return $menu;

    }

    public function EcommerceMenu($menu,$apps)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $menu
            ->addChild('E-commerce')
            ->setAttribute('icon', 'icon  icon-shopping-cart')
            ->setAttribute('dropdown', true);



        /*$menu['E-commerce']->addChild('Transaction', array('route' => ''))
            ->setAttribute('icon','fa fa-bookmark')
            ->setAttribute('dropdown', true);
        $menu['E-commerce']['Transaction']->addChild('Order',        array('route' => 'customer_order'));
        $menu['E-commerce']['Transaction']->addChild('Pre-order',    array('route' => 'customer_preorder'));
        */

        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_ORDER')) {

            $menu['E-commerce']->addChild('Order', array('route' => ''))

                ->setAttribute('dropdown', true);
            $menu['E-commerce']['Order']->addChild('Order', array('route' => 'customer_order'));
            $menu['E-commerce']['Order']->addChild('Customer', array('route' => 'ecommerce_customer'));
            $menu['E-commerce']['Order']->addChild('New Order', array('route' => 'customer_order_new'));
           /* $menu['E-commerce']['Order']->addChild('Order Return', array('route' => 'customer_order'));*/
            $menu['E-commerce']['Order']->addChild('Pre-order', array('route' => 'customer_preorder'));

        }

        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_PRODUCT')) {
            $menu['E-commerce']->addChild('Product', array('route' => ''))

                ->setAttribute('dropdown', true);

		    $menu['E-commerce']['Product']->addChild('Product', array('route' => 'ecommerce_item'));
            if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_MANAGER')) {
                $menu['E-commerce']['Product']->addChild('Promotion', array('route' => 'ecommerce_promotion'));
                $menu['E-commerce']['Product']->addChild('Discount', array('route' => 'ecommerce_discount'));
                $menu['E-commerce']->addChild('Category', array('route' => 'ecommerce_category'));
                $menu['E-commerce']->addChild('Brand', array('route' => 'ecommerce_brand'));
                $menu['E-commerce']->addChild('Coupon', array('route' => 'ecommerce_coupon'));
            }

        }
        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_SETTING') && $securityContext->isGranted('ROLE_ECOMMERCE')){
            $menu['E-commerce']->addChild('Page Feature')->setAttribute('dropdown', true);
            $menu['E-commerce']['Page Feature']->addChild('Widget', array('route' => 'appearancefeaturewidget'));
            $menu['E-commerce']['Page Feature']->addChild('Feature', array('route' => 'appearancefeature'));
        }
        if ($securityContext->isGranted('ROLE_DOMAIN_ECOMMERCE_SETTING')) {
            $menu['E-commerce']->addChild('Configuration', array('route' => 'ecommerce_config_modify'));
            $menu['E-commerce']->addChild('Master Data', array('route' => ''))
                ->setAttribute('dropdown', true);
            $menu['E-commerce']['Master Data']->addChild('Product Import', array('route' => 'ecommerce_itemimporter'));
            $menu['E-commerce']['Master Data']->addChild('Delivery Location', array('route' => 'ecommerce_location'));
            $menu['E-commerce']['Master Data']->addChild('Delivery Time', array('route' => 'ecommerce_delivertime'));
            $menu['E-commerce']['Master Data']->addChild('Category Attribute', array('route' => 'ecommerce_itemattribute'));
            $menu['E-commerce']['Master Data']->addChild('Frontend Customize', array('route' => 'template_ecommerce_edit'));
          }
        return $menu;
    }

    public function PayrollMenu($menu)
    {

        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();
        $global = $securityContext->getGlobalOption();
        $menu
            ->addChild('HR & Payroll')
            ->setAttribute('icon', 'icon-user')
            ->setAttribute('dropdown', true);
        if($global->getIsBranch() == 1) {
            $menu['HR & Payroll']->addChild('Branch', array('route' => 'domain_branches'));
        }
        if ($securityContext->isGranted('ROLE_HR_EMPLOYEE')) {
            $menu['HR & Payroll']->addChild('System Users', array('route' => 'domain_user'));
        }
        if ($securityContext->isGranted('ROLE_HR_ATTENDANCE')) {
            $menu['HR & Payroll']->addChild('Attendance')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Attendance']->addChild('Daily Sheet', array('route' => 'attendance'));
            $menu['HR & Payroll']['Attendance']->addChild('Leave Setup', array('route' => 'leave_setup'));
            $menu['HR & Payroll']['Attendance']->addChild('Daily Attendance', array('route' => 'daily_attendance'));
            $menu['HR & Payroll']['Attendance']->addChild('Calendar Weekend', array('route' => 'weekend'));
        }
        if ($securityContext->isGranted('ROLE_PAYROLL_SALARY')) {

            $menu['HR & Payroll']->addChild('Payroll')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Payroll']->addChild('Salary Transaction', array('route' => 'account_paymentsalary'));
            $menu['HR & Payroll']['Payroll']->addChild('Payroll Generate', array('route' => 'payroll'));
            $menu['HR & Payroll']['Payroll']->addChild('Manage Employee', array('route' => 'employee_payroll'));

        }

        if ($securityContext->isGranted('ROLE_PAYROLL_SETTING')) {

            $menu['HR & Payroll']->addChild('Setting')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Setting']->addChild('Payroll Setting', array('route' => 'payrollsetting'));
            $menu['HR & Payroll']['Setting']->addChild('Department', array('route' => 'hrdepartment'));
            $menu['HR & Payroll']['Setting']->addChild('Leave Policy', array('route' => 'leavepolicy'));
            $menu['HR & Payroll']['Setting']->addChild('Leave Setting', array('route' => 'payrollsetting'));

        }

        /*if ($securityContext->isGranted('ROLE_ADMIN')) {

            $menu['HR & Payroll']->addChild('Manage Agent')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Manage Agent']->addChild('Agent New', array('route' => 'agent_new'));
            $menu['HR & Payroll']['Manage Agent']->addChild('Agent', array('route' => 'agent'));
            $menu['HR & Payroll']->addChild('Agent Payroll')->setAttribute('dropdown', true);
            $menu['HR & Payroll']['Agent Payroll']->addChild('Agent Transaction', array('route' => 'agentpayment'));
            $menu['HR & Payroll']['Agent Payroll']->addChild('Agent Invoice', array('route' => 'agentpayment_invoice'));
        }*/
        return $menu;

    }

    public function manageDomainInvoiceMenu($menu)
    {
        $securityContext = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu
            ->addChild('Invoice Sms & Email')
            ->setAttribute('icon', 'fa fa-phone')
            ->setAttribute('dropdown', true);
        if ($securityContext->isGranted('ROLE_SMS_MANAGER')) {
            $menu['Invoice Sms & Email']->addChild('Manage Sms')->setAttribute('dropdown', true);
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Logs', array('route' => 'smssender'));
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Sms Bundle', array('route' => 'invoicesmsemail'));
            $menu['Invoice Sms & Email']->addChild('Invoice Application', array('route' => 'invoicemodule_domain'));
        }
        if ($securityContext->isGranted('ROLE_SMS_BULK')) {
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Bulk Sms', array('route' => 'smsbulk'));
        }
        if ($securityContext->isGranted('ROLE_SMS_CONFIG')) {
            $menu['Invoice Sms & Email']['Manage Sms']->addChild('Notification Setup', array('route' => 'domain_notificationconfig'));
        }
        return $menu;
    }

    public function megaMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menus = $this->container->get('doctrine')->getRepository('SettingAppearanceBundle:MegaMenu')->getActiveMenus();
        $categoryRepository = $this->container->get('doctrine')->getRepository('ProductProductBundle:Category');
        foreach ($menus as $item) {
            /** @var MegaMenu $item */
            $menuName = $item->getName();
            $menu
                ->addChild($menuName)
                ->setAttribute('dropdown', true);
            $this->buildChildMenus($menu[$menuName], $categoryRepository->buildCategoryGroup($item->getCategories()));
            $this->buildCollectionMenu($menu[$menuName], $item->getCollections());
            $this->buildBrandMenu($menu[$menuName], $item->getBrands());
        }

        return $menu;
    }

    private function buildChildMenus(ItemInterface $menu, $categories)
    {

        foreach ($categories as $category) {

            /** var Category $category */
            $categoryName = $category['name'];

            if (!empty($categoryName)) {

                $menu
                    ->addChild($categoryName, array('route' => 'frontend_category',
                        'routeParameters' => array('slug' => $category['slug'])
                    ))
                    ->setAttribute('icon', 'fa fa-angle-right');

                if (!empty($category['__children'])) {
                    $menu->setAttribute('dropdown', true);
                    $menu[$categoryName]->setChildrenAttribute('class', 'dropdown-menu');
                    $this->buildChildMenus($menu[$categoryName], $category['__children']);
                }
            }
        }
    }

    private function buildBrandMenu(ItemInterface $menu, $brands)
    {
        $menu
            ->addChild('brands')
            ->setAttribute('brands', true)
            ->setAttribute('class', 'col-md-12 nav-brands');
        foreach ($brands as $brand) {
            /** @var Branding $brand */
            $menu['brands']->addChild($brand->getName(), array('route' => 'frontend_brand',
                'routeParameters' => array('slug' => $brand->getSlug())
            ))
                ->setAttribute('brand', true)
                ->setAttribute('icon', $brand->getAbsolutePath());;
        }
    }

    private function buildCollectionMenu(ItemInterface $menu, $collections)
    {

        if ($collections->count() > 0) {

            $menu
                ->addChild('collection');

            foreach ($collections as $collection) {
                /** @var Branding $brand */
                $menu['collection']->addChild($collection->getName(), array('route' => 'frontend_collection',
                    'routeParameters' => array('slug' => $collection->getSlug())
                ));
            }
        }

    }

    protected function getCategoryList()
    {
        $repo = $this->container->get('doctrine')->getRepository('ProductProductBundle:Category');
        $options = array(
            'decorate' => false,
            'representationField' => 'slug',
            'html' => false
        );

        return $repo->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* true: load all children, false: only direct */
            $options
        );
    }

}
