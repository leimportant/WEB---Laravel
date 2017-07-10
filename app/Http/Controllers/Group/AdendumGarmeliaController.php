<?php

namespace App\Http\Controllers\Group;

use App\Models\AdendumGarmelia;
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
use App\Models\Officeagreement;
use App\Models\Garmeliaagreement as GarmeliaAgreement;
use File;

class AdendumGarmeliaController extends Controller
{
	public function index()
    {
     if (! Gate::allows('adendum-garmelia.create')) {
            return abort(401);
        }
        $datas = DB::table('adendum_agreement')->wherein('adendum_type', ['G-DIST', 'G-AGEN'])->get();
        $waiting = DB::table('adendum_agreement')->select(DB::raw('count(id) as id'))
                        ->wherein('adendum_type', ['G-DIST', 'G-AGEN'])
                        ->wherein('flag', ['0', '3'])
                        ->get();

        return view('site.group.adendum-garmelia.index', compact(['datas', 'waiting']));
    }

    public function create()
    {
         if (! Gate::allows('adendum-garmelia.create')) {
            return abort(401);
        }
        $relations = [
             'agreementList0' =>  DB::table('cooperation_agreement')->wherein('company_type',  ['G-DIST', 'G-AGEN'])->pluck('contract_id', 'doc_id')    
                       ->prepend('Please select', ''),   
        ];

        return view('site.group.adendum-garmelia.create', $relations);
    }

     public function store(Request $request)
    {
         if (! Gate::allows('adendum-garmelia.create')) {
            return abort(401);
        }
         $this->validate($request, [
            'co_agreement_id'=> 'required',
            'adendum_date'=> 'required|date',
            'owner_name'=> 'required',
            'office_level'=> 'required',
            'ktp_no'=> 'required',
            'address'=> 'required',
            'first_provision'=> 'required',
            'change_provision'=> 'required',
            'provision_no'=> 'required|numeric',
            'provision_title'=> 'required',
            'provision_value'=> 'required',
            'provision_no_changed'=> 'required|numeric',
            'provision_title_changed'=> 'required',
            'provision_value_changed'=> 'required',

        ]);
       
        $id = $this->generateKode_Urut();    

        $findContract =DB::table('cooperation_agreement')
                ->select('company_type', 'agreement_id')  
                ->where('doc_id', $request->input('co_agreement_id'))
                ->get();

        $add_type = $findContract[0]->company_type;
        $add_aggree = $findContract[0]->agreement_id;

        $adendum_date = date('Y-m-d', strtotime($request->input('adendum_date')));
        $adendum = $this->generateKode_Adendum($add_type);


        $request->request->add(['adendum_date' => $adendum_date]);
        $request->request->add(['agreement_office_id' => $add_aggree]);
        $request->request->add(['adendum_type' => $add_type]);
        $request->request->add(['id' => $id, 'flag' => '0', 'adendum_id' => $adendum ]);

        $data = AdendumGarmelia::create($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('adendum-garmelia.index');
    }

    public function edit($id)
    {
        if (! Gate::allows('adendum-garmelia.create')) {
            return abort(401);
        }

         $relations = [
             'agreementList0' =>  DB::table('cooperation_agreement')->wherein('company_type',  ['G-DIST', 'G-AGEN'])->pluck('contract_id', 'doc_id')    
                       ->prepend('Please select', ''),   
        ];
        
        $data = AdendumGarmelia::findOrFail($id);

        return view('site.group.adendum-garmelia.edit', compact('data') + $relations);
    }

    public function view($id)
    {
     if (! Gate::allows('adendum-garmelia.create')) {
            return abort(401);
        }

        $data = AdendumGarmelia::findOrFail($id);

        return view('site.group.adendum-garmelia.view', compact('data'));
    }

     public function vPdfagen($id)
    {
       if (! Gate::allows('adendum-garmelia.create')) {
            return abort(401);
        }
       
        $data = AdendumGarmelia::findOrFail($id);

        $html2pdf = base_path() . '\vendor\mpdf\mpdf\mpdf.php';
        File::requireOnce($html2pdf);
        $html2pdf = new \mPDF('utf-8','a4', 0, 'times', 
          25, //margin left
          25, // margin right
          20, // margin top
          20, //margin bottom 
          '', 12, 'P' );

        $content = view('site.group.adendum-garmelia.html2pdf_agen', compact(['data']));

        $html2pdf->WriteHTML($content);
        $html2pdf->Output();
    }

      public function vPdfdistributor($id)
    {
       if (! Gate::allows('adendum-garmelia.create')) {
            return abort(401);
        }
       
        $data = AdendumGarmelia::findOrFail($id);

        $html2pdf = base_path() . '\vendor\mpdf\mpdf\mpdf.php';
        File::requireOnce($html2pdf);
        $html2pdf = new \mPDF('utf-8','a4', 0, 'times', 
          25, //margin left
          25, // margin right
          20, // margin top
          20, //margin bottom 
          '', 12, 'P' );

        $content = view('site.group.adendum-garmelia.html2pdf_dist', compact(['data']));

        $html2pdf->WriteHTML($content);
        $html2pdf->Output();
    }

    public function show()
    {
     if (! Gate::allows('adendum-garmelia.create')) {
            return abort(401);
        }
         $datas = DB::table('adendum_agreement')->wherein('adendum_type', ['G-DIST', 'G-AGEN'])->get();
        $waiting = DB::table('adendum_agreement')->select(DB::raw('count(id) as id'))
                        ->wherein('adendum_type', ['G-DIST', 'G-AGEN'])
                        ->wherein('flag', ['0', '3'])
                        ->get();

        return view('site.group.adendum-garmelia.admin', compact(['datas', 'waiting']));
    }

    public function update(Request $request, $id)
    {
        if (! Gate::allows('adendum-garmelia.create')) {
            return abort(401);
        }
           $this->validate($request, [
            'co_agreement_id'=> 'required',
            'adendum_date'=> 'required|date',
            'owner_name'=> 'required',
            'office_level'=> 'required',
            'ktp_no'=> 'required',
            'address'=> 'required',
            'first_provision'=> 'required',
            'change_provision'=> 'required',
            'provision_no'=> 'required|numeric',
            'provision_title'=> 'required',
            'provision_value'=> 'required',
            'provision_no_changed'=> 'required|numeric',
            'provision_title_changed'=> 'required',
            'provision_value_changed'=> 'required',

        ]);
        
        $request->request->add(['flag' => '0' ]);
        $data = AdendumGarmelia::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('adendum-garmelia.index');
    }

    public function approve($id)
    {
       if (! Gate::allows('adendum-garmelia.admin')) {
            return abort(401);
        }
        $data = AdendumGarmelia::findOrFail($id);
         $relations = [
             'agreementList0' =>  DB::table('cooperation_agreement')->wherein('company_type',  ['G-DIST', 'P-DIST'])->pluck('contract_id', 'doc_id')    
                       ->prepend('Please select', ''),   
        ];
       
         return view('site.group.adendum-garmelia.approve', compact('data') + $relations);
    }

      public function goupdate(Request $request, $id)
    {
       if (! Gate::allows('adendum-garmelia.admin')) {
            return abort(401);
        }

         $this->validate($request, [
            'flag'=> 'required',
         ]);

        $now = Carbon::now();
        $approver = Auth::user()->id;
        $request->request->add(['approved_at' => $now, 'approved_by' => $approver]);
        
        $data = AdendumGarmelia::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('adendum-garmelia.index');
    }

    public function viewpdf($id)
    {
      if (! Gate::allows('adendum-garmelia.admin')) {
            return abort(401);
        }
  
        $data = AdendumGarmelia::findOrFail($id);

        $date = date('Y-m-d');
        $filename = $data->contract_id . '.pdf';
        $cover =  \View::make('site.group.adendum-garmelia.cover_agen', compact('data', 'date', 'cover_agen'))->render();
        $content =  \View::make('site.group.adendum-garmelia.agen_full', compact('data', 'agen_full'))->render();
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

    public function destroy($id)
    {
        if (! Gate::allows('adendum-garmelia.create')) {
            return abort(401);
        }     
        $data = AdendumGarmelia::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('garmelia-agreement.index');
    }

     public function generateKode_Urut() {
        $_d = date("ymd");

        $last_id = DB::table('adendum_agreement')
                ->select(DB::raw('max(id) as id'))  
                ->where('id', 'LIKE', '%' . $_d.'%')
                ->orderBy('id', 'DESC')
                ->get();
        
    
        $noId = $last_id[0]->id;
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

     public function generateKode_Adendum($add_type) {
   //    format     ex: ADD/001/G-DIST/2016
        $_d = date("Y");
        $_right = '/' . $add_type . '/' . $_d;
        $_first = "001";
        $no = $_first . '' .  $_right;

        $last_id = DB::table('adendum_agreement')
                ->select(DB::raw('max(adendum_id) as adendum_id'))  
                ->where('adendum_id', 'LIKE', '%' . $_right.'%')
                ->orderBy('adendum_id', 'DESC')
                ->get();

        $noId = $last_id[0]->adendum_id;
        $new_code = "ADD/001";

        if ($noId == null || $noId == '') {     
            $no =  $new_code . '' .  $_right;
        } else {
            $sort_num = substr($noId, 0, -12);
            $sort_num++;
            $new_code = sprintf("%07s", $sort_num);
            $no =   $new_code . '' .  $_right;
        }   

        return $no;
    }

     // public function generateKode_Adendum($co_agreement_id) {
     // }
}
