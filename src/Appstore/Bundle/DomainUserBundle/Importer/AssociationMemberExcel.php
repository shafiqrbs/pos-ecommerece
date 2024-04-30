<?php

namespace Appstore\Bundle\DomainUserBundle\Importer;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\EcommerceBundle\Entity\ItemBrand;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use DoctrineExtensions\Query\Sqlite\Date;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\ProductSize;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\Constraints\DateTime;


class AssociationMemberExcel
{
    use ContainerAwareTrait;

    protected $option;

    private $data = array();

    public function import($data)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $this->data = $data;
        $option = $this->option;
        foreach($this->data as $key => $item) {
            $user = strtolower("{$item['EmployeeCode']}");
            $existUser = $this->getDoctrain()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=>$option,'employeeId' => "{$user}"));
            $em3 = $this->getDoctrain()->getManager();
            if(empty($existUser)){
                $profile = new Customer();
                $profile->setGlobalOption($option);
                $profile->setCustomerId($item['EmployeeCode']);
                $profile->setEmployeeId($item['EmployeeCode']);
                $profile->setCustomerType('customer');
                $profile->setName($item['EmployeeName']);
                $profile->setMobile($item['Mobile']);
                $profile->setSection($item['Section']);
                $profile->setLine($item['Line']);
                $profile->setDepartment($item['Department']);
                $profile->setUnit($item['Unit']);
                $profile->setCategory($item['Category']);
                $profile->setCreditLimit($item['CreditLimit']);
                $profile->setProcess("pending");
                $profile->setGender(isset($item['Gender']) ? $item['Gender'] : '');
                $profile->setGrade($item['Grade']);
                $profile->setProfession($item['Designation']);
                $profile->setIsNew(false);
                $profile->setStatus(false);
                $em3->persist($profile);
            }else{
                $existUser->setCreditLimit($item['CreditLimit']);
            }
            $em3->flush();
        }
    }

    public function setGlobalOption($option)
    {
        $this->option = $option;
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