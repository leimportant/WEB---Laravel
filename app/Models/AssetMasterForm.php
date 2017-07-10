<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class AssetMasterForm extends Model
{
    protected $table = 'asset_master_form';

    protected $primaryKey = 'asset_id'; 

    public $incrementing = false;

    protected $fillable = ['asset_id','requester_id','requester_date','asset_owner','asset_name','asset_type','plant','qty','uom','price','location','asset_master_class','asset_category_id','asset_category_sub_id','cost_center_id','asset_purpose','asset_number','asset_desc','sap_asset_master','sap_asset_class','sap_asset_class_sub','is_pdf','flag','photo','remark','created_by','manager_approver_flag','manager_approved_by','director_approved_by','finance_director_approved_by','accounting_approved_by','modified_at','manager_approved_at','director_approved_at','finance_director_approved_at','accounting_approved_at', 'filename'];



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

	public function scopeUserlogin($query)
    {
        if (Auth::user()->id == 402 || Auth::user()->id == 399 || Auth::user()->id == 246 || Auth::user()->id == 159) {
             return $query->wherein('flag', ['101', '200', '250', '300', '400', '500', '600']);
        } else {
             return $query->where('created_by',  Auth::user()->id);
        }
       
    }

	public function scopeManager($query)
    {
        return $query->where('manager_approved_by',  Auth::user()->id);
    }

 	public function scopeDirector($query)
    {
        return $query->wherein('flag', ['250']);
    }


    public function scopeFinanceDirector($query)
    {
        return $query->wherein('flag', ['200', '300']);
    }

    public function scopeAdmin($query)
    {
        return $query->wherein('flag', ['400', '300']);
    }

       public function assetowner0()
    {
        return $this->belongsTo(Assetowner::class, 'asset_owner');
    }
        public function assetmaster0()
    {
        return $this->belongsTo(Assetmaster::class, 'asset_master_class');
    }

        public function location0()
    {
        return $this->belongsTo(Assetlocation::class, 'location');
    }
      public function plant0()
    {
        return $this->belongsTo(Plant::class, 'plant');
    }
     public function assetcastegory0()
    {
        return $this->belongsTo(AssetClass::class, 'asset_category_id');
    }
      public function assetcastegorysub0()
    {
        return $this->belongsTo(Assetsubclass::class, 'asset_category_sub_id');
    }

      public function costcenter0()
    {
        return $this->belongsTo(Costcenter::class, 'cost_center_id');
    }

       public function sapAsset_master0()
    {
        return $this->belongsTo(AssetMaster::class, 'sap_asset_master');
    }

       public function asset_class0()
    {
        return $this->belongsTo(AssetClass::class, 'sap_asset_class');
    }

     public function sapAsset_classSub0()
    {
        return $this->belongsTo(Assetsubclass::class, 'sap_asset_class_sub');
    }

   
}
