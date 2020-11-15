<?php

namespace App\Http\Controllers;

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
}
