<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseConfirm;
use App\Models\Billing;
use App\Models\City;
use App\Models\Country;
use App\Models\order;
use App\Models\Order_detail;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('frontend.checkout', [
            'user' => User::find(Auth::user()->id),
            'countries' => Country::all(),
            'cities' => City::all(),
        ]);
    }
    public function checkoutPost(Request $request)
    {
        if (isset($request->shipping_address_status)) {
            $shipping_id = Shipping::insertGetId([
                'name' => $request->shipping_name,
                'email' => $request->shipping_email,
                'phone_number' => $request->shipping_phone_number,
                'country_id' => $request->shipping_country_id,
                'city_id' => $request->shipping_city_id,
                'address' => $request->shipping_address,
                'created_at' => Carbon::now(),
            ]);
        } else {
            $shipping_id = Shipping::insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
                'address' => $request->address,
                'created_at' => Carbon::now(),
            ]);
        }
        $biling_id = Billing::insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'notes' => $request->notes,
            'created_at' => Carbon::now(),
        ]);
        $order_id = Order::insertGetId([
            'user_id' => Auth::user()->id,
            'sub_total' => session('cart_sub_total'),
            'discount_amount' => session('discount_amount'),
            'coupon_name' => session('coupon_name'),
            'total' => (session('cart_sub_total') - session('discount_amount')),
            'payment_option' => $request->payment_option,
            'billing_id' => $biling_id,
            'shipping_id' => $shipping_id,
            'created_at' => Carbon::now(),
        ]);
        foreach (cart_items() as $cart_item) {
            Order_detail::insert([
                'order_id' => $order_id,
                'user_id' => Auth::user()->id,
                'product_id' => $cart_item->product_id,
                'product_quantity' => $cart_item->product_quantity,
                'product_price' => $cart_item->product->product_price,
                'created_at' => Carbon::now(),
            ]);
            //Product Table Decrement----------
            Product::find($cart_item->product_id)->decrement('product_quantity', $cart_item->product_quantity);
            //Delete from Cart Table-----------
            $cart_item->forceDelete();
        }
        $order_details = Order_detail::where('order_id', $order_id)->get();
        Mail::to($request->email)->send(new PurchaseConfirm($order_details));
        if($request->payment_option == 2){
            session(['order_id_from_checkout_page'=> $order_id]);
            return redirect('stripe');
        }else{

            return redirect('cart/index')->with('success', 'Your Order Successfully Complete');

        }
    }

    public function testMail(){
        $order_details = Order_detail::where('order_id', 9)->get();
        return (new PurchaseConfirm($order_details))->render();
    }

    public function getCityListAjax(Request $request)
    {
        $stringToSend = "";
        $cities = City::where('country_id', $request->country_id)->get();
        foreach ($cities as $city) {
            $stringToSend .= "<option value='" . $city->id . "'>" . $city->name . "</option>";
        }
        return $stringToSend;
    }
}