<?php

namespace App\Models;

use App\Http\Helpers\ApiHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Shareholder extends Model
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

    public static function createShareholder($telegram, $user, $sponsor) {

        list($id, $username) = self::getUser($user);
        list($spId, $spUsername) = self::getUser($sponsor);


        $t = self::where('telegram', $telegram)->first();
        if (!$t) {
            $t = new Shareholder();
        }
        $t->uid = $id;
        $t->telegram = $telegram;
        $t->user = $username;
        $t->sponsor_id = $spId;
        $t->type = 2;
        $t->sponsor_user_name = $spUsername;

        $t->save();
    }

    public static function types() {
        return [
            1 => 'Акционер',
            2 => 'Юзер',
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
            'user' => 'Юзер',
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
            'user' => 'Юзер',
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
