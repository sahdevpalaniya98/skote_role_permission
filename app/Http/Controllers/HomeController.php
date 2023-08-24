<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        try {
            $data                       = [];
            $data['page_title']         = 'Dashboard';
            return view('home',$data);
        } catch (\Exception $e) {
            return abort(404);
        }
    }
}
