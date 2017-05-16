<?php

namespace App\Http\Controllers\Master;

use App\Models\Officeagreement as OfficeAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;
use App\Models\Bank;

class OfficeagreementController extends Controller
{
	public function index()
    {
     if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $datas = OfficeAgreement::all();

        return view('site.master.office-agreement.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $relations = [
            'bank0' => Bank::get()->pluck('name_bank', 'name_bank')->prepend('Please select', ''),
        ];
        return view('site.master.office-agreement.create', $relations);
    }

     public function store(Request $request)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $this->validate($request, [
            'agreement_name' => 'required|max:50',
            'company_name' => 'required|max:50',
            'owner_name' => 'required|max:50',
            'office_level' => 'required|max:50',
            'email' => 'required|email|max:128',
            'address_office' => 'required',
            'telepon_no' => 'required|max:50',
            'bank' => 'required|max:50',
            'bank_branch' => 'required|max:50',
            'rekening_no' => 'required|max:50',
            'rekening_name' => 'required|max:50',
        ]);
        $garmelia_flag = $request->input('garmelia_flag');
        $padimas_flag = $request->input('padimas_flag');
        if ($garmelia_flag == null || $garmelia_flag == '') {
             $request->request->add(['garmelia_flag' => 'F']);
        }
         if ($padimas_flag == null || $padimas_flag == '') {
             $request->request->add(['padimas_flag' => 'F']);
        }
        $data = OfficeAgreement::create($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('office-agreement.index');
    }

     public function edit($id)
    {
      if (! Gate::allows('create-master')) {
            return abort(401);
        }
        $relations = [
            'bank0' => Bank::get()->pluck('name_bank', 'name_bank')->prepend('Please select', ''),
        ];
        $data = OfficeAgreement::findOrFail($id);

        return view('site.master.office-agreement.edit', compact('data') + $relations);
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('create-master')) {
            return abort(401);
        }

        $this->validate($request, [
            'agreement_name' => 'required|max:50',
            'company_name' => 'required|max:50',
            'owner_name' => 'required|max:50',
            'office_level' => 'required|max:50',
            'email' => 'required|email|max:128',
            'address_office' => 'required',
            'telepon_no' => 'required|max:50',
            'bank' => 'required|max:50',
            'bank_branch' => 'required|max:50',
            'rekening_no' => 'required|max:50',
            'rekening_name' => 'required|max:50',
        ]);
        $garmelia_flag = $request->input('garmelia_flag');
        $padimas_flag = $request->input('padimas_flag');
        if ($garmelia_flag == null || $garmelia_flag == '') {
             $request->request->add(['garmelia_flag' => 'F']);
        }
         if ($padimas_flag == null || $padimas_flag == '') {
             $request->request->add(['padimas_flag' => 'F']);
        }
        $data = OfficeAgreement::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('office-agreement.index');
    }


    public function destroy($id)
    {
        if (! Gate::allows('create-master')) {
            return abort(401);
        }     
        $data = OfficeAgreement::findOrFail($id);
        $data->delete();
        Toast::success('Delete Successfull', 'info');

        return redirect()->route('office-agreement.index');
    }


}
