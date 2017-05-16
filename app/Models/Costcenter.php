<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Costcenter extends Model
{
    protected $table = 'm_cost_center';
	
	protected $primaryKey = 'cost_center_id'; 

    public $incrementing = false;

    protected $fillable = ['cost_center_id', 'cost_center_name', 'dept_id', 'flag'];

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

	public function dept0()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }
   
}
