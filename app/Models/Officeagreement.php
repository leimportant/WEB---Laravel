<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Officeagreement extends Model
{
    protected $table = 'office_agreement';

  	public $incrementing = true;

    protected $fillable = ['agreement_name', 'company_name', 'owner_name', 'office_level', 'email', 'address_office', 'telepon_no', 'bank', 'bank_branch', 'rekening_no', 'rekening_name', 'flag', 'padimas_flag', 'garmelia_flag'];

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
