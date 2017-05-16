<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Formgallery extends Model
{
    protected $table = 'form_gallery';

  	public $incrementing = false;

    protected $fillable = ['id', 'title', 'filename', 'extensions'];

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

	public function rules()
	{
	    return [
	        'id' => 'required|max:11',
            'title' => 'required|max:100',
            'filename' => 'required|mimes:doc,docx,pdf,xlx,xlxs,xlsx|file',
            'extensions' => 'required|max:10',
	    ];
	}
	
	 
}
