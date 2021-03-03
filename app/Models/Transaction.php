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

    static function types() {
        return [
            0 => 'Неизвестно',
            1 => 'Прямой перевод',
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
        'tid', 'type', 'nextDate', 'retryCount', 'errorCode', 'status', 'amount', 'currency', 'data', 'createdAt', 'duration', 'updatedAt', 'trid'
    ];

    public $timestamps = false;


    public static function generateTid() {

        $t = dechex(time()).rand(1000000, 9999999).rand(1000000, 9999999).rand(1000000, 9999999).rand(1000000, 9999999).rand(100, 999);


        return $t;
    }

    public static function createTransaction($type, $status, $amount, $trId, $data) {
        $t = new Transaction();

        $t->type = Transaction::DIRECT;
        $t->tid = Transaction::generateTid();
        $t->nextDate = $status === 1 ? date("Y-m-d H:i:s") : '2999-01-01 00:00:00';
        $t->retryCount = '0';
        $t->errorCode = 0;
        $t->status = $status;
        $t->amount = $amount;
        $t->currency = 'DLXT';
        $t->updatedAt = date("Y-m-d H:i:s");
        $t->createdAt = date("Y-m-d H:i:s");
        $t->duration =  0;
        $t->data = json_encode($data);
        $t->trId = $trId;

        $t->save();
    }

    public static function getListFields() {
        return [
            'id' => 'ID',
            'type' => 'Тип',
            'tid' => 'ID транзакции',
            'trid' => 'ID трансфера',
            'createdAt' => 'Время создания',
            'updatedAt' => 'Время закрытия',
            'duration' => 'Время выполнения(c)',
            'retryCount' => 'Кол-во ретраев',
            'errorCode' => 'Код ошибки',
            'status' => 'Статус',
            'amount' => 'Цена',
            'currency' => 'Валюта',
            'data' => 'Доп данные',

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
            return self::types()[$this->type] ?? self::types()[0];
        }

        if ($attr === 'status') {
            return self::statuses()[$this->status] ?? 'НЗ';
        }

        return $this->$attr;
    }
}
