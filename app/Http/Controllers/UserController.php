<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        
        $userId = auth()->user()->id;

        $todos = User::where('user_id', $userId)->get();

        return response()->json($todos);
    }
}
