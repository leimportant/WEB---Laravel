<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Assetsubclass extends Model
{
    protected $table = 'm_asset_class_sub';
	
	protected $primaryKey = 'id'; 

    public $incrementing = true;

    protected $fillable = ['id', 'sub_name', 'asset_id'];

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
