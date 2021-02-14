<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProjectRoad extends Model
{
    protected $table = 'project_road';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fromProject', 'toProject', 'tax_strategy', 'status', 'minAmount', 'maxAmount', 'burnPercent'
    ];

    public $timestamps = false;


    public static function getListFields() {
        return [
            'id' => 'ID',
            'fromProject' => 'От',
            'toProject' => 'К',
            'status' => 'Статус',
        ];
    }


    public static function getViewFields() {
        return [
            'fromProject' => 'От',
            'toProject' => 'К',
            'status' => 'Статус',
            'tax_strategy' => 'Стратегия категорий',
            'minAmount' => 'Минимальная сумма',
            'maxAmount' => 'Максимальная сумма',
            'burnPercent' => 'Процент от комиссии сжигается (0.01 = 1%)',
        ];
    }

    public static function defaultInputList() {
        $list = [
            'fromProject', 'toProject', 'status',
            'minAmount', 'maxAmount', 'burnPercent',
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

        $another = self::where(['fromProject' => $model->fromProject, 'toProject' => $model->toProject])->first();
        if ($another && $another->id !== $model->id) {
            throw new \Exception('Уже существует');
        }


        $model->save();

        return $model->id;
    }

    public function getAttr($attr) {
        if ($attr === 'status') {
            return $this->status === 1 ? 'Активна' : 'Выключена';
        }
        if ($attr === 'fromProject' || $attr === 'toProject') {
            $project = Project::where('id', $this->$attr)->first();

            if (!$project) return 'not found';

            return $project->name;
        }

        return $this->$attr;
    }
}
