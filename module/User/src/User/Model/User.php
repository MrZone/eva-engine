<?php

namespace User\Model;

use Eva\Api,
    Eva\Mvc\Model\AbstractModelService;

class User extends AbstractModelService
{
    protected $map = array(
        'small' => array(
        
        ),
        'medium' => array(
        
        ),
        'large' => array(
        
        )
    );

    public function createUser(array $data = array())
    {
        if($data) {
            $this->setItem($data);
        }

        $item = $this->getItem();
        
        $this->trigger('create.pre');

        $itemId = $item->create();

        if($item->hasRelationships()){
            foreach($item->getRelationships() as $key => $relItem){
                $relItem->create();
            }
        }
        $this->trigger('create');

    
        $this->trigger('create.post');

        return $itemId;
    }

    public function saveUser(array $data = array())
    {
        if($data) {
            $this->setItem($data);
        }

        $item = $this->getItem();
        
        $this->trigger('save.pre');

        $item->save();

        if($item->hasRelationships()){
            foreach($item->getRelationships() as $key => $relItem){
                $relItem->save();
            }
        }
        $this->trigger('save');

    
        $this->trigger('save.post');


        return $item->id;

    }

    public function removeUser(array $map = array())
    {
        $this->trigger('remove.pre');

        $item = $this->getItem();
        $item->remove();

        /*
        if($item->hasRelationships()){
            foreach($item->getRelationships() as $key => $relItem){
                $relItem->save();
            }
        }
        */
        $this->trigger('remove');
    
        $this->trigger('remove.post');

        return true;
    }

    public function getUser($userIdOrName = null, array $map = array())
    {
        $this->trigger('get.precache');

        if(is_numeric($userIdOrName)){
            $this->setItem(array(
                'id' => $userIdOrName,
            ));
        } elseif(is_string($userIdOrName)) {
            $this->setItem(array(
                'userName' => $userIdOrName,
            ));
        }
        $this->trigger('get.pre');

        $item = $this->getItem();
        if($map){
            $item = $item->toArray($map);
        } else {
            $item = $item->self(array('*'));
        }

        $this->trigger('get');

        $this->trigger('get.post');
        $this->trigger('get.postcache');

        return $item;
    }

    public function getUserList($map = null)
    {
        $this->trigger('list.precache');


        $this->trigger('list.pre');

        $item = $this->getItemList();
        if($map){
            $item = $item->toArray($map);
        }

        $this->trigger('get');

        $this->trigger('list.post');
        $this->trigger('list.postcache');

        return $item;
    }
}
