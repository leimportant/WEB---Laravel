<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Profile extends Model
{
    protected $table = 'profile';

    public $incrementing = false;

    protected $fillable = ['id', 'phone', 'photo', 'plant', 'dept_id', 'sub_dept'];

    
	public function user0()
    {
        return $this->belongsTo('App\User', 'user_id');

    }

         public function dept0()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

       public function plant0()
    {
        return $this->belongsTo(Plant::class, 'plant');
    }

       public function subdept0()
    {
        return $this->belongsTo(Subdepartment::class, 'sub_dept');
    }

}
