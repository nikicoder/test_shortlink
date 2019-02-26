<?php

namespace App\Entity;

use App\Entity\Entity;
 
class LinkRawEntity extends Entity
{
    const AVAILABLE_PROPS = [
        'userId'            => 'int',
        'destinationLink'   => 'string',
        'expireDate'        => 'date',
        'uriSegment'        => 'string',
        'isCommerce'        => 'bool'
    ];

    private $entityData;

    public function __construct()
    {
        // Установка значений по-умолчанию
        // по-умолчанию все ссылки вечные
        $this->entityData['expireDate'] = null;

        // Поскольку работа с аутентификацией, авторизацией и аккаунтингом
        // не входит в задачи данного тестового задания то ID пользователя
        // захардкоден до значния 1
        $this->entityData['userId'] = 1;
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
        parent::__set($name, $value);

        if($name === 'expireDate') {
            $this->setExpireDate($value);
        } else {
            $this->entityData[$name] = $this->setTypeOfPropValue($name, $value);
        }
        
    }

    public function setExpireDate($value)
    {
        // ну так, в упрощенном варианте предположим что
        // если количество задано числом то +дней от текущей даты
        if(is_int($value)) {
            $d = new \DateTime;
            $d->add(new \DateInterval('P' . $value . 'D'));
            $this->expireDate = $d;
        } elseif($value instanceof \DateTime) {
            $this->expireDate = $value;
        }
    }
}