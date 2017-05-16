<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Assetlocation extends Model
{
    protected $table = 'm_asset_location';
	
	protected $primaryKey = 'loc_id'; 

    public $incrementing = false;

    protected $fillable = ['loc_id', 'loc_name', 'plant_id'];

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

	public function plant0()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }
   
}
