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
        'telegram', 'uid', 'user', 'sponsor_id', 'sponsor_user_name'
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
        $t->sponsor_user_name = $spUsername;

        $t->save();
    }

    public static function getListFields() {
        return [
            'id' => 'ID',
            'telegram' => 'Тип',
            'uid' => 'ID юзера',
            'user' => 'Юзер',
            'sponsor_id' => 'ID Спонсора',
            'sponsor_user_name' => 'Спонсор',
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
