<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Modules extends Model
{
    protected $table = 'modules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'readRoles', 'writeRoles', 'link'
    ];

    public $timestamps = false;


    public static function getListFields() {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'link' => 'Ссылка',
        ];
    }


    public static function getViewFields() {
        return [
            'name' => 'Название',
            'readRoles' => 'Могут читать роли',
            'link' => 'Ссылка',
            'writeRoles' => 'Могут редактировать роли',
        ];
    }

    public static function defaultInputList() {
        $list = [
            'name', 'link', 'readRoles', 'writeRoles'
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
