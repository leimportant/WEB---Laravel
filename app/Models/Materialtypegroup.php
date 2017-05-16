<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Materialtypegroup extends Model
{
    protected $table = 'material_type_group';

    public $primarykey = 'id'; 
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
