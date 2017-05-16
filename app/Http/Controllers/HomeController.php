<?php


namespace App\Http\Controllers;

use App\models\PolicyProcedure; // register model 
use DB;
use Log;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\SubMenu;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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

        // $type = [];
        // $datas = DB::table('menu_type')->get();

        // foreach ($datas as $key => $value) {
        //       array_push($type, $value->type_id);
        //      }

        // $detail = Menus::wherein('menu_type',$type)->get();  
        // Log::info($detail);
        // $detail =  Menus::with('submenu')->get();
        $detail = Menu::all()->load('submenu');
        // $header = DB::table('menu_type')->get();
        // // $detail = DB::table('menus')->get();

        // foreach ($header as $key ) {
        //       array_push($type, $key->type_id);
        //      }
 
        $menu = Menu::all()->load('submenu');
        // Log::info($menu);

        return view('home',compact('menu'));
    }

    // Where ever you want your menu
    public function menu()
    {
        $menu = Menu::all()->load('submenu');

        return view('home',compact('menu'));
    }
   
}
