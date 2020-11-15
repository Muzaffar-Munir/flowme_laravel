<?php

namespace App\Http\Controllers;

use App\Models\UserTransaction;

class UserTransactionController extends Controller
{
    public function index()
    {
        $history = UserTransaction::where('send_by', auth('api')->id())->orWhere('send_by', auth('api')->id())->with('sendBy')->with('sendTo')->orderByDesc('id')->get();
        return response()->json(['history' => $history], 200);
    }
}
