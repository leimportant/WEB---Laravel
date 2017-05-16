<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMasterForm extends Model
{
    protected $table = 'asset_master_form';

    protected $fillable = ['asset_id','requester_id','requester_date','asset_owner','asset_name','asset_type','plant','qty','uom','price','location','asset_master_class','asset_category_id','asset_category_sub_id','cost_center_id','asset_purpose','asset_number','asset_desc','sap_asset_master','sap_asset_class','sap_asset_class_sub','is_pdf','flag','photo','remark','created_by','manager_approver_flag','manager_approved_by','director_approved_by','finance_director_approved_by','accounting_approved_by','modified_at','manager_approved_at','director_approved_at','finance_director_approved_at','accounting_approved_at'];

    public static function boot() {

	    parent::boot();
		    static::creating(function($post)
		    {
		      $post->created_by = Auth::user()->id;
		    });
		    
	}
}
