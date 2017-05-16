<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialMasterForm extends Model
{
    protected $table = 'material_master_form';

    protected $fillable = ['id'];

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
