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
        'from_project', 'to_project', 'tax_strategy', 'status', 'min_amount', 'max_amount', 'max_day_amount', 'max_month_amount', 'burn_percent'
    ];

    public $timestamps = false;


    public static function getListFields() {
        return [
            'id' => 'ID',
            'from_project' => 'От',
            'to_project' => 'К',
            'status' => 'Статус',
        ];
    }


    public static function getViewFields() {
        return [
            'from_project' => 'От',
            'to_project' => 'К',
            'status' => 'Статус',
            'tax_strategy' => 'Стратегия категорий',
            'min_amount' => 'Минимальная сумма за раз',
            'max_amount' => 'Максимальная сумма за раз',
            'max_day_amount' => 'Максимальная сумма в день',
            'max_month_amount' => 'Максимальная сумма в месяц',
            'burn_percent' => 'Процент от комиссии сжигается (0.01 = 1%)',
        ];
    }

    public static function defaultInputList() {
        $list = [
            'from_project', 'to_project', 'status',
            'min_amount', 'max_amount', 'burn_percent',
            'max_day_amount', 'max_month_amount'
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

        $another = self::where(['from_project' => $model->from_project, 'to_project' => $model->to_project])->first();
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
        if ($attr === 'from_project' || $attr === 'to_project') {
            $project = Project::where('id', $this->$attr)->first();

            if (!$project) return 'not found';

            return $project->name;
        }

        return $this->$attr;
    }


    public function getOptions($attr) {
        if ($attr === 'status') {
            return [
                1 => 'Активный',
                2 => 'Выключена'
            ];
        }
        if ($attr === 'from_project' || $attr === 'to_project') {
            $all = Project::all();

            $result = [
            ];

            foreach ($all as $proj) {
                $result[$proj->id] = $proj->name . '(' . $proj->pref . ')';
            }

            return $result;
        }


        return [];
    }

    public function isSelect($attr) {
        return in_array($attr, ['status', 'from_project', 'to_project']);
    }
}
