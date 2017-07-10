<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class RoleUsers extends Model
{
    protected $table = 'role_user';

    protected $fillable = ['user_id', 'role_id'];

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

	 
}
