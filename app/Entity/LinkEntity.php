<?php

namespace App\Entity;

use App\Entity\Entity;
 
class LinkEntity extends Entity
{
    const AVAILABLE_PROPS = [
        'linkId'            => 'int',
        'userId'            => 'int',
        'sourceLink'        => 'string',
        'statsLink'         => 'string',
        'destinationLink'   => 'string',
        'secret'            => 'string',
        'expireDate'        => 'date',
        'isCommerce'        => 'bool',
        'state'             => 'bool',
    ];

    private $entityData;

    public function __construct($data)
    {
        $this->entityData['linkId'] = $this->setTypeOfPropValue('linkId', $data['id']);
        $this->entityData['userId'] = $this->setTypeOfPropValue('userId', $data['user_id']);
        $this->entityData['destinationLink'] = $this->setTypeOfPropValue('destinationLink', $data['destination']);
        $this->entityData['isCommerce'] = $this->setTypeOfPropValue('isCommerce', $data['is_commerce']);
        $this->entityData['state'] = $this->setTypeOfPropValue('state', $data['state']);
        $this->entityData['secret'] = $this->setTypeOfPropValue('secret', $data['secret']);

        if(!empty($data['expire'])) {
            $this->entityData['expireDate'] = $this->setTypeOfPropValue('expireDate', $data['expire']);
        } else {
            $this->entityData['expireDate'] = null;
        }

        $sl = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $data['uri'];
        $this->entityData['sourceLink'] = $this->setTypeOfPropValue('sourceLink', $sl);

        $stl = 'http://' . $_SERVER['SERVER_NAME'] . '/stat/' . $data['uri'] . '/' . $data['secret'];
        $this->entityData['statsLink'] = $this->setTypeOfPropValue('statsLink', $stl);
    }

    public function __get($name) 
    {
        parent::__get($name);

        return array_key_exists($name, $this->entityData) ?
            $this->entityData[$name] : 
            // возвращаем пустое значение обозначенного типа
            $this->setTypeOfPropValue($name, null); 
    }
    
    public function __set($name, $value) 
    {
        throw new \Exception('Setting properties directly prohibited.');   
    }
}