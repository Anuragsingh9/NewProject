<?php

namespace App\Http\Controllers;

use App\User;

class UserController extends Controller
{

    public function show($userId)
    {
        try{
            $user = User::find($userId);
            if($user) {
                return response()->json($user);
            }
            return response()->json(['message' => 'User not found!'], 404);
        } catch(\Exception $e){
            return response()->json(['status' =>FALSE, 'error'=> $e->getMessage()],500);
        }
    }
}
