<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Bmatovu\MtnMomo\Products\Collection;
use Bmatovu\MtnMomo\Exceptions\CollectionRequestException;

class MtnController extends Controller
{
    public function transferMoney(Request $request)
    {
        $amount = $request->amount;
        try {
            $collection = new Collection();

            $momoTransactionId = $collection->requestToPay('transactionId', '46733123453', $amount);
            if ($momoTransactionId) {
                return response()->json(['success' => 'Amount has been transferred.'], 200);
            }
        } catch (CollectionRequestException $e) {
            return response()->json(['error' => $e], 404);
        }
    }
    public function sendMoney(Request $request){
        // dd(auth('api')->id());
        if(auth('api')->id()){
            $amount = $request->amount;
            $sender= User::findOrFail(auth('api')->id());

            $receiver = User::where('email','=',$request->receiver_contact)->orWhere('phone_number','=', $request->receiver_contact)->first();
                if(!$receiver || !$sender){
                return response()->json(['error' => 'receiver or sender user not exist','code'=>201], 201);
            } else{
                $transaction = new UserTransaction;
                $transaction->send_by= $sender->id;
                $transaction->send_to= $receiver->id;
                $transaction->amount= $amount;
                if($transaction->save()){
                    return response()->json(['success' => 'transactions succeded','code'=>200], 200);
                } else{
                    return response()->json(['error' => 'error in form uploading','code'=>201], 201);
                }
            }
        } else{
            return response()->json(['error' => 'plz login to trigger this route','code'=>201], 201);
        }

    }
}
