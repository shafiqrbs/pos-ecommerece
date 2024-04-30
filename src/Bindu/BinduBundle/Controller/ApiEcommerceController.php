<?php

namespace Bindu\BinduBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Entity\CustomerAddress;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use Gregwar\Image\Image;
use Setting\Bundle\ContentBundle\Entity\Page;
use Setting\Bundle\ToolBundle\Entity\Designation;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\MobileAppVersion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiEcommerceController extends Controller
{

    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10  /*limit per page*/
        );
        return $pagination;
    }

    public function resizeFilter($pathToImage, $width = 520, $height = 520)
    {
        $path = '/' . Image::open(__DIR__.'/../../../../../web/' . $pathToImage)->cropResize($width, $height, 'transparent', 'top', 'left')->guess();
        return $_SERVER['HTTP_HOST'].$path;
    }

    public function checkApiValidation($request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $unique = $request->headers->get('X-API-SECRET');
        $setup = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $unique,'status'=>1));
        if (!empty($setup) and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            return $setup;
        }
        return "invalid";
    }

    public function checkApiWithoutSecretValidation($request)
    {

        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        if ($request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            return "valid";
        }
        return "invalid";
    }

    public function portalStoreAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);

        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $result = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findBy(array('isPortalStore'=>1));
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $data = array();
            if($result) {

                /* @var $entity GlobalOption */
                foreach ($result as $key => $entity) {

                    $address = '';
                    $vatRegNo = '';
                    $vatPercentage = '';
                    $vatEnable = '';
                    $productColumn = $entity->getEcommerceConfig()->getMobileProductColumn();
                    $productFeatureColumn = $entity->getEcommerceConfig()->getMobileFeatureColumn();
                    $currency = $entity->getEcommerceConfig()->getCurrency();
                    $address = $entity->getEcommerceConfig()->getAddress();
                    $preOrder = $entity->getEcommerceConfig()->getIsPreorder();
                    $cartProcess = $entity->getEcommerceConfig()->getCartProcess();
                    $shippingCharge = $entity->getEcommerceConfig()->getShippingCharge();
                    $cashOnDelivery = $entity->getEcommerceConfig()->isCashOnDelivery();
                    $pickupLocation = $entity->getEcommerceConfig()->getPickupLocation();
                    $relatedProductMode = $entity->getEcommerceConfig()->getRelatedProductMode();
                    $productMode = $entity->getEcommerceConfig()->getProductMode();

                    $logo = $entity->getTemplateCustomize()->getLogo();
                    $bgImage = $entity->getTemplateCustomize()->getBgImage();
                    $mobile = empty($entity->getHotline()) ? $entity->getMobile() : $entity->getHotline();
                    $data[$key]['name'] = $entity->getName();
                    $data[$key]['license'] = (int)$entity->getMobile();
                    $data[$key]['activeKey'] = $entity->getUniqueCode();
                    $data[$key]['setupId'] = $entity->getId();
                    $data[$key]['description'] = $entity->getDescription();
                    $data[$key]['mobile'] = $mobile;
                    $data[$key]['address'] = $address;
                    $data[$key]['email'] = $entity->getEmail();
                    $data[$key]['website'] = $entity->getDomain();
                    $data[$key]['locationName'] =  $entity->getLocation()->getName();
                    $data[$key]['vatRegNo'] = $vatRegNo;
                    $data[$key]['vatPercentage'] = $vatPercentage;
                    $data[$key]['productColumn'] = $productColumn;
                    $data[$key]['productFeatureColumn'] = $productFeatureColumn;
                    $data[$key]['currency'] = $currency;
                    $data[$key]['preOrder'] = $preOrder;
                    $data[$key]['cartProcess'] = $cartProcess;
                    $data[$key]['shippingCharge'] = $shippingCharge;
                    $data[$key]['cashOnDelivery'] = $cashOnDelivery;
                    $data[$key]['pickupLocation'] = $pickupLocation;
                    $data[$key]['vatEnable'] = $vatEnable;
                    $data[$key]['logo']      = empty($logo) ? '' : $_SERVER['HTTP_HOST']."/{$entity->getTemplateCustomize()->getUploadDir()}/{$logo}";
                    $data[$key]['backgroundImage']      = empty($bgImage) ? '' :  $_SERVER['HTTP_HOST']."/{$entity->getTemplateCustomize()->getUploadDir()}/{$bgImage}";
                    $data[$key]['productMode']      = empty($productMode) ? 'grid' : $productMode;
                    $data[$key]['relatedProductMode']      =  $relatedProductMode;
                }
            }
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    function hex6ToHex8($hex6) {
        return str_replace("#","0xFF",$hex6);
    }

    function random_color_code() {
        $red = str_pad(dechex(rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $green = str_pad(dechex(rand(0, 255)), 2, '0', STR_PAD_LEFT);
        $blue = str_pad(dechex(rand(0, 255)), 2, '0', STR_PAD_LEFT);
        return '#' . $red . $green . $blue;
    }

    public function stringNullChecker($var){
        if (is_null($var)) {
            return "";
        } else {
            return (string) $var;
        }
    }

    public function numberNullChecker($var){
        if (is_null($var)) {
            return 0;
        } else {
            return (float) $var;
        }
    }

    public function imageBase64($path){
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function setupAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $uniqueCode = $formData['activeKey'];
        $mobile = $formData['license'];
        $data = array();
        $entity = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $uniqueCode,'mobile' => $mobile,'status'=>1));
        if (empty($entity) and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
                return new Response('Unauthorized access.', 401);
        }else{

            /* @var $entity GlobalOption */

            $address = '';
            $vatRegNo = '';
            $vatPercentage = '';
            $vatEnable = '';

            $productColumn = $entity->getEcommerceConfig()->getMobileProductColumn();
            $productFeatureColumn = $entity->getEcommerceConfig()->getMobileFeatureColumn();
            $currency = $entity->getEcommerceConfig()->getCurrency();
            $address = $entity->getEcommerceConfig()->getAddress();
            $preOrder = $entity->getEcommerceConfig()->getIsPreorder();
            $cartProcess = $entity->getEcommerceConfig()->getCartProcess();
            $shippingCharge = $entity->getEcommerceConfig()->getShippingCharge();
            $cashOnDelivery = $entity->getEcommerceConfig()->isCashOnDelivery();
            $pickupLocation = $entity->getEcommerceConfig()->getPickupLocation();

            $showProductCategory = $entity->getEcommerceConfig()->isShowCategory();
            $showProductBrand = $entity->getEcommerceConfig()->getShowBrand();
            $customTheme = $entity->getEcommerceConfig()->isCustomTheme();
            $productTheme = $entity->getEcommerceConfig()->getProductTheme();
            $orderPoint = $entity->getEcommerceConfig()->isOrderPoint();
            $uploadFile = $entity->getEcommerceConfig()->isUploadFile();
            $homeFeatureSlider = $entity->getEcommerceConfig()->isAppHomeSlider();
            $homeFeatureCategory = $entity->getEcommerceConfig()->isAppHomeFeatureCategory();
            $homeFeatureBrand = $entity->getEcommerceConfig()->isAppHomeFeatureBrand();
            $homeFeatureDiscount = $entity->getEcommerceConfig()->isAppHomeFeatureDiscount();
            $homeFeaturePromotion = $entity->getEcommerceConfig()->isAppHomeFeaturePromotion();


            $mobile = empty($entity->getHotline()) ? $entity->getMobile() : $entity->getHotline();
            $bodyBg = (string) trim($entity->getTemplateCustomize()->getBodyColor());
            $androidHeaderBg = (string) trim($entity->getTemplateCustomize()->getMobileHeaderBgColor());
            $appPrimaryColor = (string) trim($entity->getTemplateCustomize()->getAppPrimaryColor());
            $appSecondaryColor =(string) trim($entity->getTemplateCustomize()->getAppSecondaryColor());
            $appBarColor = (string) trim($entity->getTemplateCustomize()->getAppBarColor());
            $appTextTitle = (string) trim($entity->getTemplateCustomize()->getAppTextTitle());
            $appTextColor = (string) trim($entity->getTemplateCustomize()->getAppTextColor());
            $appCartColor = (string) trim($entity->getTemplateCustomize()->getAppCartColor());
            $appMoreColor = (string) trim($entity->getTemplateCustomize()->getAppMoreColor());
            $appDiscountColor = (string) trim($entity->getTemplateCustomize()->getAppDiscountColor());
            $appBorderActiveColor =(string) trim( $entity->getTemplateCustomize()->getAppBorderActiveColor());
            $appBorderInactiveColor =(string) trim( $entity->getTemplateCustomize()->getAppBorderInactiveColor());
            $appBorderColor =(string) trim( $entity->getTemplateCustomize()->getAppBorderColor());
            $appPositiveColor =(string) trim( $entity->getTemplateCustomize()->getAppPositiveColor());
            $appNegativeColor = (string) trim($entity->getTemplateCustomize()->getAppNegativeColor());
            $appIconColor = (string) trim($entity->getTemplateCustomize()->getAndroidIconColor());
            $appAnchorColor = (string) trim($entity->getTemplateCustomize()->getAndroidAnchorColor());
            $appAnchorHoverColor = (string) trim($entity->getTemplateCustomize()->getAndroidAnchorHoverColor());
            $searchPageBgColor = (string) trim($entity->getTemplateCustomize()->getSearchPageBgColor());
            $appFooterBgColor = (string) trim($entity->getTemplateCustomize()->getMobileFooterBgColor());
            $appFooterIconBgColor = (string) trim($entity->getTemplateCustomize()->getMobileFooterAnchorBg());
            $appFooterIconColor = (string) trim($entity->getTemplateCustomize()->getMobileFooterAnchorColor());
            $appFooterIconColorHover = (string) trim($entity->getTemplateCustomize()->getMobileFooterAnchorColorHover());
            $appSuccessColor = (string) trim($entity->getTemplateCustomize()->getAppSuccessColor());
            $appNoticeColor = (string) trim($entity->getTemplateCustomize()->getAppNoticeColor());
            $appCloseColor = (string) trim($entity->getTemplateCustomize()->getAppCloseColor());

            $morePageColor = (string) trim($entity->getTemplateCustomize()->getAppMoreColor());

            $tawk = (string) trim($entity->getTemplateCustomize()->getTawk());
            $pixel = (string) trim($entity->getTemplateCustomize()->getFacebookPixel());
            $messenger = (string) trim($entity->getTemplateCustomize()->getFbMessenger());
            $analytic = (string) trim($entity->getTemplateCustomize()->getGoogleAnalytic());

            $logo = empty($entity->getTemplateCustomize()->getWebPath('logo')) ? "" : $entity->getTemplateCustomize()->getWebPath('logo');
           //$introImage = empty($entity->getTemplateCustomize()->getWebPath('androidLogo')) ? '': $entity->getTemplateCustomize()->getWebPath('androidLogo');
           $logo = '';
           $introImage = '';

           $data = array(
                    'setupId' => $entity->getId(),
                    'uniqueCode' => $entity->getUniqueCode(),
                    'name' => $entity->getName(),
                    'mobile' => $mobile,
                    'tawk' => $this->stringNullChecker($tawk),
                    'pixel' => $this->stringNullChecker($pixel),
                    'messenger' => $this->stringNullChecker($messenger),
                    'analytic' => $this->stringNullChecker($analytic),
                    'email' => $this->stringNullChecker($entity->getEmail()),
                    'locationId' => $entity->getLocation()->getId(),
                    'address' => $this->stringNullChecker($address),
                    'locationName' => $this->stringNullChecker($entity->getLocation()->getName()),
                    'main_app' => $entity->getMainApp()->getId(),
                    'main_app_name' => $entity->getMainApp()->getSlug(),
                    'mainApp' => $entity->getMainApp()->getId(),
                    'mainAppName' => $entity->getMainApp()->getSlug(),
                    'appsManual' => $this->stringNullChecker($entity->getMainApp()->getApplicationManual()),
                    'website' => $this->stringNullChecker($entity->getDomain()),
                    'vatRegNo' =>  $this->stringNullChecker($vatRegNo),
                    'vatPercentage' =>  $this->numberNullChecker($vatPercentage),
                    'productColumn' =>  $this->numberNullChecker($productColumn),
                    'productFeatureColumn' =>  $this->numberNullChecker($productFeatureColumn),
                    'currency' => $currency,
                    'preOrder' => $this->numberNullChecker($preOrder),
                    'cartProcess' => $this->stringNullChecker($cartProcess),
                    'shippingCharge' => $this->numberNullChecker($shippingCharge),
                    'cashOnDelivery' => $this->numberNullChecker($cashOnDelivery),
                    'pickupLocation' =>  $this->numberNullChecker($pickupLocation),
                    'poweredBy' => empty($entity->getTemplateCustomize()->getPoweredBy()) ? '' : "Powered By {$entity->getTemplateCustomize()->getPoweredBy()}",
                    'introTitle' => empty($entity->getTemplateCustomize()->getIntroTitle()) ? '' : $entity->getTemplateCustomize()->getIntroTitle(),
                    'showProductCategory' => ($showProductCategory) ? 1:0,
                    'showProductBrand' => ($showProductBrand) ? 1:0,
                    'customTheme' => ($customTheme) ? 1:0,
                    'orderPoint' => ($orderPoint) ? 1:0,
                    'uploadFile' => ($uploadFile) ? 1:0,
                    'homeSlider' => ($homeFeatureSlider) ? 1:0,
                    'homeFeatureCategory' => ($homeFeatureCategory) ? 1:0,
                    'homeFeatureBrand' => ($homeFeatureBrand) ? 1:0,
                    'homeFeatureDiscount' => ($homeFeatureDiscount) ? 1:0,
                    'homeFeaturePromotion' => ($homeFeaturePromotion) ? 1:0,
                    'productTheme' => $productTheme,

                     'bodyBg' => empty($bodyBg) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($bodyBg),
                     'appHeaderBg' => empty($androidHeaderBg) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($androidHeaderBg),
                     'appPrimaryColor' => empty($appPrimaryColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appPrimaryColor),
                     'appSecondaryColor' => empty($appSecondaryColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appSecondaryColor),
                     'appBarColor' => empty($appBarColor) ? $this->hex6ToHex8($this->random_color_code()) :$this->hex6ToHex8($appBarColor) ,
                     'appTextTitle' => empty($appTextTitle)? $this->hex6ToHex8($this->random_color_code()) :$this->hex6ToHex8($appTextTitle) ,
                     'appTextColor' => empty($appTextColor)? $this->hex6ToHex8($this->random_color_code()) :$this->hex6ToHex8($appTextColor) ,
                     'appCartColor' => empty($appCartColor)? $this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appCartColor) ,
                     'appMoreColor' => empty($appMoreColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appMoreColor) ,
                     'appBorderColor' => empty($appBorderColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appBorderColor) ,
                     'appBorderActiveColor' => empty($appBorderActiveColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appBorderActiveColor) ,
                     'appBorderInactiveColor' => empty($appBorderInactiveColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appBorderInactiveColor) ,
                     'appPositiveColor' => empty($appPositiveColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appPositiveColor) ,
                     'appNegativeColor' => empty($appNegativeColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appNegativeColor) ,
                     'appDiscountColor' => empty($appDiscountColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appDiscountColor) ,
                     'appIconColor' => empty($appIconColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appIconColor) ,
                     'appAnchorColor' => empty($appAnchorColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appAnchorColor) ,
                     'appAnchorHoverColor' => empty($appAnchorHoverColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appAnchorHoverColor) ,
                     'searchPageBgColor' => empty($searchPageBgColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($searchPageBgColor) ,
                     'morePageBgColor' => empty($morePageColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($morePageColor) ,
                     'appFooterBgColor' => empty($appFooterBgColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appFooterBgColor) ,
                     'appFooterIconBgColor' => empty($appFooterIconBgColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appFooterIconBgColor) ,
                     'appFooterIconColor' => empty($appFooterIconColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appFooterIconColor) ,
                     'appFooterIconColorHover' => empty($appFooterIconColorHover)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appFooterIconColorHover) ,
                     'appSuccessColor' => empty($appSuccessColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8( $appSuccessColor) ,
                     'appNoticeColor' => empty($appNoticeColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appNoticeColor) ,
                     'appCloseColor' => empty($appCloseColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appCloseColor) ,
                    // 'logo'      =>  $this->imageBase64($logo),
                     'logo'      =>  '',
                     'intro'      =>  '',
                );
            }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    public function splashAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $uniqueCode = $formData['activeKey'];
        $mobile = $formData['license'];
        $data = array();
        $entity = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $uniqueCode,'mobile' => $mobile,'status'=>1));
        if (empty($entity) and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            return new Response('Unauthorized access.', 401);
        }else{

            /* @var $entity GlobalOption */

            $address = '';
            $vatRegNo = '';
            $vatPercentage = '';
            $vatEnable = '';

            $productColumn = $entity->getEcommerceConfig()->getMobileProductColumn();
            $productFeatureColumn = $entity->getEcommerceConfig()->getMobileFeatureColumn();
            $currency = $entity->getEcommerceConfig()->getCurrency();
            $address = $entity->getEcommerceConfig()->getAddress();
            $preOrder = $entity->getEcommerceConfig()->getIsPreorder();
            $cartProcess = $entity->getEcommerceConfig()->getCartProcess();
            $shippingCharge = $entity->getEcommerceConfig()->getShippingCharge();
            $cashOnDelivery = $entity->getEcommerceConfig()->isCashOnDelivery();
            $pickupLocation = $entity->getEcommerceConfig()->getPickupLocation();

            $showProductCategory = $entity->getEcommerceConfig()->isShowCategory();
            $showProductBrand = $entity->getEcommerceConfig()->getShowBrand();
            $customTheme = $entity->getEcommerceConfig()->isCustomTheme();
            $productTheme = $entity->getEcommerceConfig()->getProductTheme();
            $orderPoint = $entity->getEcommerceConfig()->isOrderPoint();
            $uploadFile = $entity->getEcommerceConfig()->isUploadFile();
            $homeFeatureSlider = $entity->getEcommerceConfig()->isAppHomeSlider();
            $homeFeatureCategory = $entity->getEcommerceConfig()->isAppHomeFeatureCategory();
            $homeFeatureBrand = $entity->getEcommerceConfig()->isAppHomeFeatureBrand();
            $homeFeatureDiscount = $entity->getEcommerceConfig()->isAppHomeFeatureDiscount();
            $homeFeaturePromotion = $entity->getEcommerceConfig()->isAppHomeFeaturePromotion();


            $mobile = empty($entity->getHotline()) ? $entity->getMobile() : $entity->getHotline();

            $androidHeaderBg = (string) trim($entity->getTemplateCustomize()->getMobileHeaderBgColor());
            $bodyBg = (string) trim($entity->getTemplateCustomize()->getBodyColor());
            $appPrimaryColor = (string) trim($entity->getTemplateCustomize()->getAppPrimaryColor());
            $appSecondaryColor =(string) trim($entity->getTemplateCustomize()->getAppSecondaryColor());
            $appBarColor = (string) trim($entity->getTemplateCustomize()->getAppBarColor());
            $appTextTitle = (string) trim($entity->getTemplateCustomize()->getAppTextTitle());
            $appTextColor = (string) trim($entity->getTemplateCustomize()->getAppTextColor());
            $appCartColor = (string) trim($entity->getTemplateCustomize()->getAppCartColor());
            $appMoreColor = (string) trim($entity->getTemplateCustomize()->getAppMoreColor());
            $appDiscountColor = (string) trim($entity->getTemplateCustomize()->getAppDiscountColor());
            $appBorderActiveColor =(string) trim( $entity->getTemplateCustomize()->getAppBorderActiveColor());
            $appBorderInactiveColor =(string) trim( $entity->getTemplateCustomize()->getAppBorderInactiveColor());
            $appBorderColor =(string) trim( $entity->getTemplateCustomize()->getAppBorderColor());
            $appPositiveColor =(string) trim( $entity->getTemplateCustomize()->getAppPositiveColor());
            $appNegativeColor = (string) trim($entity->getTemplateCustomize()->getAppNegativeColor());
            $appIconColor = (string) trim($entity->getTemplateCustomize()->getAndroidIconColor());
            $appAnchorColor = (string) trim($entity->getTemplateCustomize()->getAndroidAnchorColor());
            $appAnchorHoverColor = (string) trim($entity->getTemplateCustomize()->getAndroidAnchorHoverColor());
            $searchPageBgColor = (string) trim($entity->getTemplateCustomize()->getSearchPageBgColor());
            $appFooterBgColor = (string) trim($entity->getTemplateCustomize()->getMobileFooterBgColor());
            $appFooterIconBgColor = (string) trim($entity->getTemplateCustomize()->getMobileFooterAnchorBg());
            $appFooterIconColor = (string) trim($entity->getTemplateCustomize()->getMobileFooterAnchorColor());
            $appFooterIconColorHover = (string) trim($entity->getTemplateCustomize()->getMobileFooterAnchorColorHover());

            $appSuccessColor = (string) trim($entity->getTemplateCustomize()->getAppSuccessColor());
            $appNoticeColor = (string) trim($entity->getTemplateCustomize()->getAppNoticeColor());
            $appCloseColor = (string) trim($entity->getTemplateCustomize()->getAppCloseColor());
            $morePageColor = (string) trim($entity->getTemplateCustomize()->getAppMoreColor());
            $inputBgColor = (string) trim($entity->getTemplateCustomize()->getInputBgColor());
            $inputBgFocusColor = (string) trim($entity->getTemplateCustomize()->getInputBgFocusColor());

            $tawk = (string) trim($entity->getTemplateCustomize()->getTawk());
            $pixel = (string) trim($entity->getTemplateCustomize()->getFacebookPixel());
            $messenger = (string) trim($entity->getTemplateCustomize()->getFbMessenger());
            $analytic = (string) trim($entity->getTemplateCustomize()->getGoogleAnalytic());

            $logo = empty($entity->getTemplateCustomize()->getWebPath('logo')) ? "" : $entity->getTemplateCustomize()->getWebPath('logo');
            //$introImage = empty($entity->getTemplateCustomize()->getWebPath('androidLogo')) ? '': $entity->getTemplateCustomize()->getWebPath('androidLogo');
            $logo = '';
            $introImage = '';

            $data = array(
                'setupId' => $entity->getId(),
                'uniqueCode' => $entity->getUniqueCode(),
                'name' => $entity->getName(),
                'mobile' => $mobile,
                'tawk' => $this->stringNullChecker($tawk),
                'pixel' => $this->stringNullChecker($pixel),
                'messenger' => $this->stringNullChecker($messenger),
                'analytic' => $this->stringNullChecker($analytic),
                'email' => $this->stringNullChecker($entity->getEmail()),
                'locationId' => $entity->getLocation()->getId(),
                'address' => $this->stringNullChecker($address),
                'locationName' => $this->stringNullChecker($entity->getLocation()->getName()),
                'main_app' => $entity->getMainApp()->getId(),
                'main_app_name' => $entity->getMainApp()->getSlug(),
                'mainApp' => $entity->getMainApp()->getId(),
                'mainAppName' => $entity->getMainApp()->getSlug(),
                'appsManual' => $this->stringNullChecker($entity->getMainApp()->getApplicationManual()),
                'website' => $this->stringNullChecker($entity->getDomain()),
                'vatRegNo' =>  $this->stringNullChecker($vatRegNo),
                'vatPercentage' =>  $this->numberNullChecker($vatPercentage),
                'productColumn' =>  $this->numberNullChecker($productColumn),
                'productFeatureColumn' =>  $this->numberNullChecker($productFeatureColumn),
                'currency' => $currency,
                'preOrder' => $this->numberNullChecker($preOrder),
                'cartProcess' => $this->stringNullChecker($cartProcess),
                'shippingCharge' => $this->numberNullChecker($shippingCharge),
                'cashOnDelivery' => $this->numberNullChecker($cashOnDelivery),
                'pickupLocation' =>  $this->numberNullChecker($pickupLocation),
                'poweredBy' => empty($entity->getTemplateCustomize()->getPoweredBy()) ? '' : "Powered By {$entity->getTemplateCustomize()->getPoweredBy()}",
                'introTitle' => empty($entity->getTemplateCustomize()->getIntroTitle()) ? '' : $entity->getTemplateCustomize()->getIntroTitle(),
                'siteTermsCondition' => empty($entity->getTemplateCustomize()->getSiteTermsCondition()) ? '' : $entity->getTemplateCustomize()->getSiteTermsCondition(),
                'siteTermsConditionBn' => empty($entity->getTemplateCustomize()->getSiteTermsConditionBn()) ? '' : $entity->getTemplateCustomize()->getSiteTermsConditionBn(),
                'showProductCategory' => ($showProductCategory) ? 1:0,
                'showProductBrand' => ($showProductBrand) ? 1:0,
                'customTheme' => ($customTheme) ? 1:0,
                'orderPoint' => ($orderPoint) ? 1:0,
                'uploadFile' => ($uploadFile) ? 1:0,
                'homeSlider' => ($homeFeatureSlider) ? 1:0,
                'homeFeatureCategory' => ($homeFeatureCategory) ? 1:0,
                'homeFeatureBrand' => ($homeFeatureBrand) ? 1:0,
                'homeFeatureDiscount' => ($homeFeatureDiscount) ? 1:0,
                'homeFeaturePromotion' => ($homeFeaturePromotion) ? 1:0,
                'productTheme' => $productTheme,

                'bodyBg' => empty($bodyBg) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($bodyBg),
                'appHeaderBg' => empty($androidHeaderBg) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($androidHeaderBg),
                'appPrimaryColor' => empty($appPrimaryColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appPrimaryColor),
                'appSecondaryColor' => empty($appSecondaryColor) ? $this->hex6ToHex8($this->random_color_code()) : $this->hex6ToHex8($appSecondaryColor),
                'appBarColor' => empty($appBarColor) ? $this->hex6ToHex8($this->random_color_code()) :$this->hex6ToHex8($appBarColor) ,
                'appTextTitle' => empty($appTextTitle)? $this->hex6ToHex8($this->random_color_code()) :$this->hex6ToHex8($appTextTitle) ,
                'appTextColor' => empty($appTextColor)? $this->hex6ToHex8($this->random_color_code()) :$this->hex6ToHex8($appTextColor) ,
                'appCartColor' => empty($appCartColor)? $this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appCartColor) ,
                'appMoreColor' => empty($appMoreColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appMoreColor) ,
                'appBorderColor' => empty($appBorderColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appBorderColor) ,
                'appBorderActiveColor' => empty($appBorderActiveColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appBorderActiveColor) ,
                'appBorderInactiveColor' => empty($appBorderInactiveColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appBorderInactiveColor) ,
                'appPositiveColor' => empty($appPositiveColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appPositiveColor) ,
                'appNegativeColor' => empty($appNegativeColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appNegativeColor) ,
                'appDiscountColor' => empty($appDiscountColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appDiscountColor) ,
                'appIconColor' => empty($appIconColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appIconColor) ,
                'appAnchorColor' => empty($appAnchorColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appAnchorColor) ,
                'appAnchorHoverColor' => empty($appAnchorHoverColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appAnchorHoverColor) ,
                'searchPageBgColor' => empty($searchPageBgColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($searchPageBgColor) ,
                'morePageBgColor' => empty($morePageColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($morePageColor) ,
                'appFooterBgColor' => empty($appFooterBgColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appFooterBgColor) ,
                'appFooterIconBgColor' => empty($appFooterIconBgColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appFooterIconBgColor) ,
                'appFooterIconColor' => empty($appFooterIconColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appFooterIconColor) ,
                'appFooterIconColorHover' => empty($appFooterIconColorHover)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appFooterIconColorHover) ,
                'appSuccessColor' => empty($appSuccessColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8( $appSuccessColor) ,
                'appNoticeColor' => empty($appNoticeColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appNoticeColor) ,
                'appCloseColor' => empty($appCloseColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($appCloseColor) ,
                'inputBgColor' => empty($inputBgColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($inputBgColor) ,
                'inputBgFocusColor' => empty($inputBgFocusColor)?$this->hex6ToHex8($this->random_color_code()):$this->hex6ToHex8($inputBgFocusColor) ,
                // 'logo'      =>  $this->imageBase64($logo),
                'logo'      =>  '',
                'intro'      =>  '',
            );
        }

        $slides = array();
        $feature = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->getFeatureWidget($entity,"Home");
        if($feature){
            $slides = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->getFeatureSlider($feature);
        }
        $introPages = array();
        $featureIntor = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->findOneBy(array('globalOption' => $entity, 'position'=> 'mobile-intro'));
        if($featureIntor){
            $introPages = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->getAppIntroSlider($featureIntor);
        }

        $products = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllFeatureProduct($entity);
        $categories = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllCategory($entity);
        $brands = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllBrand($entity);
        $promotions = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiPromotion($entity);
        $discounts = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiDiscount($entity);
        $pages = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->getPages($entity);
        $version = $this->getDoctrine()->getRepository(MobileAppVersion::class)->find(1);

        $jsonData = array(
            'version' => ($version) ? $version->getVersion() : 0,
            'setup' => $data,
            'introPages' => $introPages,
            'slides' => $slides,
            'categories' => $categories,
            'brands' => $brands,
            'promotions' => $promotions,
            'discounts' => $discounts,
            'products' => $products,
            'pages' => $pages,

        );
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($jsonData));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }


    public function productAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {
            return new Response('Unauthorized access.', 401);
        }else{
            /* @var $entity GlobalOption */
            $search = $_REQUEST;
            $entity = $this->checkApiValidation($request);
            // $feature_products = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllFeatureProduct($entity);
            $products = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiProduct($entity,$search);
            /*$jsonData = array(
                'feature_products' => $feature_products,
                'products' => $products,
            );*/
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($products));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;

        }

    }



    public function configurationAction(Request $request)
    {

        $formData = $request->request->all();
        $key =  $this->getParameter('x-api-key');
        $value =  $this->getParameter('x-api-value');
        $uniqueCode = $formData['activeKey'];
        $mobile = $formData['license'];
        $data = array();
        $entity = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode' => $uniqueCode,'mobile' => $mobile,'status'=>1));
        if (empty($entity) and $request->headers->get('X-API-KEY') == $key and $request->headers->get('X-API-VALUE') == $value) {
            return new Response('Unauthorized access.', 401);
        }else{

            /* @var $option GlobalOption */

            $address = '';
            $vatRegNo = '';
            $vatPercentage = '';
            $vatEnable = '';

            $entity = $option->getEcommerceConfig();

            $productColumn = $entity->getEcommerceConfig()->getMobileProductColumn();
            $productFeatureColumn = $entity->getEcommerceConfig()->getMobileFeatureColumn();
            $currency = $entity->getEcommerceConfig()->getCurrency();
            $address = $entity->getEcommerceConfig()->getAddress();
            $preOrder = $entity->getEcommerceConfig()->getIsPreorder();
            $cartProcess = $entity->getEcommerceConfig()->getCartProcess();
            $shippingCharge = $entity->getEcommerceConfig()->getShippingCharge();
            $cashOnDelivery = $entity->getEcommerceConfig()->isCashOnDelivery();
            $pickupLocation = $entity->getEcommerceConfig()->getPickupLocation();
            $path = $entity->getEcommerceConfig()->getWebPath();
            $mobile = empty($entity->getHotline()) ? $entity->getMobile() : $entity->getHotline();

            $data = array(
                'setupId' => $entity->getId(),
                'uniqueCode' => $entity->getUniqueCode(),
                'name' => $entity->getName(),
                'mobile' => $mobile,
                'email' => $entity->getEmail(),
                'locationId' => $entity->getLocation()->getId(),
                'address' => $this->stringNullChecker($address),
                'locationName' => $entity->getLocation()->getName(),
                'main_app' => $entity->getMainApp()->getId(),
                'main_app_name' => $entity->getMainApp()->getSlug(),
                'appsManual' => $entity->getMainApp()->getApplicationManual(),
                'website' => $entity->getDomain(),
                'vatRegNo' => $vatRegNo,
                'vatPercentage' => $vatPercentage,
                'productColumn' => $productColumn,
                'productFeatureColumn' => $productFeatureColumn,
                'currency' => $currency,
                'preOrder' => $preOrder,
                'cartProcess' => $cartProcess,
                'shippingCharge' => $shippingCharge,
                'cashOnDelivery' => $cashOnDelivery,
                'pickupLocation' => $pickupLocation,
                'vatEnable' => $vatEnable,
                'logo'      =>  $_SERVER['HTTP_HOST']."/{$path}"
            );
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;

    }

    public function menuAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $data = array();
            $entity = $this->checkApiValidation($request);
            $data['discount'] = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiDiscount($entity);
            $data['category'] = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllCategory($entity);
            $data['brand'] = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllBrand($entity);
            $data['promotion'] = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiPromotion($entity);
            $data['tag'] = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiTag($entity);
            $data['page'] = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->getPages($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function dashboardAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $em = $this->getDoctrine()->getManager();
            $user = $_REQUEST['user_id'];
            $entity = $this->checkApiValidation($request);
            $result = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->OrderDashboard($entity,$user);
            $month = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->monthlyOrderDashboard($entity,$user);
            $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy(array('user'=>$user));
            $creditLimit = $customer->getCreditLimit() ? $customer->getCreditLimit():0;
            $balance = $this->getDoctrine()->getRepository(AccountSales::class)->customerSingleOutstanding( $customer->getGlobalOption(),$customer->getId());
            $customer->setAvailableCredit($creditLimit - $balance);
            $em->persist($customer);
            $em->flush();
            $currentCredit = $customer->getAvailableCredit() ? $customer->getAvailableCredit():0;
            $limitPercent = 0;
            $expense = ($creditLimit - $balance);
            if($creditLimit > 0 and $balance > 0){
                $limitPercent = (($balance * 100)/$creditLimit);
            }
            $response = new Response();
            $data = array(
                'totalOrder' => ($result['totalOrder']) ? (int) $result['totalOrder'] : 0,
                'subTotal' => ($result['subTotal']) ? (int) $result['subTotal'] : 0,
                'total' => ($result['total']) ? (int) $result['total'] : 0,
                'discount' => ($result['discount']) ? (int) $result['discount'] : 0,
                'creditLimit' => (int) $creditLimit,
                'currentLimit' => (int) $currentCredit,
                'limitPercent' => (int) $limitPercent,
                'monthTotalOrder' => ($month['totalOrder']) ? (int) $month['totalOrder'] : 0,
                'monthSubTotal' => ($month['subTotal']) ? (int) $month['subTotal'] : 0,
                'monthTotal' => ($month['total']) ? (int) $month['total'] : 0,
                'monthDiscount' => ($month['discount']) ? (int) $month['discount'] : 0,
            );
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

     public function pageMenuAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */


            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->getPages($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function pageAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $id = $_REQUEST['id'];
            $option = $this->checkApiValidation($request);
            $entity = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findOneBy(array('globalOption'=>$option,'id'=>$id));

            /* @var $entity Page */

            $path = "http://".$_SERVER['HTTP_HOST'].'/'.$entity->getWebPath();
            $data = array(
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'menu' => $entity->getMenu(),
                'shortDescription' => $entity->getShortDescription(),
                'description' => $entity->getContent(),
                'image'      => empty($entity->getPath()) ? '' : $path
            );
            if($entity->getPageMetas()){
                foreach ($entity->getPageMetas() as $key => $sub ){
                    $data['specification'][$key]['metaId'] = (integer)$sub->getId();
                    $data['specification'][$key]['label'] = (string)$sub->getMetaKey();
                    $data['specification'][$key]['value'] = (string)$sub->getMetavalue();
                }

            }else{
                $data['specification'] = array();
            }

            if($entity->getPhotoGallery() and $entity->getPhotoGallery()->getGalleryImages()){
                foreach ($entity->getPhotoGallery()->getGalleryImages() as $key => $sub ){
                    $data['gallery'][$key]['imageId'] = (integer)$sub->getId();
                    if($sub->getPath()){
                        $path = $sub->getWebPath($option->getId(),$entity->getPhotoGallery()->getId());
                        $data['gallery'][$key]['imagePath'] =  "http://".$_SERVER['HTTP_HOST'].'/'.$path;
                    }else{
                        $data['gallery'][$key]['imagePath'] = "";
                    }
                }
            }else{
                $data['gallery'] = array();
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function homeFeatureSliderAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $data = "";

            $entity = $this->checkApiValidation($request);
            $feature = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->findOneBy(array('globalOption' => $entity, 'position'=>'mobile'));
            if($feature){
                $data = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->getFeatureSlider($feature);
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function introSliderAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $data = "";

            $entity = $this->checkApiValidation($request);
            $feature = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->findOneBy(array('globalOption' => $entity,'position'=>'mobile-intro'));
            if($feature){
                $data = $this->getDoctrine()->getRepository('SettingAppearanceBundle:FeatureWidget')->getAppIntroSlider($feature);
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function featureProductAction(Request $request)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $data = array();

            $feature = $_REQUEST['feature_id'];
            $module = $_REQUEST['module'];
            $entity = $this->checkApiValidation($request);
            if($entity and $feature and $module){
                $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getFeatureWidgetProduct($entity,$feature,$module);
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }



    public function productKeywordAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $keyword = $_REQUEST['search'];
            $entity = $this->checkApiValidation($request);
            $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->searchAndroidStock($keyword,$entity->getEcommerceConfig());
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($entities));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function productDetailsAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $itemId = $_REQUEST['id'];
            $entity = $this->checkApiValidation($request);
            $productDetails = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiProductDetails($entity,$itemId);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($productDetails));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function relatedProductAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $itemId = $_REQUEST['category'];
            $entity = $this->checkApiValidation($request);
            $search = array('category' => $itemId);
            $relatedProduct = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiRelatedProduct($entity,$search);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($relatedProduct));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function categoryAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiFeatureCategory($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function featureProductsAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiFeatureCategory($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function allFeatureProductsAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllFeatureProduct($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function allCategoryAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllCategory($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function brandAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiFeatureBrand($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function allBrandAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiAllBrand($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function promotionAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiPromotion($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function tagAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiTag($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function discountAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiDiscount($entity);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderCreateAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $userId = 1;
            $attachFile = '';
            $jsonUser = '{
                "userId":"1538","address":"Dhaka","mobile":"01828148148","location":"Mirpur"
            }';
            $jsonOrder = '
                { "id":"1686384468501","userId":"10","addressId":1,"locationId":3,"bankAccount":"","cashOnDelivery":"","couponCode":"","deliveryDate":"2021-06-21","discount":"0","discountCoupon":"","id":"1624269101","mobileBankAccount":"","paymentCard":"","paymentCardNo":"","paymentMobile":"01828-148148","remark":"","shippingCharge":"50","subTotal":"85","timePeriod":"2","total":"35","transactionId":"trans","transactionMethod":"Bkash","paymentMode":"Online-Payment"}
            ';



            /*$jsonOrderItem = '[
            {"id":"051900113","itemId":"051900113","orderId":"051900113","name":"ItemName","price":"120","quantity":"2","size":"XL","color":"Red","quantity":"2","subTotal":"240","url":"http://www.terminalbd.com/uploads/domain/4/ecommerce/product/Teer semolina suji.jpg"},
            {"id":"051900114","itemId":"051900114","orderId":"051900113","name":"ItemName","price":"120","quantity":"2","size":"XL","color":"Red","quantity":"2","subTotal":"240","url":"http://www.terminalbd.com/uploads/domain/4/ecommerce/product/Teer semolina suji.jpg"}
            ]';*/

            $jsonOrderItem1 = '[
            {"itemId":"27489","orderId":"1323","quantity":"1","unitPrice":"35","description":"35"},
            {"itemId":"27490","orderId":"1323","quantity":"1","unitPrice":"35"},
            {"itemId":"27491","orderId":"1323","quantity":"1","unitPrice":"35"}
            ]';


            $data = $request->request->all();
            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $order = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->insertAndroidOrder($entity,$data);
            if($order){
                $returnData = array('orderId'=>$order->getId(),'invoice'=>$order->getInvoice(),'status'=>'success');
            }else{
                $returnData = array('orderId'=>'','invoice'=>'','status'=>'failed');
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderItemUpdatePriceAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $ids = $_REQUEST['ids'];
            $entity = $this->checkApiValidation($request);
            $data = $this->getDoctrine()->getRepository('EcommerceBundle:Item')->getApiorderItemUpdatePrice($entity,$ids);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderUploadAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);

            $data = $request->request->all();
            $order = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->insertNewCustomerOrder($entity,$data);
            if($order){
                $returnData = array('orderId'=>$order->getId(),'invoice'=>$order->getInvoice(),'status'=>'success');
            }else{
                $returnData = array('orderId'=>'','invoice'=>'','status'=>'failed');
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderListAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{


            $data = $_REQUEST;

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $returnData = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->getApiOrders($entity,$data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }


    public function processOrderAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $_REQUEST;
            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $returnData = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->getApiProcessOrders($entity,$data);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderDetailsAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{


            $order = $_REQUEST['id'];

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $returnData = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->getApiOrderDetails($order);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderDeleteAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{


            $order = $_REQUEST['id'];
            $user = $_REQUEST['user_id'];

            /* @var $entity GlobalOption */
            $entity = $this->checkApiValidation($request);
            $remove = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->findOneBy(array('globalOption'=>$entity,'createdBy' => $user ,'id' => $order));
            $em = $this->getDoctrine()->getManager();
            if($remove){
                $em->remove($remove);
                $em->flush();
                $msg = "success";
            }else{
                $msg = 'failed';
            }
            $data = array(
                'status' => $msg,
            );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderTimePeriodAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $config = $entity->getEcommerceConfig();
            $returnData = $this->getDoctrine()->getRepository('EcommerceBundle:TimePeriod')->getApiTimePeriod($config);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function orderDeliveryLocationAction(Request $request)
    {


        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $config = $entity->getEcommerceConfig();
            $returnData = $this->getDoctrine()->getRepository('EcommerceBundle:DeliveryLocation')->getApiDeliveryLocation($config);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function testimonialAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $data = "";

            $option = $this->checkApiValidation($request);
            $testimonial = $this->getDoctrine()->getRepository('SettingContentBundle:Page')->findModuleContent($option,3);
            $data = array();
            /* @var $entity Page */
            foreach ($testimonial as $key => $entity){
                $data[$key]['id'] = $entity->getId();
                $data[$key]['name'] = $entity->getName();
                $data[$key]['designation'] = $entity->getDesignation();
                $data[$key]['content'] = $entity->getContent();
                $data[$key]['organization'] = $entity->getOrganization();
                if($entity->getWebPath()){
                    $path = $this->resizeFilter($entity->getWebPath());
                    $data[$key]['imagePath']            =  $path;
                }else{
                    $data[$key]['imagePath']            = "";
                }
            }

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }
    }

    public function userLoginAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiWithoutSecretValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();

            /* @var $entity GlobalOption */

            $option = $this->checkApiValidation($request);

            $intlMobile = $data['mobile'];
            $deviceHash = (isset($data['deviceHash']) and $data['deviceHash']) ? $data['deviceHash'] : '';
            $em = $this->getDoctrine()->getManager();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
            $user = $em->getRepository('UserBundle:User')->findOneBy(array('username'=> $mobile,'userGroup'=> 'customer','enabled'=>1));
            /* @var $user User */
            if(empty($user)){
                $data['msg'] = "invalid";
            }else{
                $a = mt_rand(1000,9999);
                $otp = $a." {$deviceHash}";
                $user->setPlainPassword($a);
                $this->get('fos_user.user_manager')->updateUser($user);
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.change_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent($user,$otp));
            }
            if($user){
                $returnData['userId'] = (int) $user->getId();
                $returnData['username'] = $user->getUsername();
                $returnData['name'] = $user->getProfile()->getName();
                $returnData['email'] = $user->getProfile()->getEmail();
                $returnData['mobile'] = $user->getProfile()->getMobile();
                $returnData['password'] = $a;
                $customer = $this->getDoctrine()->getRepository(Customer::class)->findExistingEcommerceCustomer($option,$user);
                if($customer){
                    $add = $this->getDoctrine()->getRepository(Customer::class)->find($customer);
                    $addreses = $add->getCustomerAddresses();
                    $returnData['address'] = array();
                    if($addreses) {
                        /* @var $address CustomerAddress */
                        foreach ($addreses as $key => $address) {
                            $returnData['address'][$key]['id'] = (integer)$address->getId();
                            $returnData['address'][$key]['userId'] = (int) $user->getId();
                            $returnData['address'][$key]['name'] = (string)$address->getName();
                            $returnData['address'][$key]['mobile'] = (string)$address->getMobile();
                            $returnData['address'][$key]['address'] = (string)$address->getAddress();
                            $returnData['address'][$key]['mode'] = (string)$address->getMode();
                        }
                    }else{
                        $returnData['address'] = array();
                    }
                }
                $returnData['msg'] = "valid";
            }else{
                $returnData['msg'] = "invalid";
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;

        }

    }

    public function customerAddressAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiWithoutSecretValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();

            /* @var $entity GlobalOption */


            $option = $this->checkApiValidation($request);

            $intlMobile =$data['mobile'];
            $userid =$data['user_id'];
            $em = $this->getDoctrine()->getManager();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($intlMobile);
            $user = $em->getRepository('UserBundle:User')->findOneBy(array('id'=> $userid,'userGroup'=> 'customer','enabled'=>1));
            /* @var $user User */
            $returnData = array();
            if(empty($user)){
                $data['msg'] = "invalid";
            }else{
                $customer = $this->getDoctrine()->getRepository("DomainUserBundle:Customer")->findOneBy(array('globalOption'=> $option,'user'=> $user->getId()));
                if(empty($customer)){
                    $customer = new Customer();
                    $customer->setUser($user->getId());
                    $customer->setGlobalOption($option);
                    $customer->setName($data['name']);
                    $customer->setMobile($data['mobile']);
                    $customer->setAddress($data['address']);
                    $em->persist($customer);
                    $em->flush();

                    $customerAddress = new CustomerAddress();
                    $customerAddress->setCustomer($customer);
                    $customerAddress->setName($data['name']);
                    $customerAddress->setMode('Home');
                    $customerAddress->setAddress($data['address']);
                    $customerAddress->setMobile($data['mobile']);
                    $em->persist($customerAddress);
                    $em->flush();

                    $returnData['userId'] = (int) $user->getId();
                    $returnData['username'] = $user->getUsername();
                    $returnData['name'] = $user->getProfile()->getName();
                    $returnData['email'] = $user->getProfile()->getEmail();
                    $returnData['mobile'] = $user->getProfile()->getMobile();
                    $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy(array('globalOption'=> $option,'user' => $user->getId()));
                    $addreses = $customer->getCustomerAddresses();
                    $returnData['address'] = array();
                    if($addreses) {
                        /* @var $address CustomerAddress */
                        foreach ($addreses as $key => $address) {
                            $returnData['address'][$key]['id'] = (integer)$address->getId();
                            $returnData['address'][$key]['userId'] = (int) $user->getId();
                            $returnData['address'][$key]['name'] = (string)$address->getName();
                            $returnData['address'][$key]['mobile'] = (string)$address->getMobile();
                            $returnData['address'][$key]['address'] = (string)$address->getAddress();
                            $returnData['address'][$key]['mode'] = (string)$address->getMode();
                        }
                    }else{
                        $returnData['address'] = array();
                    }

                }else{

                    $customerAddress = new CustomerAddress();
                    $customerAddress->setCustomer($customer);
                    $customerAddress->setName($data['name']);
                    $customerAddress->setMode($data['mode']);
                    $customerAddress->setAddress($data['address']);
                    $customerAddress->setMobile($data['mobile']);
                    $em->persist($customerAddress);
                    $em->flush();

                    $returnData['userId'] = (int) $user->getId();
                    $returnData['username'] = $user->getUsername();
                    $returnData['name'] = $user->getProfile()->getName();
                    $returnData['email'] = $user->getProfile()->getEmail();
                    $returnData['mobile'] = $user->getProfile()->getMobile();
                    $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy(array('globalOption'=> $option,'user' => $user->getId()));
                    $addreses = $customer->getCustomerAddresses();
                    $returnData['address'] = array();
                    if($addreses) {
                        /* @var $address CustomerAddress */
                        foreach ($addreses as $key => $address) {
                            $returnData['address'][$key]['id'] = (integer)$address->getId();
                            $returnData['address'][$key]['userId'] = (int) $user->getId();
                            $returnData['address'][$key]['name'] = (string)$address->getName();
                            $returnData['address'][$key]['mobile'] = (string)$address->getMobile();
                            $returnData['address'][$key]['address'] = (string)$address->getAddress();
                            $returnData['address'][$key]['mode'] = (string)$address->getMode();
                        }
                    }else{
                        $returnData['address'] = array();
                    }
                }
                $returnData['msg'] = "valid";
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;

        }

    }

    public function userRegisterAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();
            /* @var $entity GlobalOption */

            $setup = $this->checkApiValidation($request);
            $name = $data['name'];
            $mobile = $data['mobile'];
            $email = isset($data['email']) ? $data['email'] : '';
            if(empty($email)){
               $emailAdd = "{$mobile}@gmail.com";
            }else{
               $emailAdd = $email;
            }
            $address = $data['address'];
            $em = $this->getDoctrine()->getManager();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($mobile);
            $user = $em->getRepository('UserBundle:User')->findOneBy(array('username'=> $mobile));
            $existEmail = $em->getRepository('UserBundle:User')->findOneBy(array('email'=> $emailAdd));

            /* @var $user User */
            $returnData = array();

            if(empty($user) and !empty($emailAdd) and empty($existEmail)){

                $user = new User();
                $a = mt_rand(1000,9999);
                $user->setPlainPassword($a);
                $user->setEnabled(true);
                $user->setUsername($mobile);
                $user->setEmail($emailAdd);
                $user->setRoles(array('ROLE_CUSTOMER'));
                $user->setUserGroup('customer');
                $user->setGlobalOption($setup);
                $user->setEnabled(1);
                $em->persist($user);
                $em->flush();

                $profile = new Profile();
                $profile->setUser($user);
                $profile->setName($name);
                $profile->setMobile($mobile);
                $profile->setAdditionalPhone($mobile);
                $profile->setAddress($address);
                $em->persist($profile);
                $em->flush();

                $customerId = isset($data['employeeId']) ? $data['employeeId'] : '';
                $gender = isset($data['gender']) ? $data['gender'] : '';
                $age = isset($data['age']) ? $data['age'] : '';

                $customer = new Customer();
                $customer->setUser($user->getId());
                $customer->setGlobalOption($user->getGlobalOption());
                $designationId = isset($data['designation']) ? $data['designation'] : '';
                if($designationId){
                    $designation = $this->getDoctrine()->getRepository(Designation::class)->find($designationId);
                    $customer->setDesignation($designation);
                }
                $customer->setName($profile->getName());
                $customer->setMobile($user->getUsername());
                $customer->setAddress($profile->getAddress());
                $customer->setGender($gender);
                $customer->setAge($age);
                if($customerId){$customer->setCustomerId($customerId); }
                if($gender){ $customer->setGender($gender); }
                $em->persist($customer);
                $em->flush();

                $customerAddress = new CustomerAddress();
                $customerAddress->setName($customer->getName());
                $customerAddress->setCustomer($customer);
                $customerAddress->setMode('Home');
                $customerAddress->setAddress($customer->getAddress());
                $customerAddress->setMobile($customer->getMobile());
                $em->persist($customerAddress);
                $em->flush();

                $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy(array('globalOption'=> $setup,'user' => $user->getId()));
                $addreses = $customer->getCustomerAddresses();
                if($addreses) {
                    /* @var $address CustomerAddress */
                    foreach ($addreses as $key => $address) {
                        $returnData['address'][$key]['id'] = (integer)$address->getId();
                        $returnData['address'][$key]['userId'] = (int) $user->getId();
                        $returnData['address'][$key]['name'] = (string)$address->getName();
                        $returnData['address'][$key]['mobile'] = (string)$address->getMobile();
                        $returnData['address'][$key]['address'] = (string)$address->getAddress();
                        $returnData['address'][$key]['mode'] = (string)$address->getMode();
                    }
                }else{
                    $returnData['address'] = array();
                }
                $returnData['msg'] = "valid";

                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.customer_signup_msg', new \Setting\Bundle\ToolBundle\Event\CustomerSignup($user,$setup));

            }elseif($user){

                $a = mt_rand(1000,9999);
                $user->setPlainPassword($a);
                $this->get('fos_user.user_manager')->updateUser($user);
                $returnData['userId'] = (int) $user->getId();
                $returnData['username'] = $user->getUsername();
                $returnData['password'] = $a;
                $returnData['name'] = $user->getProfile()->getName();
                $returnData['mobile'] = $user->getProfile()->getMobile();
                $returnData['location'] = empty($user->getProfile()->getDeliveryLocation()) ? '' : $user->getProfile()->getDeliveryLocation()->getName();
                $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy(array('globalOption'=> $setup,'user' => $user->getId()));

                $addreses = $customer->getCustomerAddresses();
                if($addreses) {
                    /* @var $address CustomerAddress */
                    foreach ($addreses as $key => $address) {
                        $returnData['address'][$key]['id'] = (integer)$address->getId();
                        $returnData['address'][$key]['userId'] = (int) $user->getId();
                        $returnData['address'][$key]['name'] = (string)$address->getName();
                        $returnData['address'][$key]['mobile'] = (string)$address->getMobile();
                        $returnData['address'][$key]['address'] = (string)$address->getAddress();
                        $returnData['address'][$key]['mode'] = (string)$address->getMode();
                    }
                }else{
                    $returnData['address'] = array();
                }
                $returnData['msg'] = "valid";
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.change_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent($user,$a));
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function userUpdateProfileAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            $data = $request->request->all();

            /* @var $entity GlobalOption */

            $setup = $this->checkApiValidation($request);

            $user = $data['user_id'];
            $name = $data['name'];
            $mobile = isset($data['mobile']) ? $data['mobile'] :'';
            $email = isset($data['email']) ? $data['email'] :'';
            $address = $data['address'];
            $location = isset($data['location']) ? $data['location'] : '';
            $em = $this->getDoctrine()->getManager();
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($mobile);
            $user = $em->getRepository('UserBundle:User')->find($user);



            /* @var $user User */

            if(($user) and $user->getProfile()){
                $profile = $user->getProfile();
                $profile->setName($name);
                $profile->setAdditionalPhone($mobile);
                $profile->setAddress($address);
                $profile->setEmail($email);
                if($location){
                    $loc = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($location);
                    $profile->setDeliveryLocation($loc);
                }
                $em->persist($profile);
                $em->flush();
            }else{
                $profile = new Profile();
                $profile->setUser($user);
                $profile->setName($name);
                $profile->setMobile($user->getUsername());
                $profile->setAdditionalPhone($mobile);
                $profile->setAddress($address);
                $profile->setEmail($email);
                if($location){
                    $loc = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($location);
                    $profile->setDeliveryLocation($loc);
                }
                $em->persist($profile);
                $em->flush();
            }

            $returnData['userId'] = (int) $user->getId();
            $returnData['username'] = $user->getUsername();
            $returnData['name'] = $user->getProfile()->getName();
            $returnData['email'] = $user->getProfile()->getEmail();
            $returnData['mobile'] = $user->getProfile()->getMobile();
            $returnData['location'] = empty($user->getProfile()->getDeliveryLocation()) ? '' : $user->getProfile()->getDeliveryLocation()->getName();
            $customer = $this->getDoctrine()->getRepository(Customer::class)->findExistingEcommerceCustomer($setup,$user);
            if($customer){
                $addreses = $customer->getCustomerAddresses();
                $returnData['address'] = array();
                /* @var $address CustomerAddress */
                foreach ($addreses as $key => $address) {
                    $returnData['address'][$key]['id'] = (integer)$address->getId();
                    $returnData['address'][$key]['userId'] = (int) $user->getId();
                    $returnData['address'][$key]['name'] = (string)$address->getName();
                    $returnData['address'][$key]['mobile'] = (string)$address->getMobile();
                    $returnData['address'][$key]['address'] = (string)$address->getAddress();
                    $returnData['address'][$key]['mode'] = (string)$address->getMode();
                }
            }else{
                $returnData['address'] = array();
            }
            $returnData['msg'] = "valid";
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($returnData));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }

    public function mobileAccountAction(Request $request)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        if( $this->checkApiValidation($request) == 'invalid') {

            return new Response('Unauthorized access.', 401);

        }else{

            /* @var $entity GlobalOption */

            $entity = $this->checkApiValidation($request);
            $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->findBy(array('globalOption'=>$entity));
            $data = array();

            /* @var $row AccountMobileBank */

            if($result) {
                foreach ($result as $key => $row) {
                    $data[$key]['global_id'] = (int)$entity->getId();
                    $data[$key]['id'] = (int)$row->getId();
                    $data[$key]['name'] = $row->getName();
                }
            }
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }


}
