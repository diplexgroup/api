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
        $model->step = 1;
        $model->fromProject = $fromProjectId;
        $model->toProject = $toProjectId;
        $model->dateCreated = date("Y-m-d H:i:s");
        $model->dateUpdated = date("Y-m-d H:i:s");
        $model->toAddress = $toUser;
        $model->fromAddress = $fromUser;
        $model->errorCode = $error;
        $model->status = $error ? 3 : 1;
        $model->status = $error ? 3 : 1;
        $model->trid = Transaction::generateTid();

        $model->save();


        if (!$error) {
            Transaction::createTransaction(11, 2, -$amount, $model->trid, ['project' => $fromProjectId, 'user' => $fromUser]);
            Transaction::createTransaction(17, 1, $amount, $model->trid, ['project' => $toProjectId, 'user' => $toUser]);

            $fee = $road->calculateFee($amount);
            $burn = $road->burn_percent * $amount;


            if ($fee) {
                Transaction::createTransaction(13, 1, $fee, $model->trid, ['fromProject' => $fromProjectId, 'fromType' => 1, 'toProject' => $fromProjectId, 'toType' => 3]);

                $amount -= $fee;
            }

            if ($burn) {
                Transaction::createTransaction(14, 1, $burn, $model->trid, ['fromProject' => $fromProjectId, 'fromType' => 1, 'toProject' => 0, 'toType' => 4]);

                $amount -= $burn;
            }

            Transaction::createTransaction(12, 1, $amount, $model->trid, ['fromProject' => $fromProjectId, 'fromType'=>1, 'toProject' => $fromProjectId, 'toType'=>2]);

            if ($fromProjectId !== $toProjectId) {
                Transaction::createTransaction(15, 1, $amount, $model->trid, ['fromProject' => $fromProjectId, 'fromType' => 2, 'toProject' => $toProjectId, 'toType' => 2]);
            }

            Transaction::createTransaction(16, 1, $amount, $model->trid, ['fromProject' => $toProjectId, 'fromType'=>2, 'toProject' => $toProjectId, 'toType'=>1]);
        }
    }


    public static function types() {
        return [
            1 => 'С проекта на проект',
        ];

    }

    public static function status() {
        return [
            1 => 'Открыт',
            2 => 'Упешно',
            3 => 'Ошибка',
        ];
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
        if ($attr === 'type') return self::types()[$this->type];
        if ($attr === 'status') return self::status()[$this->status];
        if ($attr === 'toProject' || $attr === 'fromProject') return Project::getName($this->$attr);

        return $this->$attr;
    }
}
