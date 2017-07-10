<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentFormDetail extends Model
{
    protected $table = 'prf_form_detail';

    protected $fillable = ['id', 'id_prf', 'descriptions', 'amount'];

    public $timestamps = false;
}
