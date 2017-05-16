<?php

namespace App\Http\Controllers\Group;

use App\Models\Padimasagreement as Padimasagreement;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\App;
use DB;
use Storage;
use Log;
use Toast;
use Auth;
use PDF;
use PDF\Options;
use Carbon\Carbon;
use App\Models\Bank;
use App\Models\Area;
use App\Models\Termpayment;
use App\Models\Officeagreement;
use App\Models\Productdisc;

class PadimasagreementController extends Controller
{
	public function index()
    {
     if (! Gate::allows('create-agreement')) {
            return abort(401);
        }
        $datas = DB::table('cooperation_agreement')->where('company_type', 'P-DIST')->get();
        $waiting = DB::table('cooperation_agreement')->select(DB::raw('count(doc_id) as doc_id'))
                        ->wherein('company_type', ['P-AGEN', 'P-DIST'])
                        ->wherein('flag', ['0', '3'])
                        ->get();

        return view('site.group.padimas-agreement.index', compact(['datas', 'waiting']));
    }

    public function create()
    {
        if (! Gate::allows('create-agreement')) {
            return abort(401);
        }
        $relations = [
            'bank0' => Bank::get()->pluck('name_bank', 'id')->prepend('Please select', ''),
        ];
        return view('site.group.padimas-agreement.create', $relations);
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-agreement')) {
            return abort(401);
        }
        $this->validate($request, [
            'company_type'=> 'required|max:10',
            'company_name'=> 'required|max:50',
            'owner_name'=> 'required|max:50',
            'office_level'=> 'required|max:50',
            'ktp_no'=> 'required|max:20',
            'email'=> 'required|email|max:128',
            'address'=> 'required',
            'telepon_no'=> 'required|max:50',
            'agreement_date_min'=> 'required|date',
            'agreement_date_max'=> 'required|date|after:agreement_date_min',
            'deposit_guarantee'=> 'required|max:20',
            'top_order'=> 'required|max:10', 
            'cash_discount' => 'required',    
            'bank_id'=> 'required',
            'bank_branch'=> 'required|max:50',
            'rekening_no'=> 'required|max:50',
            'rekening_name'=> 'required|max:50',
            'territory'=> 'required|max:100',
            'territory_north'=> 'required|max:100',
            'territory_south'=> 'required|max:100',
            'territory_west'=> 'required|max:100',
            'territory_east'=> 'required|max:100',

        ]);

        $vowel = array("Rp. ", ".");
        $deposit_guarantee =  str_replace($vowel, '', $request->input('deposit_guarantee'));
        $company = $request->input('company_type');
        $docId = $this->generateKode_Urut();
        $contractId = $this->generateKode_Contract($company);
        $request->request->add(['doc_id' => $docId, 'contract_id' => $contractId,  'agreement_id' => '5', 'flag' => '0', 'deposit_guarantee' => $deposit_guarantee ]);
        $data = Padimasagreement::create($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('padimas-agreement.index');
    }

    public function edit($doc_id)
    {
      if (! Gate::allows('create-agreement')) {
            return abort(401);
        }
        $relations = [
            'bank0' => Bank::get()->pluck('name_bank', 'id')->prepend('Please select', ''),
            'area0' => Area::get()->pluck('area', 'area')->prepend('Please select', ''),
            'prod0' => Productdisc::get()->pluck('disc', 'id')->prepend('Please select', ''),
        ];

        $data = Padimasagreement::findOrFail($doc_id);
       


        return view('site.group.padimas-agreement.edit', compact('data') + $relations);
    }

    public function view($doc_id)
    {
      if (! Gate::allows('create-agreement')) {
            return abort(401);
        }
        $relations = [
            'bank0' => Bank::get()->pluck('name_bank', 'id')->prepend('Please select', ''),
            'top0' => Termpayment::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];
        $data = Padimasagreement::findOrFail($doc_id);

        return view('site.group.padimas-agreement.view', compact('data') + $relations);
    }

    public function show()
    {
     if (! Gate::allows('create-agreement')) {
            return abort(401);
        }
        $datas = Padimasagreement::flag()->get();
        $waiting = DB::table('cooperation_agreement')->select(DB::raw('count(doc_id) as doc_id'))
                        ->wherein('company_type', ['P-AGEN', 'P-DIST'])
                        ->wherein('flag', ['0', '3'])
                        ->get();
        return view('site.group.padimas-agreement.admin', compact(['datas', 'waiting']));
    }

    public function update(Request $request, $doc_id)
    {
       if (! Gate::allows('create-agreement')) {
            return abort(401);
        }

          $this->validate($request, [
            'company_type'=> 'required|max:10',
            'company_name'=> 'required|max:50',
            'owner_name'=> 'required|max:50',
            'office_level'=> 'required|max:50',
            'ktp_no'=> 'required|max:20',
            'email'=> 'required|email|max:128',
            'address'=> 'required',
            'telepon_no'=> 'required|max:50',
            'agreement_date_min'=> 'required|date',
            'agreement_date_max'=> 'required|date|after:agreement_date_min',
            'deposit_guarantee'=> 'required|max:20',
            'top_order'=> 'required|max:10',     
            'bank_id'=> 'required',
            'bank_branch'=> 'required|max:50',
            'rekening_no'=> 'required|max:50',
            'rekening_name'=> 'required|max:50',
            'territory'=> 'required|max:100',
            'territory_north'=> 'required|max:100',
            'territory_south'=> 'required|max:100',
            'territory_west'=> 'required|max:100',
            'territory_east'=> 'required|max:100',

        ]);
        $vowel = array("Rp. ", ".");
        $deposit_guarantee =  str_replace($vowel, '', $request->input('deposit_guarantee'));

        $request->request->add(['agreement_id' => '4', 'flag' => '0', 'deposit_guarantee' => $deposit_guarantee]);
        $data = Padimasagreement::findOrFail($doc_id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('padimas-agreement.index');
    }

    public function approve($doc_id)
    {
        // if (! Gate::allows('garmelia-approver')) {
        //     return abort(401);
        // }
        $data = Padimasagreement::findOrFail($doc_id);
        $relations = [
            'bank0' => Bank::get()->pluck('name_bank', 'id')->prepend('Please select', ''),
            'office0' =>  DB::table('office_agreement')->where('padimas_flag', 'Y')->pluck('agreement_name', 'id')    
                        ->prepend('Please select', ''),
             'area0' => Area::get()->pluck('area', 'area')->prepend('Please select', ''),
           
        ];
       
         return view('site.group.padimas-agreement.approve', compact('data') + $relations);
    }

      public function goupdate(Request $request, $doc_id)
    {
       if (! Gate::allows('create-agreement')) {
            return abort(401);
        }

          $this->validate($request, [
            'company_name'=> 'required|max:50',
            'owner_name'=> 'required|max:50',
            'office_level'=> 'required|max:50',
            'ktp_no'=> 'required|max:20',
            'email'=> 'required|email|max:128',
            'address'=> 'required',
            'telepon_no'=> 'required|max:50',
            'agreement_date_min'=> 'required|date',
            'agreement_date_max'=> 'required|date|after:agreement_date_min',
            'deposit_guarantee'=> 'required|max:20',   
            'bank_branch'=> 'required|max:50',
            'rekening_no'=> 'required|max:50',
            'rekening_name'=> 'required|max:50',
            'territory'=> 'required|max:100',
            'territory_north'=> 'required|max:100',
            'territory_south'=> 'required|max:100',
            'territory_west'=> 'required|max:100',
            'territory_east'=> 'required|max:100',
            'flag' => 'required',
            'agreement_id' => 'required'

        ]);
        $now = Carbon::now();
        $approver = Auth::user()->id;
        $vowel = array("Rp. ", ".");
        $deposit_guarantee =  str_replace($vowel, '', $request->input('deposit_guarantee'));

        $request->request->add(['approved_at' => $now, 'approved_by' => $approver, 'deposit_guarantee' => $deposit_guarantee]);
        
        $data = Padimasagreement::findOrFail($doc_id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('padimas-agreement.index');
    }

    public function viewpdf($doc_id)
    {
      if (! Gate::allows('create-agreement')) {
            return abort(401);
        }
        $relations = [
            'bank0' => Bank::get()->pluck('name_bank', 'id')->prepend('Please select', ''),
            'top0' => Termpayment::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];
        $data = Padimasagreement::findOrFail($doc_id);

        $date = date('Y-m-d');
        $filename = $data->contract_id . '.pdf';
        $view =  \View::make('site.group.padimas-agreement.cover_agen', compact('data', 'date', 'cover_agen'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('A4', 'portrait');
        $pdf->setOptions(['defaultFont' => 'times']);
        return $pdf->download($filename);
    }

    public function destroy($doc_id)
    {
        if (! Gate::allows('create-agreement')) {
            return abort(401);
        }     
        $data = Padimasagreement::findOrFail($doc_id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('padimas-agreement.index');
    }

     public function generateKode_Urut() {
        $_d = date("ymd");

        $last_id = DB::table('cooperation_agreement')
                ->select(DB::raw('max(doc_id) as doc_id'))  
                ->where('doc_id', 'LIKE', '%' . $_d.'%')
                ->orderBy('doc_id', 'DESC')
                ->get();
        
    
        $noId = $last_id[0]->doc_id;
        $new_code = "0001";
 
        if ($noId == null || $noId == '') {     
            $no = $_d . '' .  $new_code;
        } else {
            $sort_num = substr($noId, 6);
            $sort_num++;
            $new_code = sprintf("%04s", $sort_num);
            $no = $_d . '' .  $new_code;
        }   

        return $no;
    }

      public function findArea()
    {
    
       $data = DB::table('area')->get();

       return json_encode($data);       
                          
    }
      public function findproduct(Request $request)
    {

       $area = $request->input('area');
    
       $data = DB::table('product')->where('area', $area)->get();

       return json_encode($data);       
                          
    }
   
       public function getproduct()
    {
    
       $data = DB::table('product')->get();

       return json_encode($data);       
                          
    }

    public function generateKode_Contract($company) {
        // format     ex: 001/P-DIST/2016
        $_d = date("Y");
        $_right = '/' . $company . '/' . $_d;
        $_first = "001";
        $no = $_first . '' .  $_right;

        $last_id = DB::table('cooperation_agreement')
                ->select(DB::raw('max(contract_id) as contract_id'))  
                ->where('contract_id', 'LIKE', '%' . $_right.'%')
                ->orderBy('contract_id', 'DESC')
                ->get();

        $noId = $last_id[0]->contract_id;
        $new_code = "001";

        if ($noId == null || $noId == '') {     
            $no = $new_code . '' .  $_right;
        } else {
            $sort_num = substr($noId, 0, -12);
            $sort_num++;
            $new_code = sprintf("%03s", $sort_num);
            $no = $new_code . '' .  $_right;
        }   

        return $no;
    }
}
