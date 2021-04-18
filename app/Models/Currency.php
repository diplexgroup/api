<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Currency extends Base
{
    protected $table = 'currency';
    protected $currencyTypeMap = [
      1 => 'Фиат',
      2 => 'Крипто'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'type', 'inDLX'
    ];

    public $timestamps = false;


    public static function getListFields() {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'code' => 'Код ISO',
            'type' => 'Тип валюты',
            'inDLX' => 'Курс',
        ];
    }


    public static function getViewFields() {
        return [
            'name' => 'Наименование',
            'code' => 'Код ISO',
            'type' => 'Тип валюты',
            'inDLX' => 'Курс',
        ];
    }

    public static function defaultInputList() {
        $list = [
            'name', 'code', 'type', 'inDLX'
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
        if ($attr === 'type') return $this->currencyTypeMap[$this->$attr] ?? 'Неизвестно';
        return $this->$attr;
    }


    public function getOptions($attr) {
        if ($attr === 'type') {
            return $this->currencyTypeMap;
        }


        return [];
    }

    public function isSelect($attr) {
        return in_array($attr, ['type']);
    }
}
