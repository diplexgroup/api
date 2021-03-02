<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transaction extends Model
{
    static function statuses() {
        return [
            1 => 'Ожидает',
            2 => 'Открыта',
            3 => 'Успешна',
            4 => 'Ошибка',
        ];
    }


    const DIRECT = 1;

    protected $table = 'transaction';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tid', 'type', 'nextDate', 'retryCount', 'errorCode', 'status', 'amount', 'currency', 'data'
    ];

    public $timestamps = false;


    public static function generateTid() {

        $t = dechex(time()).rand(1000000, 9999999).rand(1000000, 9999999).rand(1000000, 9999999).rand(1000000, 9999999).rand(100, 999);


        return $t;
    }

    public static function getListFields() {
        return [
            'id' => 'ID',
            'type' => 'Тип',
            'tid' => 'ID транзакции',
            'nextDate' => 'Время обновления',
            'retryCount' => 'Кол-во ретраев',
            'errorCode' => 'Код ошибки',
            'status' => 'Статус',
            'amount' => 'Цена',
            'currency' => 'Валюта',
            'data' => 'Доп данные'
        ];
    }


    public static function getViewFields() {
        return [];
    }

    public static function defaultInputList() {
        return [];
    }

    public static function processPost($id) {
    }

    public function getAttr($attr) {
        if ($attr === 'type') {
            return $this->type === '1' ? 'Прямая транзакция' : 'Неизвестно';
        }

        if ($attr === 'status') {
            return self::statuses()[$this->status];
        }

        return $this->$attr;
    }
}
