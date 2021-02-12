<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Wallet extends Model
{
    protected $table = 'wallet';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'relationId', 'addr', 'pkey', 'status', 'currency', 'rootType'
    ];

    public $timestamps = false;


    public static function getListFields() {
        return [
            'id' => 'ID',
            'type' => 'Тип',
            'rootType' => 'Роль в проекте',
            'relationId' => 'Привязан к',
            'addr' => 'Адрес',
            'status' => 'Статус',
            'currency' => 'Валюта',
        ];
    }


    public static function getViewFields() {
        return [
            'type' => 'Тип',
            'rootType' => 'Роль в проекте',
            'relationId' => 'Привязан к',
            'addr' => 'Адрес',
            'status' => 'Статус',
            'currency' => 'Валюта',
            'pkey' => 'Приватный ключ',
        ];
    }

    public static function defaultInputList() {
        $list = [
            'type', 'relationId', 'addr', 'pkey', 'status', 'currency', 'rootType'
        ];

        $result = [];
        $fields = self::getViewFields();

        foreach ($list as $field) {
            $result[$field] = $fields[$field] ?? $field;
        }

        return $result;
    }

    public function setAttr($attr, $value) {
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
        if ($attr === 'type') {
            return $this->type === '1' ? 'Внутренний' : 'Внешний';
        }
        return $this->$attr;
    }
}
