<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class AdendumGarmelia extends Model
{
    protected $table = 'adendum_agreement';
	
	protected $primaryKey = 'id'; 

    public $incrementing = false;

    protected $fillable = ['id','adendum_id','co_agreement_id','agreement_office_id','adendum_date','adendum_type','owner_name','office_level','ktp_no','address','first_provision','change_provision','provision_no','provision_title','provision_value','provision_no_changed','provision_title_changed','provision_value_changed','flag','created_by','updated_by','approved_by','created_at','updated_at'];

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

	public function coAgreement()
    {
        return $this->belongsTo(Garmeliaagreement::class, 'co_agreement_id');
    }

    public function agreementOffice()
    {
        return $this->belongsTo(OfficeAgreement::class, 'agreement_office_id');
    }
   // 'coAgreement' => array(self::BELONGS_TO, 'CooperationAgreement', 'co_agreement_id'),
   //          'agreementOffice' => array(self::BELONGS_TO, 'OfficeAgreement', 'agreement_office_id'),

}
