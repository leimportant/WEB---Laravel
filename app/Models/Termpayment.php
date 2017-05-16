<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Auth;

class Termpayment extends Model
{
    protected $table = 'term_of_payment';

    public $incrementing = true;

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
