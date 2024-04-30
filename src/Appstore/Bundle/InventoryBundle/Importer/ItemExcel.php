<?php

namespace Appstore\Bundle\InventoryBundle\Importer;


use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Appstore\Bundle\EcommerceBundle\Entity\ItemBrand;
use Appstore\Bundle\EcommerceBundle\Entity\ItemImport;
use Appstore\Bundle\EcommerceBundle\Entity\Promotion;
use Appstore\Bundle\EcommerceBundle\Repository\PromotionRepository;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\ProductSize;
use Setting\Bundle\ToolBundle\Repository\ProductSizeRepository;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ItemExcel
{
    use ContainerAwareTrait;

    /* @var $itemImport ItemImport */

    protected $itemImport;

    private $data = array();
    private $cache = array();

    public function import($data)
    {
        $this->data = $data;
        $vendor = $this->itemImport->getVendor();
        $config = $this->itemImport->getEcommerceConfig();

        foreach($this->data as $key => $item) {

            $name = ucfirst(strtolower($item['ProductName']));
            $productID = $item['ProductId'];

            $productOld = $this->getDoctrain()->getRepository('EcommerceBundle:Item')->findOneBy(array('ecommerceConfig' => $config,'webName' => $name));
            $productId = $this->getDoctrain()->getRepository('EcommerceBundle:Item')->findOneBy(array('ecommerceConfig' => $config,'id' => $productID));
            $unit = empty($item['ProductUnit']) ? 'Pcs' : $item['ProductUnit'];
            $sizeUnit = empty($item['SizeUnit']) ? 'Pcs' : $item['SizeUnit'];
            if((empty($productOld) and !empty($item['ProductName']) and empty($productId))) {
                $product = new Item();
                $product->setEcommerceConfig($config);
                $product->setName($name);
                $product->setWebName($name);
                $product->setProductBengalName($item['ProductNameBn']);
                $product->setQuantity($item['Quantity']);
                $product->setMasterQuantity($item['Quantity']);
                $product->setPurchasePrice($item['PurchasePrice']);
                $product->setSalesPrice($item['SalesPrice']);
                $min = empty($item['MinQuantity']) ? 1 : $item['MinQuantity'];
                $product->setMinQuantity($min);
                $max = empty($item['MaxQuantity']) ? 100 : $item['MaxQuantity'];
                $product->setMaxQuantity($max);
                $path = empty($item['ImageLink']) ? '' : $item['ImageLink'];
                $product->setPath($path);
                if ($vendor) {
                    $product->setVendor($vendor);
                }

                $category = isset($item['Category'])? $item['Category']:"";
                if ($category) {
                    $category = $this->getCategory(ucfirst(strtolower($category)));
                    $product->setCategory($category);
                }

                $brand = isset($item['Brand'])? $item['Brand']:"";
                if ($brand) {
                    $brand = $this->getBrand(ucfirst(strtolower($brand)));
                    $product->setBrand($brand);
                }

                $size = isset($item['Size'])? $item['Size']:"";
                if ($size) {
                    $size = $this->getSize(ucfirst(strtolower($size)));
                    $product->setSize($size);
                }

                if ($unit) {
                    $unit = $this->getDoctrain()->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name' => $unit));
                    $product->setProductUnit($unit);
                }

                if ($sizeUnit) {
                    $sizeUnit = $this->getDoctrain()->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name' => $sizeUnit));
                    $product->setSizeUnit($sizeUnit);
                }
                $colors = isset($item['Colors'])? $item['Colors']:"";
                if ($colors) {
                    $colorIds = explode(',', $colors);
                    foreach ($colorIds as $color) {
                        $colorObj[] = $this->getDoctrain()->getRepository('SettingToolBundle:ProductColor')->findOneBy(array('name' => $color));
                    }
                    $product->setItemColors($colorObj);
                }
               // $tags = $item['Tags'];
                $tags = isset($item['Tags'])? $item['Tags']:"";
                if ($tags) {
                    $tagIds = explode(',', $tags);
                    foreach ($tagIds as $tag) {
                        $tagObj[] = $this->getTags(ucfirst(strtolower($tag)));
                    }
                    $product->setTag($tagObj);
                }

                $this->save($product);

            }elseif(!empty($productId)){

                $product = $productId;
                $product->setSalesPrice($item['SalesPrice']);
                $product->setPurchasePrice($item['PurchasePrice']);
                $min = empty($item['MinQuantity']) ? 1 : $item['MinQuantity'];
                $product->setMinQuantity($min);
                $max = empty($item['MaxQuantity']) ? 100 : $item['MaxQuantity'];
                $product->setMaxQuantity($max);
                $path = empty($name) ? '' : $name.".jpg";
                $product->setPath($path);
                $this->save($product);

            }
        }

    }

    private function getCategory($item)
    {
        $config = $this->itemImport->getEcommerceConfig();
        $global = $this->itemImport->getEcommerceConfig()->getGlobalOption();
        $categoryRepository = $this->getCategoryRepository();

        $category = $categoryRepository->findOneBy(array(
            'globalOption'   => $global,
            'name'              => $item,
        ));
        if($category){
            return $category;
        }else{
            $category = new Category();
            $category->setName($item);
            $category->setGlobalOption($global);
            $category->setEcommerceConfig($config);
            $category = $this->save($category);
            return $category;
        }


    }

    private function getBrand($item)
    {

        $config = $this->itemImport->getEcommerceConfig();
        $brandRepository = $this->getBrandRepository();

        $brand = $brandRepository->findOneBy(array(
            'ecommerceConfig'   => $config,
            'name'              => $item
        ));
        if($brand){
            return $brand;
        }else{
            $brand = new ItemBrand();
            $brand->setName($item);
            $brand->setEcommerceConfig($config);
            $brand = $this->save($brand);
            return $brand;
        }

    }

    private function getSize($item)
    {

        $sizeRepository = $this->getSizeRepository();

        $size = $sizeRepository->findOneBy(array(
            'name'              => $item
        ));

        if($size){
            return $size;
        }else{
            $size = new ProductSize();
            $size->setName($item);
            $size = $this->save($size);
            return $size;
        }

    }

    private function getTags($item)
    {

        $config = $this->itemImport->getEcommerceConfig();
        $tagRepository = $this->getPromotionRepository();

        $tag = $tagRepository->findOneBy(array(
            'ecommerceConfig'   => $config,
            'name'              => $item
        ));
        if($tag){
            return $tag;
        }else{
            $tag = new Promotion();
            $tag->setName($item);
            $tag->setType(array("Tag"));
            $tag->setEcommerceConfig($config);
            $brand = $this->save($tag);
            return $brand;
        }

    }

    public function setItemImport($itemImport)
    {
        $this->itemImport = $itemImport;
    }

    private function save($entity){
        $this->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    private function getEntityManager()
    {
        return $this->getDoctrain()->getManager();
    }


    private function persist($entity){
        $this->getEntityManager()->persist($entity);
    }

    private function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private function getDoctrain()
    {
        return $this->container->get('doctrine');
    }

    private function getCachedData($type, $key)
    {
        if(isset($this->cache[$type][$key])){
            return $this->cache[$type][$key];
        }

        return NULL;
    }

    private function setCachedData($type, $key, $value)
    {
        $this->cache[$type][$key] = $value;
    }

    /**
     * @return  @return \Appstore\Bundle\EcommerceBundle\Repository\ItemBrandRepository
     */
    private function getBrandRepository()
    {
        return $this->getDoctrain()->getRepository('EcommerceBundle:ItemBrand');
    }

    /**
     * @return PromotionRepository
     */
    private function getPromotionRepository()
    {
        return $this->getDoctrain()->getRepository(Promotion::class);
    }

    /**
     * @return ProductSizeRepository
     */
    private function getSizeRepository()
    {
        return $this->getDoctrain()->getRepository(ProductSize::class);
    }

    /**
     * @return  @return \Product\Bundle\ProductProductBundle\Entity\CategoryRepository
     */
    private function getCategoryRepository()
    {
        return $this->getDoctrain()->getRepository('ProductProductBundle:Category');
    }

    /**
     * @param $key
     * @param $item
     * @param $defaultStr
     *
     * @return mixed
     */
    private function senatizeItemData($key, $item, $defaultStr)
    {

        if (empty($item['Brand'])) {
            $item['Brand'] = $defaultStr;
        }

        if (empty($item['Category'])) {
            $item['Category'] = $defaultStr;
        }

        if (empty($item['Quantity'])) {
            $item['Quantity'] = 1;
        }

        if (empty($item['Unit'])) {
            $item['Unit'] = $defaultStr;
        }
        $this->data[$key] = $item;

        return $item;
    }

    function sentence_case($string) {
        $sentences = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
        $new_string = '';
        foreach ($sentences as $key => $sentence) {
            $new_string .= ($key & 1) == 0?
                ucfirst(strtolower(trim($sentence))) :
                $sentence.' ';
        }
        return trim($new_string);
    }


}