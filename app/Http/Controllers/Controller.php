<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Mail\NewsLatter;
use Illuminate\Support\Facades\Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function deshboardIndex()
    {
        $users = User::latest()->simplePaginate(5);
        $total_users = User::count();
        return view('dashboard', compact('users', 'total_users'));
    }

    public function sendNewslatter()
    {
        foreach (User::all()->pluck('email') as $email) {
            Mail::to($email)->send(new NewsLatter());
        }
        return back();
    }
    
}