<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Storagelocation extends Model
{
    protected $table = 'storage_location';

    protected $fillable = ['name'];

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
