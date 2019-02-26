<?php

namespace App\Models;

use App\Entity\LinkRawEntity;
use App\Entity\LinkEntity;
use App\Repositories\LinksRepository;
use App\Exceptions\ApiException;

class LinkModel
{
    const URI_RANDOM_LENGHT = 10;
    const SECRET_RANDOM_LENGHT = 4;

    // служебные страницы и другие URI которые нельзя использовать
    const RESERVED_URI = [
        'stats',
        'admin'
    ];

    // вообще сообщения должен отдавать специальный хелпер
    // поддержка языков там вот это вот все
    // но в текущем ТЗ ограничимся массивом, главное что не в коде
    const ERROR_MESSAGES = [
        'empty_url'         => 'Целевая ссылка не должна быть пустой',
        'link_exsists'      => 'Ссылка с таким URI уже существует',
        'service_name'      => 'Нельзя использовать данный URI',
        'unallowed_symbols' => 'В URI используются неразрешенные символы'
    ];

    /**
     * getLinkByURI получение данных ссылки по URI
     *
     * @param  mixed $uri
     *
     * @return mixed
     */
    public function getLinkByURI(string $uri)
    {
        $lR = new LinksRepository;

        $linkData = $lR->getLinkDataByURI($uri);

        if(empty($linkData)) {
            return false;
        }

        return new LinkEntity($linkData);
    }

    
    /**
     * getLinkByID получение данных ссылки по ID
     *
     * @param  int $id
     *
     * @return mixed
     */
    public function getLinkByID(int $id)
    {
        $lR = new LinksRepository;

        $linkData = $lR->getRDataByID($id);

        if(empty($linkData)) {
            return false;
        }

        return new LinkEntity($linkData);
    }

    /**
     * addLink добавление ссылки
     *
     * @param  LinkRawEntity $entity
     *
     * @throws
     * @return int
     */
    public function addLink(LinkRawEntity $entity): int
    {
        $lR = new LinksRepository;

        // как минимум это полезно из-за наличия nullable-полей
        $insertData = $lR->createEmptyStructure();

        // целевая ссылка
        // вопросы валидации не оговаривались в ТЗ
        // ограничимся в контексте ТЗ только проверкой чтобы была не пустая
        if(strlen($entity->destinationLink) <= 0) {
            throw new ApiException(self::ERROR_MESSAGES['empty_url']);
        }

        $insertData['destination'] = $entity->destinationLink;

        // Если ссылка не задана самостоятельно -- генерация
        if(strlen($entity->uriSegment) <= 0) {
            // через какое-то время возможны коллизии
            // уникальные поля в БД и исключение конечно хорошо
            // но задача предполагает коммерческие URL 
            // по этому такая проверка на уровне БД создат определенные неудобства
            // по этому проверка на уровне бизнес-логики
            $flagStopGeneration = false;
            $newLink = '';

            while(!$flagStopGeneration) {
                $newLink = $this->generateRandom(self::URI_RANDOM_LENGHT);
                // достаточное условие чтобы у ссылки с таким URI
                // если она существует state был false (или 0)
                $checkResult = $lR->getLinkDataByURI($newLink);
                if(empty($checkResult) || (bool)$checkResult['state'] == false) {
                    $flagStopGeneration = true;
                }
            }

            $insertData['uri'] = $newLink;
        } else {
            // URI не должен быть в пуле служебных страниц
            if(in_array($entity->uriSegment, self::RESERVED_URI)) {
                throw new ApiException(self::ERROR_MESSAGES['service_name']);
            }
            
            // URI не должен содержать ничего кроме цифр и латиницы
            if(preg_match('/[^a-z0-9]/ui', $entity->uriSegment)) {
                throw new ApiException(self::ERROR_MESSAGES['unallowed_symbols']);
            }

            // нужно проверить чтобы не было активной ссылки с таким URI
            $checkResult = $lR->getLinkDataByURI($entity->uriSegment);
            if(empty($checkResult) || (bool)$checkResult['state'] == false) {
                $insertData['uri'] = $entity->uriSegment;
            } else {
                throw new ApiException(self::ERROR_MESSAGES['link_exsists']);
            }
        }

        $insertData['secret'] = $this->generateRandom(self::SECRET_RANDOM_LENGHT);
        $insertData['user_id'] = $entity->userId;
        $insertData['is_commerce'] = $entity->isCommerce;

        // время жизни ссылки
        $expire = $entity->expireDate;
        if(!empty($expire)) {
            $insertData['expire'] = $expire;
        }

        return $lR->addLink($insertData);
    }
    
    /**
     * generateRandom
     *
     * @param  int $rlb
     *
     * @return string
     */
    protected function generateRandom(int $rlb): string
    {
        $randomString = bin2hex(random_bytes($rlb));

        return (string)base_convert($randomString, 16, 36);
    }
}
