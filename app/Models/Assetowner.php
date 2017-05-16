<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Auth;

class Assetowner extends Model
{
    protected $table = 'm_asset_owner';

	protected $primaryKey = 'owner_id'; 

    public $incrementing = false;

    protected $fillable = ['owner_id', 'owner_name', 'flag'];

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
