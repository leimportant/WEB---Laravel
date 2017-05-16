<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubMenu extends Model
{
    protected $table = 'submenu';

    protected $primaryKey = 'id';

    protected $fillable = ['name', 'menu_id', 'url', 'access', 'parent_id', 'flag', 'sorting'];

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

	 public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
	 
}

