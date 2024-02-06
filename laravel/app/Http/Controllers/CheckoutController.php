<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\CheckoutAddress;
use App\Models\CheckoutPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{

    // protected $checkout;
    // protected $checkoutPayment;
    // protected $checkoutAddress;

    // public function __construct(Checkout $checkout, CheckoutAddress $checkoutAddress, CheckoutPayment $checkoutPayment)
    // {
    //     $this->checkout = $checkout;
    //     $this->checkoutPayment = $checkoutPayment;
    //     $this->checkoutAddress = $checkoutAddress;
    // }

    // public function store(Request $request)
    // {
    // $validator = Validator::make($request->all(), [
    //     'tour_id' => 'required',
    //     "first_name" => "required",
    //     "last_name" => "required",
    //     "email" => "required",
    //     "phone" => "required",
    //     "address.address_1" => "required",
    //     "address.city" => "required",
    //     "address.province" => "required",
    //     "address.postal_code" => "required",
    //     "address.country" => "required",
    //     "payment.payment_method" => "required",
    //     "payment.name_of_card" => "required",
    //     "payment.number_of_card" => "required",
    //     "payment.expiry_date" => "required",
    //     "payment.cvv" => "required"
    // ]);

    // if ($validator->fails()) {
    //     return response()->json([
    //         'errors' => $validator->errors()->toArray()
    //     ], 422);
    // }


    // DB::beginTransaction();

    // try {

    //     $credentials_checkout = collect($request->only($this->checkout->getFillable()))
    //         ->put('user_id', $request->user->id)
    //         ->toArray();
    //     $new_checkout = $this->checkout->create($credentials_checkout);

    //     $credentials_checkout_address = collect($request->input('address'))->only($this->checkoutAddress->getFillable())
    //         ->put('checkout_id', $new_checkout->id)
    //         ->toArray();
    //     $new_checkout_address = $this->checkoutAddress->create($credentials_checkout_address);

    //     $credentials_checkout_payment = collect($request->input('payment'))->only($this->checkoutPayment->getFillable())
    //         ->put('checkout_id', $new_checkout->id)
    //         ->toArray();
    //     $new_checkout_payment = $this->checkoutPayment->create($credentials_checkout_payment);

    //     return response()->json([
    //         'message' => 'Success'
    //     ], 201);

    //     DB::commit();
    // } catch (\Exception $e) {
    //     DB::rollBack();
    //     return response()->json(['error' => $e->getMessage()], 500);
    // }
    // }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required',
            "first_name" => "required",
            "last_name" => "required",
            "email" => "required",
            "phone" => "required",
            "address.address_1" => "required",
            "address.city" => "required",
            "address.province" => "required",
            "address.postal_code" => "required",
            "address.country" => "required",
            "payment.payment_method" => "required",
            "payment.name_of_card" => "required",
            "payment.number_of_card" => "required",
            "payment.expiry_date" => "required",
            "payment.cvv" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $checkout = Checkout::create($validator->validated() + ['user_id' => $request->user->id]);
            CheckoutAddress::create($request->address + ['checkout_id' => $checkout->id]);
            CheckoutPayment::create($request->payment + ['checkout_id' => $checkout->id]);

            DB::commit();

            return response()->json([
                'message' => 'Success'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return dd(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }
}
