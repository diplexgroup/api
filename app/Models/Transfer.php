<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transfer extends Base
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



    public static function getDayValue($project) {
        return Transfer::where('fromProject', $project->id)
            ->where('status', 2)
            ->where('dateCreated', '>=', date("Y-m-d 00:00:00"))
            ->sum('amount');
    }

    public static function getMonthValue($project) {
        return Transfer::where('fromProject', $project->id)
            ->where('status', 2)
            ->where('dateCreated', '>=', date("Y-m-00 00:00:00"))
            ->sum('amount');
    }

    public static function createComission($amount, $project, $user, $error) {
        $fromProjectId = $project->id;
        $toProjectId = $project->id;


        $model = new self();


        $model->amount = $amount;
        $model->type = 2;
        $model->step = 1;
        $model->fromProject = $fromProjectId;
        $model->toProject = $toProjectId;
        $model->dateCreated = date("Y-m-d H:i:s");
        $model->dateUpdated = date("Y-m-d H:i:s");
        $model->toAddress = $user;
        $model->fromAddress = '-';
        $model->errorCode = $error;
        $model->status = $error ? 3 : 1;
        $model->status = $error ? 3 : 1;
        $model->trid = Transaction::generateTid();

        $model->save();

        if (!$error) {
            Transaction::createTransaction(18, 2, $amount, $model->trid, ['fromProject' => $fromProjectId, 'fromType' => 3, 'toProject' => $fromProjectId, 'toType' => 1]);

            Transaction::createTransaction(17, 1, $amount, $model->trid, ['project' => $toProjectId, 'user' => $user]);

            return $model;
        }

        return null;
    }


    public static function create($amount, $fromProject, $toProject, $fromUser, $toUser, $road, $error) {
        $fromProjectId = $fromProject->id;
        $toProjectId = $toProject->id;


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

            $fee = $road->calculateFee($amount);
            $burn = $road->burn_percent * $fee;

            $fromLast = ['fromProject' => $fromProjectId];

            if ($fromProject->pref !== 'OUT') {

                Transaction::createTransaction(11, 2, -$amount, $model->trid, ['project' => $fromProjectId, 'user' => $fromUser]);


                Transaction::createTransaction(12, 1, $amount, $model->trid, ['fromProject' => $fromProjectId, 'fromType' => 1, 'toProject' => $fromProjectId, 'toType' => 2]);

                if ($fee) {
                    Transaction::createTransaction(13, 1, $fee - $burn, $model->trid, ['fromProject' => $fromProjectId, 'fromType' => 2, 'toProject' => $fromProjectId, 'toType' => 3]);

                    $amount -= $fee - $burn;
                }

                if ($burn) {
                    Transaction::createTransaction(14, 1, $burn, $model->trid, ['fromProject' => $fromProjectId, 'fromType' => 2, 'toProject' => 0, 'toType' => 4]);

                    $amount -= $burn;
                }

                $fromLast[ 'fromType'] = 2;

            } else {

                if ($fee) {
                    Transaction::createTransaction(13, 1, $fee - $burn, $model->trid, ['fromProject' => $fromProjectId, 'fromAddr' => $fromUser, 'toProject' => $fromProjectId, 'toType' => 3]);

                    $amount -= $fee - $burn;
                }

                if ($burn) {
                    Transaction::createTransaction(14, 1, $burn, $model->trid, ['fromProject' => $fromProjectId, 'fromAddr' => $fromUser, 'toProject' => 0, 'toType' => 4]);

                    $amount -= $burn;
                }

                $fromLast[ 'fromAddr'] = $fromUser;
            }



            if ($toProject->pref !== 'OUT') {

                if ($fromProjectId !== $toProjectId) {
                    Transaction::createTransaction(15, 1, $amount, $model->trid, array_merge($fromLast, ['toProject' => $toProjectId, 'toType' => 2]));
                }

                Transaction::createTransaction(16, 1, $amount, $model->trid, ['fromProject' => $toProjectId, 'fromType' => 2, 'toProject' => $toProjectId, 'toType' => 1]);
                Transaction::createTransaction(17, 1, $amount, $model->trid, ['project' => $toProjectId, 'user' => $toUser]);
            } else {
                Transaction::createTransaction(16, 1, $amount, $model->trid, array_merge($fromLast, ['toProject' => $toProjectId, 'toAddr' => $toUser]));
            }

            return $model;
        }

        return null;
    }


    public static function types() {
        return [
            1 => '?? ?????????????? ???? ????????????',
            2 => '?? ?????????????????????????? ???? ??????????????????????',
        ];

    }

    public static function status() {
        return [
            1 => '????????????',
            2 => '????????????',
            3 => '????????????',
        ];
    }

    public static function getListFields() {
        return [
            'id' => 'ID',
            'trid' => 'ID ??????????????????',
            'type' => '?????? ??????????????????',
            'fromProject' => '?? ??????????????',
            'toProject' => '?? ????????????',
            'step' => '??????',
            'dateCreated' => '?????????? ????????????????',
            'dateUpdated' => '?????????? ????????????????',
            'toAddress' => '????????',
            'fromAddress' => '????????????',
            'errorCode' => '?????? ????????????',
            'status' => '????????????'
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
