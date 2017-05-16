<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Faq extends Model
{
    protected $table = 'faq';

  	public $incrementing = true;

    protected $fillable = ['question', 'answer', 'modul'];

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
