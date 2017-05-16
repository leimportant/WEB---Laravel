<?php

namespace App\Http\Controllers\Master;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;


class BankController extends Controller
{

    public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = Bank::all();

        return view('site.master.bank.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }

        return view('site.master.bank.create');
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'name_bank' => 'required|max:50',
            'code_bank' => 'max:10',
        ]);

        $data = Bank::create($request->all());
         Toast::success('Data Successfull', 'info');
         return redirect()->route('bank.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
       
        $data = Bank::findOrFail($id);

        return view('site.master.bank.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Bank::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('bank.index');
    }



    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $data = Bank::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');
        return redirect()->route('bank.index');
    }



}
