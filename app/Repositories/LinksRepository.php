<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class LinksRepository extends BaseRepository
{
    const TABLE_NAME = 'links';
    const PRIMARY_KEY_FIELD = 'id';

    // Данная константа имеет публичную видимость
    // с целью использования её в моделях т.к.
    // она является зависимостью между источником данных
    // и бизнес-логикой приложения
    const DATA_STRUCTURE = [
        'user_id'       => 'int',
        'uri'           => 'string',
        'secret'        => 'string',
        'destination'   => 'string',
        'expire'        => 'date',
        'is_commerce'   => 'int',
        'state'         => 'int',
        'created_at'    => 'date',
        'updated_at'    => 'date'
    ];

    // Поля структуры, которым разрешено иметь значение null
    const NULLABLE_FIELDS = [
        'expire',
        'created_at',
        'updated_at'
    ];

    /**
     * getLinkDataByURI возвращает даныне по URI
     *
     * @param  mixed $uri
     *
     * @return array
     */
    public function getLinkDataByURI(string $uri): array
    {
        return (array)DB::table(self::TABLE_NAME)
            ->where('uri', '=', $uri)
            ->first();
    }

    public function addLink(array $data): int
    {
        // Данные поля не обязательно должны присутствовать в массиве
        // и значения полей могут нарушать логику
        $data['state'] = 1;

        $now = new \DateTime;
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        return $this->insertRData($data);
    }
}