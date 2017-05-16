<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentForm extends Model
{
    protected $table = 'prf_form';

    protected $fillable = ['id_prf', 'payment_to', 'id_dept', 'sub_dept','no_prf','plant','due_settlement','due_payment','claim','total_amount','payment','bank_id','bank_office','rekening_no','rekening_name','is_processed','currency_id','remark','payment_method','Flag','approved_by','approved_at', 'descriptions', 'amount'];

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
