<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    protected $table = 'states';
  
    protected $fillable = ['name','slug','country_id','status'];
  
    public function cities()
    {
        return $this->hasMany(Cities::class);
    }
}
