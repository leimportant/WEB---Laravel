<?php

namespace App\Http\Controllers\Master;

use App\Models\Formgallery;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use DB;
use Validator;
use Storage;
use Log;
use Toast;
use File;

class FormgalleryController extends Controller
{
	public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = Formgallery::all();

        return view('site.master.form-gallery.index', compact('datas'));
    }

    public function show()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = Formgallery::all();

        return view('site.master.form-gallery.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        return view('site.master.form-gallery.create');
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $setFolder = $request->input('setFolder');
        $noId = $this->generateKode_Urut(); 

        if ($setFolder == "IAMR") {
            $path = '/files/policyandprocedureServices/gallery/IAMR/';
        }else if ($setFolder == "general") {
            $path = '/files/policyandprocedureServices/gallery/General/';
        } else {
            $path = '/files/policyandprocedureServices/gallery/';
        }
        $setname = $noId . '.' .$request->file('filename')->getClientOriginalExtension();
        $gallery = new Formgallery(array(
              'id' => $noId,
              'title'  => $request->get('title'),
              'filename' =>  $path . '' . $setname,
              'extensions'  => $request->file('filename')->getClientOriginalExtension()
            ));

        $gallery->save();

        $fileUpload = $gallery->id . '.' .$request->file('filename')->getClientOriginalExtension();
        $request->file('filename')->move(base_path() . $path , $fileUpload );

        Toast::success('Data Successfull', 'info');
        return redirect()->route('form-gallery.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
       
        $data = Formgallery::findOrFail($id);

        return view('site.master.form-gallery.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $data = Formgallery::findOrFail($id);
        $setFolder = $request->input('setFolder');

        if ($setFolder == "IAMR") {
            $path = '/files/policyandprocedureServices/gallery/IAMR/';
        }else if ($setFolder == "general") {
            $path = '/files/policyandprocedureServices/gallery/General/';
        } else {
            $path = '/files/policyandprocedureServices/gallery/';
        }

        $setname = $request->get('id') . '.' .$request->file('filename')->getClientOriginalExtension();
        $data->update($request->all());

        $fileUpload = $request->get('id') . '.' .$request->file('filename')->getClientOriginalExtension();
        $request->file('filename')->move(base_path() . $path , $fileUpload );

    
        Toast::success('Data Successfull', 'info');
        return redirect()->route('form-gallery.index');
    }


    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }     

        $data = Formgallery::findOrFail($id);

        File::delete(base_path() . $data->filename);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('form-gallery.index');
    }

 
    public function generateKode_Urut() {
        $_d = date("ymd");

        $last_id = DB::table('form_gallery')
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
