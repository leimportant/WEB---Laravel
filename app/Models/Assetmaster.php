<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Auth;

class Assetmaster extends Model
{
    protected $table = 'm_asset_master';

     public $primarykey = 'asset_id'; 

    public $incrementing = true;

    protected $fillable = ['asset_id'];

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
