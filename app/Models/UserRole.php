<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class UserRole extends Model
{
    protected $table = 'user_role';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public $timestamps = false;


    public function isSelect($attr) {
        return false;
    }

    public static function getListFields() {
        return [
            'id' => 'ID',
            'name' => 'Название Роли',
        ];
    }


    public static function getViewFields() {
        return [
            'name' => 'Название Роли',
        ];
    }

    public static function defaultInputList() {
        $list = [
            'name'
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

        return $this->$attr;
    }
}
