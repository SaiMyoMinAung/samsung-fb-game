<?php

namespace App\Http\Controllers;

use App\Exports\ExportUser;
use App\Models\GameUsedUser;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $gameUsedUsers = GameUsedUser::latest()->paginate(10);

        return view('home', compact('gameUsedUsers'));
    }

    public function exportUsers(Request $request)
    {
        return Excel::download(new ExportUser, 'users.xlsx');
    }

}
