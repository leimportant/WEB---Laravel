<?php

namespace App\Http\Controllers\Group;

use App\Models\MaterialMasterForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;
use Mail;
use App\Models\Department;
use App\Models\Subdepartment;
use File;


class MaterialMasterFormController extends Controller
{

    public function index()
    {
     if (! Gate::allows('material-master-form.create')) {
            return abort(401);
        }
        $datas = MaterialMasterForm::Userlogin()->get();
        $waiting = DB::table('material_master_form')->select(DB::raw('count(id) as id'))
                        ->where('flag', ['1'])
                        ->get();

        return view('site.group.material-master-form.index', compact(['datas', 'waiting']));
    }

    public function admin()
    {
     if (! Gate::allows('material-master-form.admin')) {
            return abort(401);
        }
        $datas = MaterialMasterForm::Admin()->get();

        return view('site.group.material-master-form.admin', compact(['datas', 'waiting']));
    }

    public function create()
    {
        if (! Gate::allows('material-master-form.create')) {
            return abort(401);
        }

        return view('site.group.material-master-form.create');
    }

     public function store(Request $request)
    {
        if (! Gate::allows('material-master-form.create')) {
            return abort(401);
        }
        $this->validate($request, [
           'name_of_requester' => 'required', 
           'material_name' => 'required', 
        ]);

        $id = $this->generateKode_Urut();
        $date_requested = date('Y-m-d', strtotime($request->get('date_requested')));
        $division_requester = implode(",", $request->get('division_requester')); 
        $material_category = implode(",", $request->get('material_category')); 
        $flag = 1;


        $request->request->add(['id' => $id]);
        $request->request->add(['date_requested' => $date_requested]);
        $request->request->add(['division_requester' => $division_requester]);
        $request->request->add(['material_category' => $material_category]);
        $request->request->add(['flag' => $flag]);

        if ($data = MaterialMasterForm::create($request->all())) {
             $datas = ['id' => $data->id, 
                   'date_requested' =>$data->date_requested, 
                   'name_of_requester' => $data->name_of_requester,
                   'dept_id' => $data->dept_id,
                   'material_name' => $data->material_name,
                   ];

            Mail::send('site.group.material-master-form.mail', $datas, function($message) {
                     // $address = 'dcr@stanli.co.id';
                  $address = 'm.soleh@stanli.co.id'; // dcr@stanli.co.id
                  $name = 'portalBiz Email - <no-reply@stanli.co.id>';
                  $message->from('no-reply@stanli.co.id', $name = null);
                  $message->to($address);
                  $message->sender('no-reply@stanli.co.id', $name);
                  $message->subject('portalBiz Email - Material Master Form');
              });
          }

        Toast::success('Data Successfull', 'info');
        return redirect()->route('material-master-form.index');
    }

      public function view($id)
    {
      if (! Gate::allows('material-master-form.create')) {
            return abort(401);
        }
       
        $relations = [
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
            'subdept0' => Subdepartment::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];

        $data = MaterialMasterForm::findOrFail($id);

        $data['division_requester'] = explode(",", $data['division_requester']);
        $data['material_category'] = explode(",", $data['material_category']);

        return view('site.group.material-master-form.view', compact('data') + $relations);
    }

     public function vPdf($id)
    {
      if (! Gate::allows('material-master-form.create')) {
            return abort(401);
        }
       

        $html2pdf = base_path() . '\vendor\mpdf\mpdf\mpdf.php';
        File::requireOnce($html2pdf);
        $html2pdf = new \mPDF('utf-8','a4', 0, 'times', 
          15, //margin left
          15, // margin right
          10, // margin top
          5, //margin bottom 
          '', 12, 'P' );


        $data = MaterialMasterForm::findOrFail($id);

        $data['division_requester'] = explode(",", $data['division_requester']);
        $data['material_category'] = explode(",", $data['material_category']);

        $content = view('site.group.material-master-form.html2pdf', compact(['data']));

        $html2pdf->WriteHTML($content);
        $html2pdf->Output();
    }

     public function edit($id)
    {
      if (! Gate::allows('material-master-form.edit')) {
            return abort(401);
        }

        $relations = [
            'dept0' => Department::get()->pluck('name', 'id')->prepend('Please select', ''),
            'subdept0' => Subdepartment::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];

        $data = MaterialMasterForm::find($id);
        $data['division_requester'] = explode(",", $data['division_requester']);
        $data['material_category'] = explode(",", $data['material_category']);

        return view('site.group.material-master-form.edit', compact('data') + $relations);
    }

    public function update(Request $request, $id)
    {
        if (! Gate::allows('material-master-form.edit')) {
            return abort(401);
        }
        $date_requested = date('Y-m-d', strtotime($request->get('date_requested')));
        $sap_complete_date = date('Y-m-d', strtotime($request->get('sap_complete_date')));
        $sap_receive_date = date('Y-m-d', strtotime($request->get('sap_receive_date')));

        $request->request->add(['date_requested' => $date_requested]);
        $request->request->add(['sap_complete_date' => $sap_complete_date]);
        $request->request->add(['sap_receive_date' => $sap_receive_date]);

        $this->validate($request, [
           'sap_complete_date' => 'required', 
           'sap_receive_date' => 'required', 
           'sap_material_number' => 'required',
           'sap_material_name' => 'required'
        ]);

        $data = MaterialMasterForm::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('material-master-form.index');
    }

  public function revisi($id)
    {
      if (! Gate::allows('material-master-form.create')) {
            return abort(401);
        }

        $data = MaterialMasterForm::find($id);
        $data['division_requester'] = explode(",", $data['division_requester']);
        $data['material_category'] = explode(",", $data['material_category']);

        return view('site.group.material-master-form.revision', compact('data'));
    }

     public function gorevisi(Request $request, $id)
    {
        if (! Gate::allows('material-master-form.edit')) {
            return abort(401);
        }
       $this->validate($request, [
           'name_of_requester' => 'required', 
           'material_name' => 'required', 
        ]);

        $date_requested = date('Y-m-d', strtotime($request->get('date_requested')));
        $division_requester = implode(",", $request->get('division_requester')); 
        $material_category = implode(",", $request->get('material_category')); 
        $flag = 1;

        $request->request->add(['date_requested' => $date_requested]);
        $request->request->add(['division_requester' => $division_requester]);
        $request->request->add(['material_category' => $material_category]);
        $request->request->add(['flag' => $flag]);

        $data = MaterialMasterForm::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('material-master-form.index');
    }


    public function destroy($id)
    {
         if (! Gate::allows('material-master-form.destroy')) {
            return abort(401);
        }

        $data = MaterialMasterForm::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('MaterialMasterForm.index');
    }


     public function plant()
    {
    
       $data = DB::table('plant')->get();

       return json_encode($data);           
    }

      public function sloc()
    {
    
       $data = DB::table('storage_location')->get();

       return json_encode($data);           
    }

      public function uom()
    {
    
       $data = DB::table('m_unit')->get();

       return json_encode($data);           
    }

     public function generateKode_Urut() {
        $_d = date("ymd");

        $last_id = DB::table('material_master_form')
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



}
