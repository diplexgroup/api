<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Log extends Base
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

        if ($attr === 'response') {
            try {
                return Storage::disk('api_logs')->get($this->response);
            } catch(\Exception $ex) {
                return 'file deleted';
            }
        }

        return $this->$attr;
    }
}
