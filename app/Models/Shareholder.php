<?php

namespace App\Models;

use App\Http\Helpers\ApiHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Shareholder extends Base
{
    protected $table = 'shareholder';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'telegram', 'uid', 'user', 'sponsor_id', 'sponsor_user_name', 'type'
    ];

    public $timestamps = false;

    public static function getUser($idUser) {
        $parts = explode('/', $idUser);

        $id = $parts[0];
        $username = $parts[1] ?? '';

        if (!preg_match('/^\d+$/', $id)) {
            $t = $id;
            $id = $username;
            $username = $id;
        }

        return [$id, $username];
    }

    public static function createShareholder($sds_id, $telegram, $userId, $sponsor, $sponsorId, $type) {


        $t = self::where('telegram', $telegram)->first();
        if (!$t) {
            $t = new Shareholder();
            $t->type = $type;
            $t->uid = $userId;
            $t->telegram = $telegram;
            $t->user = $sds_id;
            $t->sponsor_id = $sponsorId;
            $t->sponsor_user_name = $sponsor;
        } else {
            $map = [
                'sds_id' => 'user',
                'telegram' => 'telegram',
                'userId' => 'uid',
                'sponsor' => 'sponsor_user_name',
                'sponsorId' => 'sponsor_id',
                'type' => 'type',
            ];

            foreach ($map as $key => $target) {
                if ($$key) {
                    $t->$target = $$key;
                }
            }
        }

        $t->save();
    }

    public static function types() {
        return [
            1 => 'Юзер',
            2 => 'Акционер',
            3 => 'Акционер + Юзер',
        ];
    }

    public static function getListFields() {
        return [
            'id' => 'ID',
            'telegram' => 'Телеграм',
            'uid' => 'ID юзера',
            'user' => 'Юзер',
            'sponsor_id' => 'ID Спонсора',
            'sponsor_user_name' => 'Спонсор',
            'type' => 'Тип',
        ];
    }


    public static function getViewFields() {
        return [
            'id' => 'ID',
            'telegram' => 'Телеграм',
            'uid' => 'ID юзера',
            'user' => 'SDS ID',
            'sponsor_id' => 'ID Спонсора',
            'sponsor_user_name' => 'Спонсор',
            'type' => 'Тип',
        ];
    }

    public static function defaultInputList() {
        return [
            'id' => 'ID',
            'telegram' => 'Телеграм',
            'uid' => 'ID юзера',
            'user' => 'SDS ID',
            'sponsor_id' => 'ID Спонсора',
            'sponsor_user_name' => 'Спонсор',
            'type' => 'Тип',
        ];
    }

    public function setAttr($attr, $value) {
        $this->$attr = $value;
    }


    public function isSelect($attr) {
        return in_array($attr, ['type']);
    }

    public function getOptions($attr) {
        if ($attr === 'type') return self::types();

        return [];
    }


    public static function processPost($id) {
        $model = $id ? self::where(['id' => $id])->first() : new Shareholder();

        $forms = $_POST['forms'];

        foreach ($forms as $field => $value) {
            $model->setAttr($field, $value);
        }

        $model->save();

        return $model->id;
    }

    public function getAttr($attr) {

        if ($attr === 'type') {
            return self::types()[$this->type];
        }

        return $this->$attr;
    }


}
