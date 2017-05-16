<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Auth;

class Productdisc extends Model
{
    protected $table = 'product';

     public $primarykey = 'id'; 

    public $incrementing = true;

    protected $fillable = ['product', 'disc', 'area', 'flag'];

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

     public function area0()
    {
        return $this->belongsTo(Area::class, 'area');
    }
}
