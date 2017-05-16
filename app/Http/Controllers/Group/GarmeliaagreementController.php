<?php

namespace App\Http\Controllers\Group;

use App\Models\Garmeliaagreement as GarmeliaAgreement;
use Illuminate\Http\Request;
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
use App\Models\Termpayment;
use App\Models\Officeagreement;

class GarmeliaagreementController extends Controller
{
	public function index()
    {
     if (! Gate::allows('create-agreement')) {
            return abort(401);
        }
        $datas = DB::table('cooperation_agreement')->wherein('company_type', ['G-AGEN', 'G-DIST'])->get();
        $waiting = DB::table('cooperation_agreement')->select(DB::raw('count(doc_id) as doc_id'))
                        ->wherein('company_type', ['G-AGEN', 'G-DIST'])
                        ->wherein('flag', ['0', '3'])
                        ->get();

        return view('site.group.garmelia-agreement.index', compact(['datas', 'waiting']));
    }

    public function create()
    {
        if (! Gate::allows('create-agreement')) {
            return abort(401);
        }
        $relations = [
            'bank0' => Bank::get()->pluck('name_bank', 'id')->prepend('', ''),
        ];
        return view('site.group.garmelia-agreement.create', $relations);
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
        $company = $request->input('company_type');
        $docId = $this->generateKode_Urut();
        $contractId = $this->generateKode_Contract($company);
        $request->request->add(['doc_id' => $docId, 'contract_id' => $contractId,  'agreement_id' => '4', 'flag' => '0' ]);
        $data = GarmeliaAgreement::create($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('garmelia-agreement.index');
    }

    public function edit($doc_id)
    {
      if (! Gate::allows('create-agreement')) {
            return abort(401);
        }
        $relations = [
            'bank0' => Bank::get()->pluck('name_bank', 'id')->prepend('Please select', ''),
        ];
        $data = GarmeliaAgreement::findOrFail($doc_id);

        return view('site.group.garmelia-agreement.edit', compact('data') + $relations);
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
        $data = GarmeliaAgreement::findOrFail($doc_id);

        return view('site.group.garmelia-agreement.view', compact('data') + $relations);
    }

    public function show()
    {
     if (! Gate::allows('create-agreement')) {
            return abort(401);
        }
        $datas = GarmeliaAgreement::flag()->get();
        $waiting = DB::table('cooperation_agreement')->select(DB::raw('count(doc_id) as doc_id'))
                        ->wherein('company_type', ['G-AGEN', 'G-DIST'])
                        ->wherein('flag', ['0', '3'])
                        ->get();
        return view('site.group.garmelia-agreement.admin', compact(['datas', 'waiting']));
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
        
        $request->request->add(['agreement_id' => '4', 'flag' => '0' ]);
        $data = GarmeliaAgreement::findOrFail($doc_id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('garmelia-agreement.index');
    }

    public function approve($doc_id)
    {
        // if (! Gate::allows('garmelia-approver')) {
        //     return abort(401);
        // }
        $data = GarmeliaAgreement::findOrFail($doc_id);
        $relations = [
            'bank0' => Bank::get()->pluck('name_bank', 'id')->prepend('Please select', ''),
            'office0' =>  DB::table('office_agreement')->where('garmelia_flag', 'Y')->pluck('agreement_name', 'id')    
                        ->prepend('Please select', ''),
           
        ];
       
         return view('site.group.garmelia-agreement.approve', compact('data') + $relations);
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
        $request->request->add(['approved_at' => $now, 'approved_by' => $approver]);
        
        $data = GarmeliaAgreement::findOrFail($doc_id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('garmelia-agreement.index');
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
        $data = GarmeliaAgreement::findOrFail($doc_id);

        $date = date('Y-m-d');
        $filename = $data->contract_id . '.pdf';
        $cover =  \View::make('site.group.garmelia-agreement.cover_agen', compact('data', 'date', 'cover_agen'))->render();
        $content =  \View::make('site.group.garmelia-agreement.agen_full', compact('data', 'agen_full'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $loadFull = $cover . '' . $content; 
        // $pdf->loadHTML($cover)->setPaper('A4', 'portrait');
        $pdf->loadHTML($loadFull)->setPaper('A4', 'portrait');
        $pdf->setOptions(['defaultFont' => 'times', "isPhpEnabled", true]);
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(0, 0, "Halaman {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->download($filename);
    }

    public function destroy($doc_id)
    {
        if (! Gate::allows('create-agreement')) {
            return abort(401);
        }     
        $data = GarmeliaAgreement::findOrFail($doc_id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('garmelia-agreement.index');
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

     public function generateKode_Contract($company) {
        // format     ex: 001/G-DIST/2016
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
