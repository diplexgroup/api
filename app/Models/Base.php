<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    public function getInputAttr($attr) {
        return $this->$attr;
    }
}
