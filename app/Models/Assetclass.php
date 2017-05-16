<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Assetclass extends Model
{
    protected $table = 'm_asset_class';
	
	protected $primaryKey = 'class_id'; 

    public $incrementing = false;

    protected $fillable = ['class_id', 'class_name', 'asset_id'];

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

	public function asset0()
    {
        return $this->belongsTo(Assetmaster::class, 'asset_id');
    }
   
}
