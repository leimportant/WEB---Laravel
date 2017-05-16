<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Auth;

class Productpricepadimas extends Model
{
    protected $table = 'product_price_padimas';

     public $primarykey = 'id'; 

    public $incrementing = true;

    protected $fillable = ['name', 'area', 'dbp', 'sobp', 'wbp', 'rbp', 'cbp'];

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
