<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PolicyProcedure extends Model
{
  	protected $table = 'PolicyandprocedureServices';

    protected $fillable = [

       'id','Title','Description','isPdf' ,'Status' ,'isActive',
       
    ];

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
