<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Padimasagreement extends Model
{
    protected $table = 'cooperation_agreement';

  	protected $primaryKey = 'doc_id'; 

  	public $incrementing = false;

    protected $fillable = ['doc_id','agreement_id','company_type','contract_id','company_name','owner_name'
		    				,'office_level','ktp_no','email','address','telepon_no','agreement_date_min','agreement_date_max'
							,'deposit_guarantee','top_order','cash_discount','top_cash_discount','area'  
							,'product_id_1', 'product_id_2', 'product_id_3', 'product_id_4', 'product_id_5', 'product_id_6', 'product_id_7'    
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
        return $query->wherein('company_type', ['P-AGEN', 'P-DIST'])->wherein('flag', ['0']);
    }

    public function bank0()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
    public function cooperation()
    {
        return $this->belongsTo(OfficeAgreement::class, 'agreement_id');
    }

     public function adendumAgreements()
    {
        return $this->HasMany(AdendumAgreement::class, 'co_agreement_id');
    }

     
     public function product1()
    {
        return $this->belongsTo(Productdisc::class, 'product_id_1');
    }

     public function product2()
    {
        return $this->belongsTo(Productdisc::class, 'product_id_2');
    }

     public function product3()
    {
        return $this->belongsTo(Productdisc::class, 'product_id_3');
    }

     public function product4()
    {
        return $this->belongsTo(Productdisc::class, 'product_id_4');
    }

     public function product5()
    {
        return $this->belongsTo(Productdisc::class, 'product_id_5');
    }

     public function product6()
    {
        return $this->belongsTo(Productdisc::class, 'product_id_6');
    }

     public function product7()
    {
        return $this->belongsTo(Productdisc::class, 'product_id_7');
    }

   
}
