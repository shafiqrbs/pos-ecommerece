<?php

namespace Setting\Bundle\ContentBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ContactMessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContactMessageRepository extends EntityRepository
{
    public function insertMessage($data)
    {

        $em = $this->_em;
        $user = $em->getRepository('UserBundle:User')->find($data['user']);

        if($data){
            $entity = new ContactMessage();
            $entity->setUser($user);
            $entity->setName($data['name']);
            $entity->setEmail($data['email']);
            $entity->setMobile($data['mobile']);
            $entity->setContent($data['content']);
            $em->persist($entity);
            $em->flush();
        }

    }

    public function reply($data)
    {
        $em = $this->_em;
        $ids = $data['id'];
        $i=0;
        foreach ($ids as $id ){

            if(isset($data['remove']) AND !empty($data['remove'][$i]) ){
               $this->remove($data['remove'][$i]);
            }

            $entity = $em->getRepository('SettingContentBundle:ContactMessage')->find($id);
            if(!empty($entity) AND !empty($data['reply'][$i])){

                $entity->setReply($data['reply'][$i]);
                $entity->setReplyDate(new \DateTime());
                $entity->setArchive(1);
                $em->persist($entity);
            }

            $i++;
        }

        $em->flush();
    }

    private function remove($id){

        $em = $this->_em;
        $entity = $em->getRepository('SettingContentBundle:ContactMessage')->find($id);
        $em->remove($entity);
        $em->flush();
    }
}