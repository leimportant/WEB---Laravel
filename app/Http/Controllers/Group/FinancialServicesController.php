<?php

namespace App\Http\Controllers\Group;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FinancialServicesController extends Controller
{
	 public function __construct()
    {
        $this->middleware('auth');
    }
    
     public function index()
    {
            
        return view('bad');
    }
}
