@extends('layouts.frontend_app')

@section('frontend_content')
 <!-- .breadcumb-area start -->
 <div class="breadcumb-area bg-img-4 ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcumb-wrap text-center">
                    <h2>Shopping Cart</h2>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><span>Shopping Cart</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- .breadcumb-area end -->
    <!-- cart-area start -->
    <div class="cart-area ptb-100">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @if (session('remove'))
                    <div class="alert alert_warning">
                       {{ session('remove') }}
                    </div>   
                    @endif
                    <form action="http://themepresss.com/tf/html/tohoney/cart">
                        <table class="table-striped cart-wrap table-width">
                            <thead>
                                <tr>
                                    <th class="images">Image</th>
                                    <th class="product">Product</th>
                                    <th class="ptice">Price</th>
                                    <th class="quantity">Quantity</th>
                                    <th class="total">Total</th>
                                    <th class="remove">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $cart_sub_total = 0;
                                @endphp
                                @forelse (cart_items() as $cart_item)
                                <tr>
                                    <td class="images"><img src="{{ asset('uploads/product_photos') }}/{{ $cart_item->product->product_thambnail_photo }}" alt="not found"></td>
                                    <td class="product"><a href="{{ url('product/details') }}/{{ $cart_item->product->slug }}" target="_blank">{{ $cart_item->product->product_name }}</a></td>
                                    <td class="ptice">${{ $cart_item->product->product_price }}</td>
                                    <td class="quantity cart-plus-minus">
                                        <input type="text" value="{{ $cart_item->product_quantity }}" />
                                    </td>
                                    <td class="total">${{  $cart_item->product_quantity * $cart_item->product->product_price }}</td>
                                    <td class="remove">
                                        <a href="{{ route('cart.remove', $cart_item->id) }}"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                                @php
                                    $cart_sub_total += ($cart_item->product_quantity * $cart_item->product->product_price);
                                @endphp
                                @empty
                                <tr>
                                    <td colspan="50" class="text-center text-danger">No Data Available</td>
                                </tr>
                                @endforelse
                               

                            </tbody>
                        </table>
                        <div class="row mt-60">
                            <div class="col-xl-4 col-lg-5 col-md-6 ">
                                <div class="cartcupon-wrap">
                                    <ul class="d-flex">
                                        <li>
                                            <button>Update Cart</button>
                                        </li>
                                        <li><a href="{{ url('shop') }}" target="_blank">Continue Shopping</a></li>
                                    </ul>
                                    <h3>Cupon</h3>
                                    <p>Enter Your Cupon Code if You Have One</p>
                                    <div class="cupon-wrap">
                                        <input type="text" placeholder="Cupon Code">
                                        <button>Apply Cupon</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 offset-xl-5 col-lg-4 offset-lg-3 col-md-6">
                                <div class="cart-total text-right">
                                    <h3>Cart Totals</h3>
                                    <ul>
                                        <li><span class="pull-left">Subtotal </span>${{ $cart_sub_total }}</li>
                                        <li><span class="pull-left"> Total </span> $380.00</li>
                                    </ul>
                                    <a href="checkout.html">Proceed to Checkout</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- cart-area end -->
@endsection
