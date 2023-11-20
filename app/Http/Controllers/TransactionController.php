<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionCreateRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{

    public function index(Request $request)
    {

        try {

            $user_id = $request->user()->id;
            $user_type = $request->user()->user_type;

            // $user_id = 1;
            // $user_type = "ADMIN";

            $transactions = Transaction::when($user_type == 'CUSTOMER', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->with('payments')->orderBy('id', 'desc')->get();

            // dd(TransactionResource::collection($transactions));
            // return response()->json($transactions);
            return TransactionResource::collection($transactions);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create(TransactionCreateRequest $request)
    {
        try {

            Transaction::create($request->only([
                'amount',
                'user_id',
                'due_on',
                'vat',
                'is_vat_inclusive',
            ]));

            // ** Adding payable_amount in TransactionObserver

            return response()->json(['message' => 'Transaction created successfully'], 201);


        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
