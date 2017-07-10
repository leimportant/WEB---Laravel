<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use App\Models\SubMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;
use App\Http\Controllers\Controller;
use Storage;
use Log;
use Toast;

class MenusController extends Controller
{
	public function index()
    {
     if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $datas = SubMenu::all();

        return view('site.admin.menus.index', compact('datas'));
    }

    public function create()
    {
        if (! Gate::allows('superadmin')) {
            return abort(401);
        }

        return view('site.admin.menus.create');
    }

     public function store(Request $request)
    {
        if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $this->validate($request, [
            'name' => 'required|max:10',
            'display_name' => 'required|max:150',
        ]);

        $data = Menu::create($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('menus.index');
    }


 public function edit($id)
    {
      if (! Gate::allows('superadmin')) {
            return abort(401);
        }

        $relations = [
            'menu0' => Menu::get()->pluck('name', 'id')->prepend('Please select', ''),
        ];
       
        $data = SubMenu::findOrFail($id);
        
        return view('site.admin.menus.edit', compact(['data']) + $relations);
    }

    public function update(Request $request, $id)
    {
       if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $data = SubMenu::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('menus.index');
    }

  public function editMenu($id)
    {
      if (! Gate::allows('superadmin')) {
            return abort(401);
        }
       
        $data = Menu::findOrFail($id);
        
        return view('site.admin.menus.edit-menu', compact(['data']));
    }

    public function goupdate(Request $request, $id)
    {
       if (! Gate::allows('superadmin')) {
            return abort(401);
        }
        $data = Menu::findOrFail($id);
        $data->update($request->all());
        Toast::success('Data Successfull', 'info');
        return redirect()->route('menus.index');
    }

    public function destroy($id)
    {
        if (! Gate::allows('superadmin')) {
            return abort(401);
        }     
        $data = SubMenu::where('id', '=', $id)->delete();
        Toast::success('Delete Successfull', 'info');
        return redirect()->route('menus.index');
    }


}
