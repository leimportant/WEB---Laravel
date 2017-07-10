<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\App;
use App\Models\Menu;
use App\Models\SubMenu;
use DB;
use Log;

class MenusController extends Controller
{
    

     public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = Menu::all()->load('submenu');

        return view('site.menu',compact('menu'));
    }

     public function search($id)
    {
        $menu = Menu::where('id', '!=',  [$id])->get()->load('submenu');

        $data = SubMenu::where('menu_id',  [$id])->get();
        
        return view('site.menu-search', compact('data', 'menu'));
    }
}
