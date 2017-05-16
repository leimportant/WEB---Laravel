<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// use App\Models\Department;

use Auth;

class Subdepartment extends Model
{
    protected $table = 'subOrganization';

    public $incrementing = true;

    protected $fillable = ['id_organization', 'name'];

    public static function boot() {

	    parent::boot();
		    static::creating(function($post)
		    {
		      $post->created_by = $post->updated_by = Auth::user()->id;
		    });
		    static::updating(function($post)
		    {
		      $post->updated_by = Auth::user()->id;
		    });
	}

	 public function setRoleIdAttribute($input)
    {
        $this->attributes['id_organization'] = $input ? $input : null;
    }

	public function subdept()
    {
        return $this->belongsTo(Department::class, 'id_organization');
    }
   
}
