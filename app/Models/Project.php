<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Project extends Model
{
    protected $table = 'project';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'api_endpont', 'api_front_link', 'type', 'addr_need_flag', 'pref', 'description', 'status', 'token'
    ];

    public $timestamps = false;


    public static function getListFields() {
        return [
            'id' => 'ID',
            'pref' => 'Префикс',
            'name' => 'Название',
            'api_endpont' => 'Endpoint',
            'api_front_link' => 'Ссылка на проект',
            'status' => 'Статус',
        ];
    }


    public static function getViewFields() {
        return [
            'pref' => 'Префикс',
            'name' => 'Название',
            'api_endpont' => 'Endpoint',
            'api_front_link' => 'Ссылка на проект',
            'type' => 'Тип',
            'addr_need_flag' => 'Флаг внутри узловых кошельков',
            'description' => 'Описание',
            'status' => 'Статус',
            'token' => 'Токен',
        ];
    }

    public static function defaultInputList() {
        $list = [
            'name', 'api_endpont', 'api_front_link', 'type', 'addr_need_flag', 'pref', 'description', 'status', 'token'
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

        $model = $id ? self::where(['id' => $id])->first() : new Project();

        $forms = $_POST['forms'];

        foreach ($forms as $field => $value) {
            $model->setAttr($field, $value);
        }

        $model->save();

        return $model->id;
    }

    public function getAttr($attr) {
        if ($attr === 'addr_need_flag') {
            return $this->addr_need_flag ? 'Да' : 'Нет';
        }

        if ($attr === 'api_front_link') {
            $link = $this->api_front_link;
            return "<a href='$link'>$link</a>";
        }

        if ($attr === 'status') {
            return $this->status === 1 ? 'Активен' : 'Заблокирован';
        }

        return $this->$attr;
    }
}
