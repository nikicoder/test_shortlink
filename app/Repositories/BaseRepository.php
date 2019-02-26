<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
 
class BaseRepository
{
    // Паттерн использования данного класса состоит в том, что
    // он не может быть инициализирован напрямую, только потомком
    protected function __construct() { }

    /**
     * getRDataByID функция возвращает данные из источника по ID
     * ID в данном случае является первичным ключем
     *
     * @param  $id
     *
     * @return array
     */
    public function getRDataByID($id): array
    {
        if(empty(static::PRIMARY_KEY_FIELD)) {
            throw new \Exception('Repository not supported getRDataByID() method');
        }

        return (array)DB::table(static::TABLE_NAME)
            ->where(static::PRIMARY_KEY_FIELD, '=', $id)
            ->first();
    }

    /**
     * insertRData функция добавляет данные в источник
     *
     * @param  array $data
     * 
     * @return mixed
     */
    protected function insertRData(array $data)
    {
        return !empty(static::PRIMARY_KEY_FIELD) ? 
            DB::table(static::TABLE_NAME)
                ->insertGetId($data) :
            DB::table(static::TABLE_NAME)
                ->insert($data);
    }

    /**
     * updateRDataByID функция обновляет данные в источнике по ID
     *
     * @param  int $id
     *
     * @return mixed
     */
    protected function updateRDataByID(int $id, $updateData)
    {
        return DB::table(static::TABLE_NAME)
            ->where(static::PRIMARY_KEY_FIELD, '=', $id)
            ->update($updateData);
    }

    /**
     * createEmptyStructure функция создает пустую структуру данных
     * в виде массива с целью работы с ней на уровне моделей данных
     *
     * @return array
     */
    public function createEmptyStructure(): array
    {
        if(empty(static::DATA_STRUCTURE)) {
            throw new \Exception('Repository has no structure');
        }

        $extTypes = ['date'];
        $result = [];

        foreach(static::DATA_STRUCTURE as $field => $type) {
            
            $result[$field] = null;
            // если явно разрешено данному полю быть null
            if(!empty(static::NULLABLE_FIELDS) 
                && in_array($field, static::NULLABLE_FIELDS)) {
                    
                continue;
            }

            if(!in_array($type, $extTypes)) {
                settype($result[$field], $type);
            } else {
                switch($type) {
                    case 'date': 
                        $result[$field] = new \DateTime;
                        break;
                    default:
                        throw new \Exeption('Invalid type of repository data');
                }
            }
        }

        return $result;
    }
}