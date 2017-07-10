<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class MaterialMasterForm extends Model
{
    protected $table = 'material_master_form';

    protected $fillable = ['id','name_of_requester', 'dept_id', 'sub_dept', 'plant_id', 'plant_other', 'division_requester', 'division_requester_other', 'material_category', 'date_requested', 'material_name','material_name_new', 'plant_req', 'plant_req_other', 'sloc0', 'sloc1','conversion1', 'conversion2', 'uom1', 'uom2', 'division_req','division_req_other','material_type_id','purchasing_org','costing_lot_size','lead_time','safety_stok','sap_material_name', 'sap_receive_date', 'sap_material_number', 'sap_complete_date', 'email_from','sap_user_entry','flag'];

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
        if (Auth::user()->id == 402 || Auth::user()->id == 399 || Auth::user()->id == 400) {
             return $query->wherein('flag', ['1', '2', '3', '4', '5']);
        } else {
             return $query->where('created_by',  Auth::user()->id);
        }
       
    }

	public function scopeAdmin($query)
    {
        return $query->wherein('flag',  ['1', '4']);
    }

          public function dept0()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

       public function subdept0()
    {
        return $this->belongsTo(Subdepartment::class, 'sub_dept');
    }

      public function plant0()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }
      public function plantreq0()
    {
        return $this->belongsTo(Plant::class, 'plant_req');
    }
      public function esloc0()
    {
        return $this->belongsTo(Storagelocation::class, 'sloc0');
    }
      public function esloc1()
    {
        return $this->belongsTo(Storagelocation::class, 'sloc1');
    }

      public function divreq0()
    {
        return $this->belongsTo(Storagelocation::class, 'sloc1');
    }
      public function material_type0()
    {
        return $this->belongsTo(Materialtypegroup::class, 'material_type_id');
    }
      public function purchasing0()
    {
        return $this->belongsTo(Department::class, 'purchasing_org');
    }

    
}
