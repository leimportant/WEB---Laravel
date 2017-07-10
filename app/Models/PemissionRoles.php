<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class PemissionRoles extends Model
{
    protected $table = 'permission_role';

    protected $fillable = ['permission_id', 'role_id'];

    public $incrementing = false;

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

	  public function role0()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

     public function permission0()
    {
        return $this->belongsTo(Permissions::class, 'permission_id');
    }
	 
}
