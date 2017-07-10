<?php

namespace App\Http\Controllers\Group;

use App\Models\AssetMasterForm;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Auth;
use DB;
use Storage;
use Log;
use Toast;
use File;
use App\Models\Plant;
use App\Models\Department;
use App\Models\Subdepartment;
use App\Models\Unit;
use Mail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use PDF\Options;
use Carbon\Carbon;



class AssetMasterFormController extends Controller
{

    public function index()
    {
     if (! Gate::allows('asset-master-form.create')) {
            return abort(401);
        }
        $datas = AssetMasterForm::Userlogin()->get();
// <!-- '000' => 'Rejected', '100' => 'Draft', '101' => 'Waiting', '200' => 'Approved By Manager', '250' => 'Waiting Approved By Owner Director', '300' => 'Waiting Approved By Finance Director', '400' => 'Approved By Finance Director', '500' => 'Approved By Accounting', '600' => 'Closed' -->
        $waiting_manager = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->where('flag', ['101'])
                        ->where('manager_approved_by', Auth::user()->id)
                        ->get();
        $waiting_director = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->wherein('flag', ['250'])
                        ->get();
        $waiting_financedirector = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->wherein('flag', ['200', '300'])
                        ->get();
        $waiting_admin = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->where('flag', ['400'])
                        ->get();

        return view('site.group.asset-master-form.index', compact(['datas', 'waiting_manager', 'waiting_director', 'waiting_financedirector', 'waiting_admin']));
    }

    public function create()
    {
        if (! Gate::allows('asset-master-form.create')) {
            return abort(401);
        }

          $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];

        return view('site.group.asset-master-form.create', $relations);
    }

     public function store(Request $request)
    {
        if (! Gate::allows('asset-master-form.create')) {
            return abort(403, 'Unauthorized action.');
        }

       $this->validate($request, [
            'requester_id' => 'required|max:150',
            'requester_date' => 'required|date',
            'asset_owner' => 'required|max:15',
            'asset_name' => 'required|max:150',
            'asset_type' => 'required|max:15',
            'plant' => 'required|max:10',
            'qty' => 'required',
            'uom' => 'required|max:15',
            'location' => 'required|max:10',
            'asset_master_class' => 'required|max:20',
            'asset_category_id' => 'required|max:10',
            'cost_center_id' => 'required|max:10',
            'asset_purpose' => 'required|max:150',
            'photo' => 'required|max:10000',
        ]);

        $noId = $this->generateKode_Urut(); 
        
        $setname = $noId . '.' . $request->file('photo')->getClientOriginalExtension();
        $extensions  = $request->file('photo')->getClientOriginalExtension();
        if ($extensions !== 'pdf' ) {
            $path = '/files/asset_references/Image/';
            $is_pdf = 'N';
            $fileUpload = $noId . '.jpg';
            File::delete(base_path() .  $path . '' . $fileUpload);
        } else {
            $path = '/files/asset_references/PDF/';
            $is_pdf = 'Y';
            $fileUpload = $noId . '.pdf';
            File::delete(base_path() .  $path . '' . $fileUpload);
        }

        $asset_name = $request->input('asset_name') . ' ' . $request->input('asset_name_1') . ' ' . $request->input('asset_name_2');
        $requester_date = date('Y-m-d', strtotime($request->get('requester_date')));
        $requester = $request->input('requester_id');
      

        $gallery = new AssetMasterForm(array(
              'asset_id' => $noId,
              'requester_id'  => $requester,
              'requester_date'  => $requester_date,
              'asset_owner'  => $request->input('asset_owner'),
              'asset_name'  => strtoupper($asset_name),
              'asset_type'  => $request->input('asset_type'),
              'plant'  => $request->input('plant'),
              'qty'  => $request->input('qty'),
              'uom'  => $request->input('uom'),
              'price'  => NULL,
              'location'  => $request->input('location'),
              'asset_master_class'  => $request->input('asset_master_class'),
              'asset_category_id'  => $request->input('asset_category_id'),
              'asset_category_sub_id'  => $request->input('asset_category_sub_id'),
              'cost_center_id'  => $request->input('cost_center_id'),
              'asset_purpose'  => $request->input('asset_purpose'),
              'flag' => '100',
              'is_pdf' => $is_pdf,
              'photo' =>  $path . '' . $fileUpload,
              
            ));

       if($gallery->save() ) {
           $request->file('photo')->move(base_path() . $path , $fileUpload );
           Toast::success('Data Successfull', 'info');
       } else {
         Toast::error('Error Data Input', 'error');
       }

        return redirect()->route('asset-master-form.index');
    }

     public function edit($asset_id)
    {
      if (! Gate::allows('asset-master-form.edit')) {
            return abort(403, 'Unauthorized action.');
        }

        $data = AssetMasterForm::findOrFail($asset_id);
        
        return view('site.group.asset-master-form.edit', compact('data'));
    }

    public function update(Request $request, $asset_id)
    {
       if (! Gate::allows('asset-master-form.edit')) {
            return abort(401);
        }
        $data = AssetMasterForm::findOrFail($asset_id);

        $this->validate($request, [
            'requester_id' => 'required|max:150',
            'requester_date' => 'required|date',
            'asset_owner' => 'required|max:15',
            'asset_name' => 'required|max:150',
            'asset_type' => 'required|max:15',
            'plant' => 'required|max:10',
            'qty' => 'required',
            'uom' => 'required|max:15',
            'location' => 'required|max:10',
            'asset_master_class' => 'required|max:20',
            'asset_category_id' => 'required|max:10',
            'cost_center_id' => 'required|max:10',
            'asset_purpose' => 'required|max:150',
            'flag' => 'required|max:3',
            'photo' => 'max:10000',
        ]);

         $users = substr("$data->requester_id", 0, 6);

        $get_manager =  DB::table('users')
                      ->where('username', $users )
                      ->get();
        $get_manager[0]->email;
        $get_manager[0]->name;
        $get_manager[0]->id;

        if ($data->asset_owner == 'PRDDEPT' && $data->plant == 'ST02') {
                 $request->request->add(['manager_approved_by' => '248']);
            } else if ($data->asset_owner == 'PRDDEPT' && $data->plant == 'ST01') {
                 $request->request->add(['manager_approved_by' => '68']);
            } else {
                $data->manager_approved_by = $get_manager[0]->id;
         }
    

        if ($request->hasFile('photo')) {

           $extensions  = $request->file('photo')->getClientOriginalExtension();

            if ($extensions !== 'pdf' ) {
                $path = '/files/asset_references/Image/';
                $is_pdf = 'N';
                $fileUpload = $data->asset_id . '.jpg';
                $is_pdf = 'N';
                File::delete(base_path() .  $path . '' . $fileUpload);
              
                $request->file('photo')->move(base_path() . $path , $fileUpload );
                $request->request->add(['is_pdf' => $is_pdf]);
                
            } else {
                $path = '/files/asset_references/PDF/';
                $is_pdf = 'Y';
                $fileUpload = $data->asset_id . '.pdf';
                $is_pdf = 'Y';
                $request->request->add(['photo' =>  $path]);
                File::delete(base_path() .  $path . '' . $fileUpload);
               
                $request->file('photo')->move(base_path() . $path , $fileUpload );
                $request->request->add(['is_pdf' => $is_pdf]);
            }
           

        } else if ($data->is_pdf == 'N') { 
            $request->request->add(['photo' => '/files/asset_references/Image/' . $data->asset_id . '.jpg']);
        } else {
           $request->request->add(['photo' => '/files/asset_references/PDF/' . $data->asset_id . '.pdf']);
        }
     
       $request->request->add(['manager_approver_flag' => 'N']);
       $requester_date = date('Y-m-d', strtotime($request->get('requester_date')));
       $request->request->add(['requester_date' => $requester_date]);

       $data->update($request->all());
  
       if ($data->update() == true && $data->flag== 101) {

           if ($data->is_pdf == 'N') {
                 AssetMasterForm::find($asset_id)->update(['photo' => '/files/asset_references/Image/' . $data->asset_id . '.jpg']);
            } else {
                 AssetMasterForm::find($asset_id)->update(['photo' => '/files/asset_references/PDF/' . $data->asset_id . '.pdf']);
            }


          if ($data->photo !== NULL) {
                    $ref_asset = 'Available, Click Attach file below !';
          } else {
              $ref_asset = 'Not Available';
          }
           $datas = ['asset_id' => $data->asset_id, 
           'asset_type' =>$data->asset_type, 
           'asset_name' => $data->asset_name,
           'asset_purpose' => $data->asset_purpose,
           'get_name' =>  $get_manager[0]->name,
           ];

             // $cc1 = 'nurul.ulfah@stanli.co.id';
             // $cc2 = 'satriadi@stanli.co.id';

            Mail::send('site.group.asset-master-form.mail', $datas, function($message) use ($get_manager) {
   
                  $address = 'm.soleh@stanli.co.id'; // $get_manager[0]->email
                  $cc1 = 'no-reply@stanli.co.id';
                  $cc2 = 'm.soleh@stanli.co.id';
                  $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                  $message->from('no-reply@stanli.co.id', $name = null);
                  $message->to($address);
                  $message->sender('no-reply@stanli.co.id', $name);
                  $message->cc($cc1);
                  $message->cc($cc2);
                  $message->subject('portalBiz Email - Asset Master Form');
              });

       }

       Toast::success('Data Successfull', 'info');
       return redirect()->route('asset-master-form.index');
    }

  public function view($asset_id)
    {
      if (! Gate::allows('asset-master-form.create')) {
            return abort(403, 'Unauthorized action.');
        }

        $data = AssetMasterForm::findOrFail($asset_id);

        return view('site.group.asset-master-form.view', compact('data'));
    }

     public function viewPdf($asset_id)
    {
      if (! Gate::allows('asset-master-form.create')) {
            return abort(403, 'Unauthorized action.');
        }

        $data = AssetMasterForm::findOrFail($asset_id);

        return view('site.group.asset-master-form.viewpdf', compact('data'));
    }

    public function vPdf($asset_id)
    {
      if (! Gate::allows('asset-master-form.create')) {
            return abort(403, 'Unauthorized action.');
        }
        $relations = [
            'plant0' => Plant::get()->pluck('name', 'id')->prepend('Please select', ''),
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
            'subdept0' => SubDepartment::get()->pluck('name', 'id')->prepend('Please select', ''),

        ];
        $data = AssetMasterForm::findOrFail($asset_id);

        $html2pdf = base_path() . '\vendor\mpdf\mpdf\mpdf.php';
        File::requireOnce($html2pdf);
        $html2pdf = new \mPDF('utf-8','a4', 0, 'times', 
          15, //margin left
          15, // margin right
          10, // margin top
          5, //margin bottom 
          '', 12, 'P' );

        $content = view('site.group.asset-master-form.html2pdf', compact(['data']) + $relations);

        $html2pdf->WriteHTML($content);
        $html2pdf->Output();
    }

     public function printing(Request $request, $asset_id)
    {
      if (! Gate::allows('asset-master-form.create')) {
            return abort(403, 'Unauthorized action.');
        }
        $data = AssetMasterForm::findOrFail($asset_id);
        $date = date('Y-m-d');
        $filename = $data->asset_id . '.pdf';
        $cover =  \View::make('site.group.asset-master-form.html2pdf', compact('data', 'date', 'cover_agen'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $loadFull = $cover; 
        $pdf->loadHTML($loadFull)->setPaper('A4', 'portrait');
        $pdf->setOptions(['defaultFont' => 'times', "isPhpEnabled", true]);
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(0, 0, "Halaman {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $pdf->download($filename);

    }

    public function downloadFile($asset_id)
    {
      $data = AssetMasterForm::findOrFail($asset_id);
      $base_dir = base_path() .  $data->photo;

      return response()->download($base_dir);
    }


    public function destroy($asset_id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = AssetMasterForm::findOrFail($asset_id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('asset-master-form.index');
    }


     public function generateKode_Urut() {
        $_d = date("ym");

        $last_id = DB::table('asset_master_form')
                ->select(DB::raw('max(asset_id) as asset_id'))  
                ->where('asset_id', 'LIKE', '%' . $_d.'%')
                ->orderBy('asset_id', 'DESC')
                ->get();
        
    
        $noId = $last_id[0]->asset_id;
        $new_code = "001";
 
        if ($noId == null || $noId == '') {     
            $no = $_d . '' .  $new_code;
        } else {
            $sort_num = substr($noId, 4);
            $sort_num++;
            $new_code = sprintf("%03s", $sort_num);
            $no = $_d . '' .  $new_code;
        }   

        return $no;
    }


      public function requester()
    {
    
       $data = DB::table('users')->where('approver', 'Y')->get();

       return json_encode($data);           
    }

       public function assetType()
    {
    
       $data = DB::table('m_asset_type')->get();

       return json_encode($data);           
    }

        public function assetOwner()
    {
    
       $data = DB::table('m_asset_owner')->where('flag', 'Y')->get();

       return json_encode($data);           
    }

       public function getunit()
    {
    
       $data = DB::table('m_unit')->get();

       return json_encode($data);           
    }

       public function plantLocation()
    {
    
       $data = DB::table('m_asset_location')->select('plant_id')->groupBy('plant_id')->get();

       return json_encode($data);           
    }

       public function assetLocation()
    {
    
       $data = DB::table('m_asset_location')->get();

       return json_encode($data);           
    }

      public function getLocation(Request $request)
    {
       $plant = $request->input('plant');
    
       $data = DB::table('m_asset_location')->where('plant_id', $plant)->get();

       return json_encode($data);           
    }

      public function setLocation()
    {    
       $data = DB::table('m_asset_location')->get();

       return json_encode($data);           
    }


        public function getAssetMaster()
    {
    
       $data = DB::table('m_asset_master')->get();

       return json_encode($data);           
    }

      public function getAssetClass(Request $request)
    {
       $asset_master_class = $request->input('asset_master_class');
    
       $data = DB::table('m_asset_class')->where('asset_id', $asset_master_class)->get();

       return json_encode($data);           
    }

    public function getSubAssetClass(Request $request)
    {
       $asset_master_class = $request->input('asset_master_class');
    
       $data = DB::table('m_asset_class_sub')->where('asset_id', $asset_master_class)->get();

       return json_encode($data);           
    }

      public function setAssetClass()
    {
    
       $data = DB::table('m_asset_class')->get();

       return json_encode($data);           
    }

     public function setSubAssetClass()
    {    
       $data = DB::table('m_asset_class_sub')->get();

       return json_encode($data);           
    }

     public function getCostcenter()
    {
    
       $data = DB::table('m_cost_center')->get();

       return json_encode($data);           
    }

    // access manager

  public function manager()
    {
     if (! Gate::allows('asset-master-form.manager')) {
            return abort(401);
        }
        $datas = AssetMasterForm::manager()->get();

        $waiting = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->where('flag', ['101'])
                        ->where('manager_approved_by', Auth::user()->id)
                        ->get();

        return view('site.group.asset-master-form.manager', compact(['datas', 'waiting']));
    }

     public function appmanager($asset_id)
    {
      if (! Gate::allows('asset-master-form.manager')) {
            return abort(401);
        }

        $data = AssetMasterForm::findOrFail($asset_id);
        
        return view('site.group.asset-master-form.appmanager', compact('data'));
    }

    public function updatemanager(Request $request, $asset_id)
    {
       if (! Gate::allows('asset-master-form.manager')) {
            return abort(401);
        }
        $data = AssetMasterForm::findOrFail($asset_id);

        $this->validate($request, [
            'flag' => 'required|max:3',
            'price' => 'max:10000',
        ]);
       $users = substr("$data->requester_id", 0, 6);
        $get_manager =  DB::table('users')
                      ->where('username', $users )
                      ->get();
        $get_manager[0]->email;
        $get_manager[0]->name;
        $get_manager[0]->id;
     

         if ($data->asset_owner == 'PRDDEPT' && $data->plant == 'ST02') {
                 $request->request->add(['manager_approved_by' => '248']);
            } else if ($data->asset_owner == 'PRDDEPT' && $data->plant == 'ST01') {
                 $request->request->add(['manager_approved_by' => '68']);
            } else {
                $data->manager_approved_by = $get_manager[0]->id;
            }
      
       $manager_approved_at = date("ymd H:i:s");
       $vowel = array("Rp. ", ".");
       $price =  str_replace($vowel, '', $request->get('price'));
       $request->request->add(['manager_approved_at' => $manager_approved_at]);
       $request->request->add(['manager_approver_flag' => 'Y']);
       $request->request->add(['manager_approved_by' => Auth::user()->id]);
       $request->request->add(['price' => $price]);

       if ($request->get('flag') == '200' && $request->get('asset_owner') == '0005') {
            $request->request->add(['flag' => '250']);
        }


       $data->update($request->all());
  
       if ($data->update() == true && $data->flag== 200 && $data->asset_owner != '0005' ) {

          if ($data->photo !== NULL) {
               $ref_asset = 'Available, Click Attach file below !';
          } else {
              $ref_asset = 'Not Available';
          }

      $get_asset_owner =  DB::table('m_asset_owner')->where('owner_id', $data->asset_owner )->get();
      $get_asset_owner[0]->owner_name;

      $datas = ['asset_id' => $data->asset_id, 
                   'asset_type' =>$data->asset_type, 
                   'asset_name' => $data->asset_name,
                   'asset_purpose' => $data->asset_purpose,
                   'price' => $data->price,
                   'assetowner' => $get_asset_owner[0]->owner_name,
                   'get_name' =>  $get_manager[0]->name,
                   'ref_asset' =>  $ref_asset,
                   'photo' => $data->photo,
           ];

            Mail::send('site.group.asset-master-form.mail-attach', $datas, function($message) use ($data) {
                 
                  $address = 'm.soleh@stanli.co.id';  //scahyadi@stanli.co.id
                  $cc1 = 'no-reply@stanli.co.id';
                  $cc2 = 'm.soleh@stanli.co.id';
                  $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                
                  $message->from('no-reply@stanli.co.id', $name = null);
                  $message->to($address);
                  $message->sender('no-reply@stanli.co.id', $name);
                  $message->attach(base_path() . $data['photo']);
                  $message->subject('portalBiz Email - Asset Master Form');
              });

       } 

        if ($data->update() == true && $data->flag== 250 && $data->asset_owner == '0005' ) {

          if ($data->photo !== NULL) {
               $ref_asset = 'Available, Click Attach file below !';
          } else {
              $ref_asset = 'Not Available';
          }

      $get_asset_owner =  DB::table('m_asset_owner')->where('owner_id', $data->asset_owner )->get();
      $get_asset_owner[0]->owner_name;

      $datas = ['asset_id' => $data->asset_id, 
                   'asset_type' =>$data->asset_type, 
                   'asset_name' => $data->asset_name,
                   'asset_purpose' => $data->asset_purpose,
                   'price' => $data->price,
                   'assetowner' => $get_asset_owner[0]->owner_name,
                   'get_name' =>  $get_manager[0]->name,
                   'ref_asset' =>  $ref_asset,
                   'photo' => $data->photo,
           ];

            Mail::send('site.group.asset-master-form.mail-attach_2', $datas, function($message) use ($data) {
                 
                  $address = 'm.soleh@stanli.co.id';  //denny.cahyadi@stanli.co.id
                  $cc1 = 'no-reply@stanli.co.id';
                  $cc2 = 'm.soleh@stanli.co.id';
                  $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                
                  $message->from('no-reply@stanli.co.id', $name = null);
                  $message->to($address);
                  $message->sender('no-reply@stanli.co.id', $name);
                  $message->attach(base_path() . $data['photo']);
                  $message->subject('portalBiz Email - Asset Master Form');
              });

       } 

       Toast::success('Data Successfull', 'info');
       return redirect()->route('asset-master-form.manager');
    }

    // untuk finance director

  public function financedirector()
    {
     if (! Gate::allows('asset-master-form.financedirector')) {
            return abort(401);
        }
        $datas = AssetMasterForm::financeDirector()->get();

        $waiting_financedirector = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->wherein('flag', ['200', '300'])
                        ->get();


        return view('site.group.asset-master-form.financedirector', compact(['datas', 'waiting_financedirector']));
    }

     public function appfinancedirector($asset_id)
    {
      if (! Gate::allows('asset-master-form.financedirector')) {
            return abort(401);
        }

        $data = AssetMasterForm::findOrFail($asset_id);
        
        return view('site.group.asset-master-form.appfinancedirector', compact('data'));
    }

    public function updatefinancedirector(Request $request, $asset_id)
    {
       if (! Gate::allows('asset-master-form.financedirector')) {
            return abort(401);
        }
        $data = AssetMasterForm::findOrFail($asset_id);

        $this->validate($request, [
            'flag' => 'required|max:3',
        ]);

       $vowel = array("Rp. ", ".");
       $price =  str_replace($vowel, '', $request->get('price'));
       $request->request->add(['price' => $price]);
      

        if ($request->get('asset_owner') != 'SLSMKT') {
            $request->request->add(['director_approved_by' => Auth::user()->id]);
            $request->request->add(['director_approved_at' =>  date("ymd H:i:s")]);
        }

        $request->request->add(['finance_director_approved_by' => Auth::user()->id]);
        $request->request->add(['finance_director_approved_at' =>  date("ymd H:i:s")]);
        $users = substr("$data->requester_id", 0, 6);

        $get_manager =  DB::table('users')
                      ->where('username', $users )
                      ->get();
        $get_manager[0]->email;
        $get_manager[0]->name;
        $get_manager[0]->id;


       $data->update($request->all());
  
       if ($data->update() == true && $data->flag== 400) {

          if ($data->photo !== NULL) {
              $ref_asset = 'Available, Click Attach file below !';
          } else {
              $ref_asset = 'Not Available';
          }

        $get_asset_owner =  DB::table('m_asset_owner')
                      ->where('owner_id', $data->asset_owner )
                      ->get();
        $get_asset_owner[0]->owner_name;

        $datas = ['asset_id' => $data->asset_id, 
                   'asset_type' =>$data->asset_type, 
                   'asset_name' => $data->asset_name,
                   'asset_purpose' => $data->asset_purpose,
                   'price' => $data->price,
                   'assetowner' => $get_asset_owner[0]->owner_name,
                   'get_name' =>  $get_manager[0]->name,
                   'ref_asset' =>  $ref_asset,
                   'photo' => $data->photo,
           ];

       Mail::send('site.group.asset-master-form.mail-admin', $datas, function($message) use ($data) {
                  
                  $address = 'm.soleh@stanli.co.id';  //nurul.ulfah@stanli.co.id
                  $cc1 = 'no-reply@stanli.co.id'; //satriadi@stanli.co.id
                  $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                
                  $message->from('no-reply@stanli.co.id', $name = null);
                  $message->to($address);
                  $message->cc($cc1);
                  $message->sender('no-reply@stanli.co.id', $name);
                  $message->attach(base_path() . $data['photo']);
                  $message->subject('portalBiz Email - Asset Master Form');
              });

       } elseif ($data->update() == true && $data->flag== 000) {

          if ($data->photo !== NULL) {
              $ref_asset = 'Available, Click Attach file below !';
          } else {
              $ref_asset = 'Not Available';
          }

        $get_asset_owner =  DB::table('m_asset_owner')
                      ->where('owner_id', $data->asset_owner )
                      ->get();
        $get_asset_owner[0]->owner_name;

        $datas = ['asset_id' => $data->asset_id, 
                   'asset_type' =>$data->asset_type, 
                   'asset_name' => $data->asset_name,
                   'asset_purpose' => $data->asset_purpose,
                   'price' => $data->price,
                   'assetowner' => $get_asset_owner[0]->owner_name,
                   'get_name' =>  $get_manager[0]->name,
                   'ref_asset' =>  $ref_asset,
                   'photo' => $data->photo,
                   'remark' => $data->remark,
           ];

       Mail::send('site.group.asset-master-form.mail-reject', $datas, function($message) use ($get_manager) {
                 Log::info($get_manager[0]->email);
                  $address = 'm.soleh@stanli.co.id';  // $get_manager[0]->email;
                  $cc1 = 'no-reply@stanli.co.id';
                  $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                
                  $message->from('no-reply@stanli.co.id', $name = null);
                  $message->to($address);
                  $message->cc($cc1);
                  $message->sender('no-reply@stanli.co.id', $name);
                  $message->subject('portalBiz Email - Asset Master Form');
              });

       }


       Toast::success('Data Successfull', 'info');
       return redirect()->route('asset-master-form.financedirector');
    }


// untuk director

  public function director()
    {
     if (! Gate::allows('asset-master-form.director')) {
            return abort(401);
        }
        $datas = AssetMasterForm::Director()->get();

        $waiting_director = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->where('flag', ['250'])
                        ->get();


        return view('site.group.asset-master-form.director', compact(['datas', 'waiting_director']));
    }

     public function appdirector($asset_id)
    {
      if (! Gate::allows('asset-master-form.director')) {
            return abort(401);
        }

        $data = AssetMasterForm::findOrFail($asset_id);
        
        return view('site.group.asset-master-form.appdirector', compact('data'));
    }

    public function updatedirector(Request $request, $asset_id)
    {
       if (! Gate::allows('asset-master-form.director')) {
            return abort(401);
        }
        $data = AssetMasterForm::findOrFail($asset_id);

        $this->validate($request, [
            'flag' => 'required|max:3',
        ]);

       $vowel = array("Rp. ", ".");
       $price =  str_replace($vowel, '', $request->get('price'));
       $request->request->add(['price' => $price]);
      

        if ($request->get('asset_owner') != 'SLSMKT') {
            $request->request->add(['director_approved_by' => Auth::user()->id]);
            $request->request->add(['director_approved_at' =>  date("ymd H:i:s")]);
        }

        $request->request->add(['finance_director_approved_by' => Auth::user()->id]);
        $request->request->add(['finance_director_approved_at' =>  date("ymd H:i:s")]);

        $users = substr("$data->requester_id", 0, 6);

        $get_manager =  DB::table('users')
                      ->where('username', $users )
                      ->get();
        $get_manager[0]->email;
        $get_manager[0]->name;
        $get_manager[0]->id;


       $data->update($request->all());
  
       if ($data->update() == true && $data->flag== 300) {

          if ($data->photo !== NULL) {
              $ref_asset = 'Available, Click Attach file below !';
          } else {
              $ref_asset = 'Not Available';
          }

        $get_asset_owner =  DB::table('m_asset_owner')
                      ->where('owner_id', $data->asset_owner )
                      ->get();
        $get_asset_owner[0]->owner_name;

        $datas = ['asset_id' => $data->asset_id, 
                   'asset_type' =>$data->asset_type, 
                   'asset_name' => $data->asset_name,
                   'asset_purpose' => $data->asset_purpose,
                   'price' => $data->price,
                   'assetowner' => $get_asset_owner[0]->owner_name,
                   'get_name' =>  $get_manager[0]->name,
                   'ref_asset' =>  $ref_asset,
                   'photo' => $data->photo,
           ];

       Mail::send('site.group.asset-master-form.mail-attach_3', $datas, function($message) use ($data) {
                  
                  $address = 'm.soleh@stanli.co.id';  //scahyadi@stanli.co.id
                  $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                
                  $message->from('no-reply@stanli.co.id', $name = null);
                  $message->to($address);
                  $message->sender('no-reply@stanli.co.id', $name);
                  $message->attach(base_path() . $data['photo']);
                  $message->subject('portalBiz Email - Asset Master Form');
              });

       } elseif ($data->update() == true && $data->flag== 000) {

          if ($data->photo !== NULL) {
              $ref_asset = 'Available, Click Attach file below !';
          } else {
              $ref_asset = 'Not Available';
          }

        $get_asset_owner =  DB::table('m_asset_owner')
                      ->where('owner_id', $data->asset_owner )
                      ->get();
        $get_asset_owner[0]->owner_name;

        $datas = ['asset_id' => $data->asset_id, 
                   'asset_type' =>$data->asset_type, 
                   'asset_name' => $data->asset_name,
                   'asset_purpose' => $data->asset_purpose,
                   'price' => $data->price,
                   'assetowner' => $get_asset_owner[0]->owner_name,
                   'get_name' =>  $get_manager[0]->name,
                   'ref_asset' =>  $ref_asset,
                   'photo' => $data->photo,
                   'remark' => $data->remark,
           ];

       Mail::send('site.group.asset-master-form.mail-reject', $datas, function($message) use ($get_manager) {
                  $address = 'm.soleh@stanli.co.id';  // $get_manager[0]->email;
                  $cc1 = 'no-reply@stanli.co.id';
                  $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                
                  $message->from('no-reply@stanli.co.id', $name = null);
                  $message->to($address);
                  $message->cc($cc1);
                  $message->sender('no-reply@stanli.co.id', $name);
                  $message->subject('portalBiz Email - Asset Master Form');
              });

       }


       Toast::success('Data Successfull', 'info');
       return redirect()->route('asset-master-form.director');
    }


    public function admin()
    {
     if (! Gate::allows('asset-master-form.admin')) {
            return abort(401);
        }
        $datas = AssetMasterForm::Admin()->get();

        $waiting_admin = DB::table('asset_master_form')->select(DB::raw('count(asset_id) as asset_id'))
                        ->where('flag', ['400'])
                        ->get();


        return view('site.group.asset-master-form.admin', compact(['datas', 'waiting_admin']));
    }

     public function appadmin($asset_id)
    {
      if (! Gate::allows('asset-master-form.admin')) {
            return abort(401);
        }

        $data = AssetMasterForm::findOrFail($asset_id);
        
        return view('site.group.asset-master-form.appadmin', compact('data'));
    }

    public function updateadmin(Request $request, $asset_id)
    {
       if (! Gate::allows('asset-master-form.admin')) {
            return abort(401);
        }
        $data = AssetMasterForm::findOrFail($asset_id);

        $this->validate($request, [
            'asset_number' => 'required',
            'asset_desc' => 'required',
            'sap_asset_master' => 'required',
            'sap_asset_class' => 'required',
            'sap_asset_class_sub' => 'required',
        ]);
        $now = Carbon::now();
       $vowel = array("Rp. ", ".");
       $price =  str_replace($vowel, '', $request->get('price'));
       $request->request->add(['price' => $price]);
       $request->request->add(['flag' => '600']);
       $request->request->add(['accounting_approved_by' => Auth::user()->id]);
       $request->request->add(['accounting_approved_at' => $now]);
      

        $users = substr("$data->requester_id", 0, 6);

        $get_manager =  DB::table('users')
                      ->where('username', $users )
                      ->get();
        $get_manager[0]->email;
        $get_manager[0]->name;
        $get_manager[0]->id;


       $data->update($request->all());
  
       if ($data->update() == true) {

          if ($data->photo !== NULL) {
              $ref_asset = 'Available, Click Attach file below !';
          } else {
              $ref_asset = 'Not Available';
          }

        $get_asset_owner =  DB::table('m_asset_owner')
                      ->where('owner_id', $data->asset_owner )
                      ->get();
        $get_asset_owner[0]->owner_name;

        $datas = ['asset_id' => $data->asset_id, 
                   'asset_type' =>$data->asset_type, 
                   'asset_name' => $data->asset_name,
                   'asset_purpose' => $data->asset_purpose,
                   'price' => $data->price,
                   'assetowner' => $get_asset_owner[0]->owner_name,
                   'get_name' =>  $get_manager[0]->name,
                   'ref_asset' =>  $ref_asset,
                   'asset_number' => $data->asset_number,
           ];

       Mail::send('site.group.asset-master-form.mail-close', $datas, function($message) use ($get_manager) {
                  Log::info( $get_manager[0]->email);
                  $address = 'm.soleh@stanli.co.id';  // $get_manager[0]->email;
                  $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                
                  $message->from('no-reply@stanli.co.id', $name = null);
                  $message->to($address);
                  $message->sender('no-reply@stanli.co.id', $name);
                  $message->subject('portalBiz Email - Asset Master Form');
              });

       } 

       Toast::success('Data Successfull', 'info');
       return redirect()->route('asset-master-form.admin');
    }
}
