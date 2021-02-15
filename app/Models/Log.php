<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Log extends Model
{
    protected $table = 'api_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api', 'prjectId', 'request', 'response', 'date'
    ];

    public $timestamps = false;


    public static function getListFields() {
        return [
            'id' => 'ID',
            'projectId' => 'Project ID',
            'api' => 'API',
            'request' => 'Request',
            'response' => 'Response',
            'date' => 'Время',
        ];
    }


    public static function getViewFields() {
        return [];
    }

    public static function defaultInputList() {
        return [];
    }

    public static function processPost($id) {
    }

    public function getAttr($attr) {
        return $this->$attr;
    }
}
