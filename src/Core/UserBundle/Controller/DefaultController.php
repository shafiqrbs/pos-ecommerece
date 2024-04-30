<?php

namespace Core\UserBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\AppModule;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UserBundle:Default:index.html.twig', array());
    }

    public function landingAction()
    {
        $user = $this->getUser();
        if(empty($user)){
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $globalOption = $user->getGlobalOption();
        /* @var $globalOption GlobalOption */

        if($globalOption->getStatus() == 2 or $globalOption->getStatus() == 3 ) {

            $this->get('security.context')->setToken(null);
            $this->get('request')->getSession()->invalidate();
            $this->get('session')->getFlashBag()->add('notice', "Your account has been temporary suspended. Please contact administrator for further any query");
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }elseif($globalOption->getStatus() == 4 ){
            $this->get('security.context')->setToken(null);
            $this->get('request')->getSession()->invalidate();
            $this->get('session')->getFlashBag()->add(
                'error',"Your account has been deleted. Please contact administrator for further any query"
            );
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }else{
            $enable = $globalOption->getStatus();
        }

        $apps = array();
        if (!empty($globalOption ->getSiteSetting())) {

        	$modules = $globalOption->getSiteSetting()->getAppModules();

            /* @var AppModule $mod */

            foreach ($modules as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $apps[] = $mod->getSlug();
                }
            }
        }

        $mainApp = !empty($globalOption->getMainApp()) ? $globalOption->getMainApp()->getSlug() : "";

	    if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->redirect($this->generateUrl('tools_domain'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN') && $enable != 1) {
            return $this->redirect($this->generateUrl('bindu_build'));
	    }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_REPORT_MANAGEMENT') && $enable == 1) {
            return $this->redirect($this->generateUrl('report_management_dashboard'));
	    }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_INVENTORY') && $enable == 1 && $mainApp == 'inventory') {
        	return $this->redirect($this->generateUrl('inventory_homepage'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_ECOMMERCE') && $enable == 1 && $mainApp == 'e-commerce') {
	        return $this->redirect($this->generateUrl('ecommerce_dashboard'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_WEBSITE') && $enable == 1 && $mainApp == 'website') {
            return $this->redirect($this->generateUrl('website'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_WEBSITE') && $enable == 1 && $mainApp == 'accounting') {
            return $this->redirect($this->generateUrl('account_transaction_cash_summary'));
        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_DOMAIN') && $enable == 1) {
	        return $this->redirect($this->generateUrl('domain'));
        }elseif (!empty($user) && $enable == 2 ) {
            return $this->redirect($this->generateUrl('domain_pendig'));
        }elseif (!empty($user) && $enable == 3 ) {
            return $this->redirect($this->generateUrl('domain_suspended'));
        }else{
            return $this->redirect($this->generateUrl('home'));
        }

    }

    public function lockAction(){
        return $this->render('UserBundle:Default:lock.html.twig', array());
    }

    public function adminAction()
    {
        $user = $this->getUser();
        $em = $this->get('doctrine.orm.entity_manager');
        return $this->render('UserBundle:Default:admin.html.twig', array(
            'user' => $user
        ));

    }

    public function userAction()
    {
        $user = $this->getUser();
        $em = $this->get('doctrine.orm.entity_manager');

        return $this->render('UserBundle:Default:admin.html.twig', array(
            'user' => $user
        ));

    }
    public function domainAction()
    {

        /* @var GlobalOption $globalOption */
        $globalOption = $this->getUser()->getGlobalOption();
        $modules = $globalOption->getSiteSetting()->getAppModules();
        $apps = array();
        if (!empty($globalOption ->getSiteSetting()) and !empty($modules)) {
            /* @var AppModule $mod */
            foreach ($modules as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $apps[] = $mod->getSlug();
                }
            }
        }
        return $this->render('UserBundle:Default:domain.html.twig', array(
            'globalOption' => $globalOption,
             'apps' => $apps
        ));
    }

    public function websiteAction()
    {
        /* @var GlobalOption $globalOption */
        $globalOption = $this->getUser()->getGlobalOption();
        $modules = $globalOption->getSiteSetting()->getAppModules();
        $apps = array();
        if (!empty($globalOption ->getSiteSetting()) and !empty($modules)) {
            /* @var AppModule $mod */
            foreach ($modules as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $apps[] = $mod->getSlug();
                }
            }
        }
        return $this->render('UserBundle:Default:domain.html.twig', array(
            'globalOption' => $globalOption,
            'apps' => $apps
        ));

    }

    public function hospitalAction()
    {
        /* @var GlobalOption $globalOption */
        $globalOption = $this->getUser()->getGlobalOption();
        $modules = $globalOption->getSiteSetting()->getAppModules();
        $apps = array();
        if (!empty($globalOption ->getSiteSetting()) and !empty($modules)) {
            /* @var AppModule $mod */
            foreach ($modules as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $apps[] = $mod->getSlug();
                }
            }
        }
        return $this->render('UserBundle:Default:domain.html.twig', array(
            'globalOption' => $globalOption,
            'apps' => $apps
        ));

    }


    public function pendingAction()
    {
        $user = $this->getUser();
        return $this->render('UserBundle:Default:pending.html.twig', array(
            'user' => $user,
        ));
    }

    public function suspendedAction()
    {
        $user = $this->getUser();
        return $this->render('UserBundle:Default:lock.html.twig', array(
            'user' => $user,
        ));
    }

    public function vendorAction()
    {

        $user = $this->getUser();
        $em = $this->get('doctrine.orm.entity_manager');
        return $this->render('UserBundle:Default:domain.html.twig', array(
            'user' => $user
        ));
    }
    public function tutorAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        return $this->render('UserBundle:Default:index.html.twig', array());
    }

    public function generalAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        return $this->render('UserBundle:Default:index.html.twig', array());
    }


}