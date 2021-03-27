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
            11 => 'Списание со счёта отправителя',
            12 => 'Списание с основного кошелька на транзакционный',
            13 => 'Списание с основного кошелька (комиссия)',
            14 => 'Списание с основного кошелька (сжигание)',
            15 => 'Списание с транзакционного на транзакционный получателя',
            16 => 'Списание с транзитного на основной кошелёк',
            17 => 'Начисление на кошелёк получателя',
            18 => 'C комиссионого на транзитный',
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

        $t->type = $type;
        $t->tid = Transaction::generateTid();
        $t->nextDate = $status === 2 ? date("Y-m-d H:i:s") : '2999-01-01 00:00:00';
        $t->retryCount = '0';
        $t->errorCode = 0;
        $t->status = $status;
        $t->amount = $amount;
        $t->currency = 'DLXT';
        $t->updatedAt = date("Y-m-d H:i:s");
        $t->createdAt = date("Y-m-d H:i:s");
        $t->duration =  0;
        $t->data = json_encode($data);
        $t->trid = $trId;

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

    public static function processTransactions() {
        $transactions = self::where('nextDate', '<', date("Y-m-d H:i:s"))->get();

        foreach ($transactions as $transaction) {
            self::processTransaction($transaction);
        }
    }

    public static function processTransactionWW($transaction) {
        var_dump($transaction->id);
        $data = json_decode($transaction->data, true);

        $project = Project::getById($data['project']);
        $addr = $data['user'];

        $token = $project->token;
        $endpoint = $project->api_endpont;

        $url = "$endpoint/change_wallet_amount?token=".$token."&amount=".$transaction->amount."&wallet=".$addr."&transaction_id=".$transaction->tid."&transfer_id=".$transaction->trid;



        try {
            $json = file_get_contents($url);

            $data = json_decode($json, true);

            $ok = $data['success'] ?? false;

            $transaction->data = json_encode(array_merge(
                json_decode($transaction->data, true),
                $data
            ));


            return $data['success'] === true ? 0 : 1104;

        } catch (Exception $ex) {
        }

        return 1103;

    }

    public static function processTransactionBlockChain($transaction) {
        $data = json_decode($transaction->data, true);

        $walletFrom = Wallet::getWallet($data['fromProject'], $data['fromType'] ?? NULL, $data['fromAddr'] ?? NULL);
        $walletTo = Wallet::getWallet($data['toProject'], $data['toType'] ?? NULL, $data['toAddr'] ?? NULL);

        $from = $walletFrom->addr;
        $fromKey = $walletFrom->pkey;
        $amount = $transaction->amount;
        $to = $walletTo->addr;
        $code = 0;


        try {
            $port = env('FLASK_PORT');
            $url = "http://localhost:".$port."/send-wallet-wallet?from=".$from."&to=".$to."&fromKey=".$fromKey."&amount=".$amount;

            $resultData = file_get_contents($url);

            $result['resultData'] = $resultData;

            $json = json_decode($resultData, true);

            $transaction->data = json_encode(array_merge(
                json_decode($transaction->data, true),
                $json
            ));

            if (isset($json['result']) &&  $json['result'] !== 'success') {
                $code = 10003;
            }

        } catch (Exception $ex) {

            $code = 1002;
        }

        return $code;
    }

    public static function getNextTransaction($trid) {
        return Transaction::where(['trid' => $trid, 'status' => 1])
                ->orderBy('type', 'asc')->first();
    }

    public static function processTransaction($transaction) {

        $transaction->nextDate = date("Y-m-d H:i:s", strtotime("+".($transaction->retryCount)." minutes"));
        $transaction->save();

        $start = microtime(true);
        if ($transaction->type === 11 || $transaction->type === 17) {
            $error = self::processTransactionWW($transaction);
        } else {
            $error = self::processTransactionBlockChain($transaction);
        }

        $transfer = Transfer::where('trid', $transaction->trid)->first();

        if ($error) {
            $transaction->errorCode = $error;
            $transaction->retryCount++;

        } else {
            $nxtTransaction = self::getNextTransaction($transaction->trid);

            if ($nxtTransaction) {
                $nxtTransaction->status = 2;
                $nxtTransaction->nextDate = date("Y-m-d H:i:s");
                $nxtTransaction->save();
                $transfer->step++;
            } else {
                $transfer->status = 2;
            }

            $transaction->duration = microtime(true) - $start;
            $transaction->status = 3;
            $transaction->nextDate = '2099-01-01 00:00:00';
        }
        $transaction->updatedAt = date("Y-m-d H:i:s");
        $transaction->save();

        $transfer->dateUpdated = date("Y-m-d H:i:s");
        $transfer->save();

    }
}
