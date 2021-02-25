<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transaction extends Model
{
    protected $table = 'transaction';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'relationId', 'addr', 'pkey', 'status', 'currency', 'rootType'
    ];

    protected $rootTypes = [
        0 => 'Не назначен',
        1 => 'Основной',
        2 => 'Комиссионный',
        3 => 'Резервный',
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
        if ($attr === 'rootType') {
            return $this->rootTypes[$this->rootType];
        }
        if ($attr === 'relationId') {
            if (!$this->relationId) return 'Нет';

            $proj = Project::where('id', $this->relationId)->first();

            return $proj->name . '(' . $proj->pref . ')';
        }
        return $this->$attr;
    }


    public function getOptions($attr) {
        if ($attr === 'status') {
            return [
                1 => 'Активный',
                2 => 'Выключен'
            ];
        }
        if ($attr === 'type') {
            return [
                1 => 'Внутренний',
                2 => 'Внешний'
            ];
        }
        if ($attr === 'rootType') {
            return $this->rootTypes;
        }
        if ($attr === 'relationId') {
            $all = Project::all();

            $result = [
                0 => 'Не назвначен'
            ];

            foreach ($all as $proj) {
                $result[$proj->id] = $proj->name . '(' . $proj->pref . ')';
            }

            return $result;
        }


        return [];
    }

    public function isSelect($attr) {
        return in_array($attr, ['status', 'type', 'relationId', 'rootType']);
    }

    public static function getDayValue($project) {
        return 1000;
    }

    public static function getMonthValue($project) {
        return 1200;
    }
}
