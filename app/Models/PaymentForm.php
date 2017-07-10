<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class PaymentForm extends Model
{
    protected $table = 'prf_form';

    protected $primaryKey = 'id_prf'; 

  	public $incrementing = false;

    protected $fillable = ['id_prf', 'payment_to', 'dept_id', 'sub_dept','no_prf','plant','due_settlement','due_payment','payment', 'claim','total_amount','bank_id','bank_office','rekening_no','rekening_name','is_processed','currency_id','remark','payment_method','Flag','approved_by','approved_at', 'grids'];

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

	public function scopeUserLogin($query)
    {
        return $query->where('created_by', Auth::user()->id);
    }

    public function scopeAdmin($query)
    {
        return $query->wherein('is_processed', ['N', 'P'])->wherein('flag', ['1']);
    }

		public function curr()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
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
        return $this->belongsTo(Plant::class, 'plant');
    }

       public function bank0()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
