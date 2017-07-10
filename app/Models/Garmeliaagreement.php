<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Garmeliaagreement extends Model
{
    protected $table = 'cooperation_agreement';

  	protected $primaryKey = 'doc_id'; 

  	public $incrementing = false;

    protected $fillable = ['doc_id','agreement_id','company_type','contract_id','company_name','owner_name'
		    				,'office_level','ktp_no','email','address','telepon_no','agreement_date_min','agreement_date_max'
							,'deposit_guarantee','top_order','cash_discount','cash_discount','area'      
							,'bank_id','bank_branch','rekening_no','rekening_name','territory'
							,'territory_north','territory_south','territory_west','territory_east', 'flag', 'approved_by', 'approved_at'];

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

	 public function top0()
    {
        return $this->belongsTo(Termpayment::class, 'top_order');
    }


    public function scopeFlag($query)
    {
        return $query->wherein('company_type', ['G-AGEN', 'G-DIST'])->wherein('flag', ['0']);
    }

       public function cooperation()
    {
        return $this->belongsTo(OfficeAgreement::class, 'agreement_id');
    }

     public function bank0()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

}
