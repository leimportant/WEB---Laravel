<?php

namespace App\Http\Controllers\Master;

use App\Models\Faq as FAQ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;

class FaqController extends Controller
{
	public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = FAQ::all();

        return view('site.master.faq.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        return view('site.master.faq.create');
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'question' => 'required|max:160',
            'answer' => 'required',
        ]);
        $modul = '1'; 
        $request->request->add(['modul' => $modul]);
        $data = FAQ::create($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('faq.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
       
        $data = FAQ::findOrFail($id);

        return view('site.master.faq.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'question' => 'required|max:160',
            'answer' => 'required',
        ]);
        $modul = '1'; 
        $request->request->add(['modul' => $modul]);
        $data = FAQ::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('faq.index');
    }


    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }     
        $data = FAQ::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('faq.index');
    }


}
