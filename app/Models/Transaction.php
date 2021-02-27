<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transaction extends Model
{
    protected $table = 'transaction';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tid', 'nextDate', 'retryCount', 'errorCode', 'status', 'amount', 'currency', 'data'
    ];

    public $timestamps = false;


    public static function getListFields() {
        return [
            'id' => 'ID',
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
        return $this->$attr;
    }
}
