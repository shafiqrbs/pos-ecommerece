<?php

namespace Appstore\Bundle\EcommerceBundle\Importer;


use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Appstore\Bundle\EcommerceBundle\Entity\ItemBrand;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ItemExcel
{
    use ContainerAwareTrait;

    protected $config;

    private $data = array();

    public function import($data)
    {
        $this->data = $data;
        $config = $this->config;
        foreach($this->data as $key => $item) {

            $name = $item['Name'];
            $productOld = $this->getDoctrain()->getRepository('EcommerceBundle:Item')->findOneBy(array('ecommerceConfig'=> $config,'webName' => $name));
            if(empty($productOld) and !empty($item['Name'])){
                $product = new Item();
                $product->setEcommerceConfig($config);
                $product->setName( $item['Name']);
                $product->setQuantity( $item['Quantity']);
                $product->setMasterQuantity($item['Quantity']);
                $product->setPurchasePrice( $item['PurchasePrice']);
                $product->setSalesPrice( $item['SalesPrice']);
                /*
                 $category = $item['Category'];
                 if($category){
                     $unit = $this->getDoctrain()->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name' => $unit));
                     $product->setProductUnit($unit);
                 }
                 $brand = $item['Brand'];
                 if($brand){
                     $unit = $this->getDoctrain()->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name' => $unit));
                     $product->setProductUnit($unit);
                 }*/

                $unit = $item['Unit'];
                if($unit){
                    $unit = $this->getDoctrain()->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name' => $unit));
                    $product->setProductUnit($unit);
                }
                $this->persist($product);
                $this->flush($product);
            }

        }

    }

    private function getBrand($item)
    {
        $brand = $this->getCachedData('Brand', $item['Brand']);

        $brandRepository = $this->getBrandRepository();

        if($brand == NULL) {

            $brand = $brandRepository->findOneBy(array(
                'ecommerceConfig'   => $this->config,
                'name'              => $item['Brand']
            ));

            if($brand == null) {
                $brand = new ItemBrand();
                $brand->setName($item['Brand']);
                $brand->setEcommerceConfig($this->config);
                $brand = $this->save($brand);
            }

            $this->setCachedData('Brand', $item['Brand'], $brand);
        }

        return $brand;
    }

    public function setConfig($config)
    {
        $this->config = $config;
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


}