<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transfer extends Model
{
    protected $table = 'transfer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trid', 'type',  'fromProject', 'toProject', 'step', 'dateCreated', 'dateUpdated', 'toAddress', 'fromAddress', 'errorCode', 'status'
    ];

    public $timestamps = false;


    public static function create($amount, $fromProjectId, $toProjectId, $fromUser, $toUser, $road, $error) {
        $model = new self();


        $model->amount = $amount;
        $model->type = 1;
        $model->fromProject = $fromProjectId;
        $model->toProject = $toProjectId;
        $model->dateCreated = date("Y-m-d H:i:s");
        $model->dateUpdated = date("Y-m-d H:i:s");
        $model->toAddress = $toUser;
        $model->fromAddress = $fromUser;
        $model->errorCode = $error;
        $model->status = $error ? 1 : 3;
        $model->trid = Transaction::generateTid();

        $model->save();


        if (!$error) {

        }
    }


    public static function getListFields() {
        return [
            'id' => 'ID',
            'trid' => 'ID трансфера',
            'type' => 'Тип трансфера',
            'fromProject' => 'С проекта',
            'toProject' => 'В проект',
            'step' => 'Шаг',
            'dateCreated' => 'Время создания',
            'dateUpdated' => 'Время закрытия',
            'toAddress' => 'Кому',
            'fromAddress' => 'Откуда',
            'errorCode' => 'Код ошибки',
            'status' => 'Статус'
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
