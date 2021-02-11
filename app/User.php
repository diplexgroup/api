<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'updated_at', 'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'confirm_code', 'confirm_attempts',
    ];

    public static function getListFields() {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'status' => 'Статус'
        ];
    }


    public static function getViewFields() {
        return [
            'email' => 'Email',
            'password' => 'Пароль (хэш)',
            'confirm_code' => 'Код подтверждения',
            'status' => 'Статус',
            'roles' => 'Роли',
            'updated_at' => 'Обновлён',
        ];
    }

    public static function defaultInputList() {
        $list = [
            'email', 'password', 'status', 'roles'
        ];

        $result = [];
        $fields = self::getViewFields();

        foreach ($list as $field) {
            $result[$field] = $fields[$field] ?? $field;
        }

        return $result;
    }

    public function setAttr($attr, $value) {
        if ($attr === 'password') {
            if ($this->password !== $value) {
                $this->password = Hash::make($value);
            }

            return;
        }

        $this->$attr = $value;
    }

    public static function processPost($id) {

        $model = $id ? self::where(['id' => $id])->first() : new self();

        $forms = $_POST['forms'];

        foreach ($forms as $field => $value) {
            $model->setAttr($field, $value);
        }

        $model->save();

        return $model->id;
    }

    public function getAttr($attr) {
        if ($attr === 'status') {
            return $this->status === 1 ? 'Активный' : 'Выключен';
        }

        return $this->$attr;
    }
}
