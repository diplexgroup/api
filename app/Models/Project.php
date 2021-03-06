<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Project extends Base
{
    protected $table = 'project';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'api_endpont', 'api_front_link', 'type', 'addr_need_flag', 'pref', 'description', 'status', 'token', 'show_in_explorer', 'svg', 'ip'
    ];

    public $timestamps = false;

    public static function findByPref($pref) {
        return self::where('pref', $pref)->first();
    }

    public static function getName($id) {
        $proj = self::where('id', $id)->first();

        return $proj ? $proj->name : '';
    }

    public static function getListFields() {
        return [
            'id' => 'ID',
            'pref' => 'Префикс',
            'name' => 'Название',
            'api_endpont' => 'Endpoint',
            'api_front_link' => 'Ссылка на проект',
            'status' => 'Статус',
            'show_in_explorer' => 'Показывать в Explorer'
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
            'show_in_explorer' => 'Показывать в Explorer',
            'svg' => 'SVG',
            'ip' => 'IP проекта (для whitelist)'
        ];
    }

    public static function defaultInputList() {
        $list = [
            'name', 'api_endpont', 'api_front_link', 'type', 'addr_need_flag', 'pref', 'description', 'status', 'token', 'show_in_explorer', 'svg', 'ip'
        ];

        $result = [];
        $fields = self::getViewFields();

        foreach ($list as $field) {
            $result[$field] = $fields[$field] ?? $field;
        }

        return $result;
    }

    public function setAttr($attr, $value) {
        if ($attr === 'svg') {

            if (!$this->id) return;

            Storage::disk('svg')->put($this->id . '.svg', $value);

            $this->svg = $this->id . '.svg';

            return;
        }

        $this->$attr = $value;
    }

    public static function processPost($id) {

        $model = $id ? self::where(['id' => $id])->first() : new Project();

        $forms = $_POST['forms'];

        foreach ($forms as $field => $value) {
            $model->setAttr($field, $value);
        }

        $model->save();

        if (!$id) {
            $model->setAttr('svg', $forms['svg']);
            $model->save();
        }

        return $model->id;
    }

    public function getAttr($attr) {
        if ($attr === 'addr_need_flag') {
            return $this->addr_need_flag ? 'Да' : 'Нет';
        }
        if ($attr === 'show_in_explorer') {
            return $this->show_in_explorer ? 'Да' : 'Нет';
        }

        if ($attr === 'api_front_link') {
            $link = $this->api_front_link;
            return "<a href='$link'>$link</a>";
        }

        if ($attr === 'status') {
            return $this->status === 1 ? 'Активен' : 'Заблокирован';
        }

        if ($attr === 'type') {
            return $this->type === 1 ? 'Внутренний' : 'Внешний';
        }

        if ($attr === 'svg') {
            return $this->svg();
        }

        return $this->$attr;
    }

    public function getOptions($attr) {
        if ($attr === 'status') {
            return [
                1 => 'Активный',
                2 => 'Заблокирован'
            ];
        }
        if ($attr === 'addr_need_flag') {
            return [
                1 => 'Да',
                0 => 'Нет'
            ];
        }
        if ($attr === 'show_in_explorer') {
            return [
                1 => 'Да',
                0 => 'Нет'
            ];
        }
        if ($attr === 'type') {
            return [
                1 => 'Внутренний',
                0 => 'Внешний'
            ];
        }


        return [];
    }

    public function isSelect($attr) {
        return in_array($attr, ['status', 'addr_need_flag', 'type', 'show_in_explorer']);
    }

    public static function getById($id) {
        return self::where('id', $id)->first();
    }

    public static function getShort($id) {
        return self::getById($id)->pref ?? 'UNKNOWN';
    }

    public function svg() {
        try {
            return  Storage::disk('svg')->get($this->id . '.svg');
        } catch (\Exception $ex) {
            return '';
        }
    }

    public function getInputAttr($attr) {

        if ($attr === 'svg') {
            return $this->svg();
        }

        return $this->$attr;
    }
}
