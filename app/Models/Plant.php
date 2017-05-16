<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Plant extends Model
{
    protected $table = 'plant';

  	public $incrementing = false;

    protected $fillable = ['id', 'name'];

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
