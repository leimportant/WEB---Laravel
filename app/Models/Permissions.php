<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Permissions extends Model
{
    protected $table = 'permissions';

    protected $primaryKey = 'id';

    protected $fillable = ['name', 'display_name'];

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
