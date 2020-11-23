<?php

namespace App\Http\Controllers;

use App\Mail\paymentEmail;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe;

class UserTransactionController extends Controller
{
    public function index()
    {
        if (auth('api')->id()) {

            $history = UserTransaction::where('send_by', auth('api')->id())->orWhere('send_by', auth('api')->id())->with('sendBy')->with('sendTo')->orderByDesc('id')->get();
            if ($history &&  $history->count() > 0) {
                $newarray=[];
                foreach ($history as $data) {

                    if ($data->send_by == null || $data->send_by == 'null') {
                        $data->send_by = [];
                    }
                    if ($data->send_to == null || $data->send_to == 'null') {
                        $data->send_to = [];
                    }
                    $newarray[]= $data;
                }
                return response()->json(['history' => $newarray, 'success' => true, 'code' => 200], 200);
            } else {
                return response()->json(['error' => 'no history exists for this user', 'code' => 201], 201);
            }
        } else {
            return response()->json(['error' => 'plz login to view history', 'code' => 201], 201);
        }
    }
    public function stripePost(Request $request)
    {

        if (auth('api')->id()) {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
            if ($request->card && $request->card_expiry_month && $request->card_expiry_year && $request->card_cvc && $request->amount) {
                $token = $stripe->tokens->create([
                    'card' => [
                        'number' => $request->card,
                        'exp_month' => $request->card_expiry_month,
                        'exp_year' => $request->card_expiry_year,
                        'cvc' => $request->card_cvc,
                    ],
                ]);
                $payment = Stripe\Charge::create([
                    "amount" =>  $request->amount * 100,
                    "currency" => "usd",
                    "source" => $token,
                    "description" => 'flow me payment from user'
                ]);
                $savingUserTransaction= $this->addPaymentUser($request->amount,auth('api')->id() );
                if ($payment && $savingUserTransaction) {
                    return response()->json(['stripe_payment' => $payment, 'user'=>$savingUserTransaction, 'success' => true, 'code' => 200], 200);
                } else {
                    return response()->json(['error' => 'error in sending payment', 'code' => 201], 201);
                }
            } else {
                return response()->json(['error' => 'data validation error', 'code' => 201], 201);
            }

            return back();
        } else {
            return response()->json(['error' => 'plz login to do transaction', 'code' => 201], 201);
        }
    }
    public function addPaymentUser($amount, $user_id){
        $transaction = new UserTransaction;
        $transaction->send_by = $user_id;
        $transaction->send_to = "stripe";
        $transaction->amount = $amount;
        $user = User::findOrfail($user_id);
        $user->total_cash= $user->total_cash+$amount;
        Mail::to($user->email)->send(new paymentEmail($user));
        if($user->save() && $transaction->save()){
            return ['user'=>$user];
        } else{
            return false;
        }
    }
}
