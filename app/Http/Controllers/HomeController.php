<?php


namespace App\Http\Controllers;

use App\models\PolicyProcedure; // register model 
use App\Models\Users;
use DB;
use Log;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\SubMenu;
use Toast;
use App\User;
use Auth;
use Illuminate\Support\Facades\Cache;
use laravelcollective\html;

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
        
        $detail = Menu::all()->load('submenu');
 
        $menu = Menu::all()->load('submenu');

        return view('home',compact(['menu']));

    }

    // Where ever you want your menu
    public function menu()
    {
        $menu = Menu::all()->load('submenu');

        return view('home',compact('menu'));
    }

    public function clearCache() {
        Cache::flush();
    }

     public function emails()
    {
        $datas = Users::all();

        return view('list-email',compact(['datas']));

    }
   
}
