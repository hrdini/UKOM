<?php

namespace App\Http\Livewire;

use Cart;
use Stripe;
use Exception;
use App\Models\Order;
use App\Mail\OrderMail;
use App\Models\Country;
use Livewire\Component;
use App\Models\Shipping;
use App\Models\OrderItem;
use App\Models\Expedition;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CheckoutComponent extends Component
{
    public $ship_to_different;

    public $firstname;
    public $lastname;
    public $email;
    public $mobile;
    public $line1;
    public $line2;
    public $city;
    public $province;
    public $country;
    public $zipcode;

    public $s_firstname;
    public $s_lastname;
    public $s_email;
    public $s_mobile;
    public $s_line1;
    public $s_line2;
    public $s_city;
    public $s_province;
    public $s_country;
    public $s_zipcode;

    public $paymentmode;
    public $thankyou;

    public $card_no;
    public $exp_month;
    public $exp_year;
    public $cvc;

    public $select_expedition;
    public $shipping_charge;

    public function updated($fields)
    {
        $this->validateOnly($fields,[
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|numeric',
            'line1' => 'required',
            'city' => 'required',
            'province' => 'required',
            'country' => 'required',
            'zipcode' => 'required',
            'paymentmode' => 'required',
            'select_expedition' => 'required'
    ]);

    if($this->ship_to_different)
    {
        $this->validateOnly($fields,[
            's_firstname' => 'required',
            's_lastname' => 'required',
            's_email' => 'required|email',
            's_mobile' => 'required|numeric',
            's_line1' => 'required',
            's_city' => 'required',
            's_province' => 'required',
            's_country' => 'required',
            's_zipcode' => 'required'
    ]);
    }
    if ($this->paymentmode == 'card') {
        $this->validateOnly($fields,[
            'card_no'=>'required | numeric',
            'exp_month'=>'required | numeric',
            'exp_year'=>'required | numeric',
            'cvc'=>'required | numeric'
        ]);
    }
    }

    public function placeOrder()
    {
        $this->validate([
                'firstname' => 'required',
                'lastname' => 'required',
                'email' => 'required|email',
                'mobile' => 'required|numeric',
                'line1' => 'required',
                'city' => 'required',
                'province' => 'required',
                'country' => 'required',
                'zipcode' => 'required',
                'paymentmode' => 'required'
        ]);

        if ($this->paymentmode == 'card') {
            $this->validate([
                'card_no'=>'required | numeric',
                'exp_month'=>'required | numeric',
                'exp_year'=>'required | numeric',
                'cvc'=>'required | numeric'
            ]);
        }

        // shippingcharge=
        $shippingcharge = $this->shoppingcharge() * 5/10;
        $totalcharge = $this->totalcharge() + $shippingcharge;

        // $selected_country = Country::where('distance', $this->country)->first()->country;
        // $exp_id = Expedition::where('price_km', $this->select_expedition)->first()->id;
        // $exp_agent = Expedition::where('price_km', $this->select_expedition)->first()->name;
            $order = new Order();
            $order->user_id = Auth::user()->id;
            $order->subtotal = session()->get('checkout')['subtotal'];
            $order->discount = session()->get('checkout')['discount'];
            $order->tax = session()->get('checkout')['tax'];
            $order->total = $totalcharge;
            $order->firstname = $this->firstname;
            $order->lastname = $this->lastname;
            $order->email = $this->email;
            $order->mobile = $this->mobile;
            $order->line1 = $this->line1;
            $order->line2 = $this->line2;
            $order->city = $this->city;
            $order->province = $this->province;
            $order->country = $this->country;
            $order->zipcode = $this->zipcode;
            $order->status = 'ordered';
            $order->is_shipping_different = $this->ship_to_different ? 1:0;
            $order->shipping_charge = $shippingcharge;
            $order->save();

            foreach(Cart::instance('cart')->content() as $item)
            {
                $orderItem = new OrderItem();
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->price = $item->price;
                $orderItem->quantity = $item->qty;
                if($item->options)
                {
                    $orderItem->options = serialize($item->options);
                }
                $orderItem->save();
            }

            if($this->ship_to_different)
            {
                $this->validate([
                    's_firstname' => 'required',
                    's_lastname' => 'required',
                    's_email' => 'required|email',
                    's_mobile' => 'required|numeric',
                    's_line1' => 'required',
                    's_city' => 'required',
                    's_province' => 'required',
                    's_country' => 'required',
                    's_zipcode' => 'required'
            ]);

            $shipping = new Shipping();
            $shipping->order_id = $order->id;
            $shipping->firstname = $this->s_firstname;
            $shipping->lastname = $this->s_lastname;
            $shipping->email = $this->s_email;
            $shipping->mobile = $this->s_mobile;
            $shipping->line1 = $this->s_line1;
            $shipping->line2 = $this->s_line2;
            $shipping->city = $this->s_city;
            $shipping->province = $this->s_province;
            $shipping->country = $this->s_country;
            $shipping->zipcode = $this->s_zipcode;
            $shipping->save();
            }

            if($this->paymentmode == 'cod')
            {
                $this->makeTransactions($order->id,'pending');
                $this->resetCart();
            }
            else if($this->paymentmode == 'card')
            {
                $stripe = Stripe::make(env('STRIPE_KEY'));

            try {
                $token = $stripe->tokens()->create([
                    'card'=>[
                        'number'=>$this->card_no,
                        'exp_month'=>$this->exp_month,
                        'exp_year'=>$this->exp_year,
                        'cvc'=>$this->cvc
                    ]
                ]);

                if (!isset($token['id'])) {
                    session()->flash('stripe_error','The stripe token was not generated correctly!');
                    $this->thankyou = 0;
            }

            $customer = $stripe->customers()->create([
                'name'=>$this->firstname.' '.$this->lastname,
                'email'=>$this->email,
                'phone'=>$this->mobile,
                'address'=>[
                    'line1'=>$this->line1,
                    'postal_code'=>$this->zipcode,
                    'city'=>$this->city,
                    'state'=>$this->province,
                    'country'=>$this->country
                ],
                'shipping'=>[
                    'name'=>$this->firstname.' '.$this->lastname,
                    'address'=>[
                        'line1'=>$this->line1,
                        'postal_code'=>$this->zipcode,
                        'city'=>$this->city,
                        'state'=>$this->province,
                        'country'=>$this->country
                    ],
                ],
                'source'=>$token['id']
            ]);

            $charge = $stripe->charges()->create([
                'customer'=>$customer['id'],
                'currency'=>'USD',
                'amount'=>session()->get('checkout')['total'],
                'description'=>'Payment for order no '.$order->id
            ]);

            if ($charge['status'] == 'succeeded') {
                $this->makeTransactions($order->id,'approved');
                $this->resetCart();

            } else {
                session()->flash('stripe_error', 'Error in transactions!');
                $this->thankyou = 0;
            }

        } catch (Exception $e) {
            session()->flash('stripe_error', $e->getMessage());
            $this->thankyou = 0;
        }
    }
    $this->sendOrderConfirmationMail($order);
    }

    public function resetCart()
    {
        $this->thankyou = 1;
        Cart::instance('cart')->destroy();
        session()->forget('checkout');
    }

    public function setShippingCharge(){
        
        if (isset($this->country)) {
            $country_l = Country::where('distance', $this->country)->first()->distance;
            $price_km = Expedition::where('price_km', $this->select_expedition)->first()->price_km;
            $this->shipping_charge = $price_km * $country_l;

        } else {
            session()->flash('msg', 'Select the country first!');
        }
        
    }

    public function makeTransactions($order_id,$status)
    {
        $transaction = new Transaction();
        $transaction->user_id = Auth::user()->id;
        $transaction->order_id = $order_id;
        $transaction->mode = $this->paymentmode;
        $transaction->status = $status;
        $transaction->save();
    }

    public function sendOrderConfirmationMail($order)
    {
        Mail::to($order->email)->send(new OrderMail($order));
    }

    public function verifyForCheckout()
    {
        if(!Auth::check())
        {
            return redirect()->route('login');
        }
        else if($this->thankyou)
        {
            return redirect()->route('thankyou');
        }
        else if(!session()->get('checkout'))
        {
            return redirect()->route('product.cart');
        }
    }

    //shippingcharge=
    public function shoppingcharge(){
        return (float) str_replace(',' , '',  Cart::instance('cart')->subtotal());
    }

    //shippingcharge=
    public function totalcharge(){
        return (float) str_replace(',' , '',  Cart::instance('cart')->total());
    }

    // public function setShippingCharge()
    // {
    //     if(isset($this->country)||isset($this->s_country))
    //     {
    //         $country_distance = country::where('distance',$this->country)->first()->distance;
    //         $price_km = expedition::where('price_km',$this->name)->first()->price_km;
    //         $this->shipping_charge = $country_distance * $price_km;
    //     }
    // }

    public function render()
    {
        //shippingcharge=
        $shippingcharge = $this->shoppingcharge() * 5/100;
        $totalcharge = $this->totalcharge() + $shippingcharge;

        $this->verifyForCheckout();

        //shippingcharge=
        return view('livewire.checkout-component',['shippingcharge'=>$shippingcharge,'totalcharge'=>$totalcharge])->layout('layouts.base');
    }
}
