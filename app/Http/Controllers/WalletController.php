<?php

namespace App\Http\Controllers;

use App\Utility\PayfastUtility;
use Illuminate\Http\Request;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\PublicSslCommerzPaymentController;
use App\Http\Controllers\InstamojoController;
use App\Http\Controllers\PaytmController;
use Auth;
use Session;
use App\Wallet;
use App\Utility\PayhereUtility;
use App\MobileTopupTransaction;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::where('user_id', Auth::user()->id)->latest()->paginate(9);
        return view('frontend.user.wallet.index', compact('wallets'));
    }

    public function recharge(Request $request)
    {
        $data['amount'] = $request->amount;
        $data['payment_method'] = $request->payment_option;

        // dd($data);

        $request->session()->put('payment_type', 'wallet_payment');
        $request->session()->put('payment_data', $data);

        if ($request->payment_option == 'paypal') {
            $paypal = new PaypalController;
            return $paypal->getCheckout();
        } elseif ($request->payment_option == 'stripe') {
            $stripe = new StripePaymentController;
            return $stripe->stripe();
        } elseif ($request->payment_option == 'sslcommerz') {
            $sslcommerz = new PublicSslCommerzPaymentController;
            return $sslcommerz->index($request);
        } elseif ($request->payment_option == 'instamojo') {
            $instamojo = new InstamojoController;
            return $instamojo->pay($request);
        } elseif ($request->payment_option == 'razorpay') {
            $razorpay = new RazorpayController;
            return $razorpay->payWithRazorpay($request);
        } elseif ($request->payment_option == 'paystack') {
            $paystack = new PaystackController;
            return $paystack->redirectToGateway($request);
        } elseif ($request->payment_option == 'proxypay') {
            $proxy = new ProxypayController;
            return $proxy->create_reference($request);
        } elseif ($request->payment_option == 'voguepay') {
            $voguepay = new VoguePayController;
            return $voguepay->customer_showForm();
        } elseif ($request->payment_option == 'payhere') {
            $order_id = rand(100000, 999999);
            $user_id = Auth::user()->id;
            $amount = $request->amount;
            $first_name = Auth::user()->name;
            $last_name = 'X';
            $phone = '123456789';
            $email = Auth::user()->email;
            $address = 'dummy address';
            $city = 'Colombo';

            return PayhereUtility::create_wallet_form($user_id, $order_id, $amount, $first_name, $last_name, $phone, $email, $address, $city);
        } elseif ($request->payment_option == 'payfast') {
            $user_id = Auth::user()->id;
            $amount = $request->amount;
            return PayfastUtility::create_wallet_form($user_id, $amount);
        } elseif ($request->payment_option == 'ngenius') {
            $ngenius = new NgeniusController();
            return $ngenius->pay();
        } else if ($request->payment_option == 'iyzico') {
            $iyzico = new IyzicoController();
            return $iyzico->pay();
        } else if ($request->payment_option == 'nagad') {
            $nagad = new NagadController;
            return $nagad->getSession();
        } else if ($request->payment_option == 'bkash') {
            $bkash = new BkashController;
            return $bkash->pay();
        } else if ($request->payment_option == 'aamarpay') {
            $aamarpay = new AamarpayController;
            return $aamarpay->index();
        } else if ($request->payment_option == 'mpesa') {
            $mpesa = new MpesaController();
            return $mpesa->pay();
        } else if ($request->payment_option == 'flutterwave') {
            $flutterwave = new FlutterwaveController();
            return $flutterwave->pay();
        } elseif ($request->payment_option == 'paytm') {
            $paytm = new PaytmController;
            return $paytm->index();
        }
    }

    public function wallet_payment_done($payment_data, $payment_details)
    {
        $user = Auth::user();
        $user->balance = $user->balance + $payment_data['amount'];
        $user->save();

        $wallet = new Wallet;
        $wallet->user_id = $user->id;
        $wallet->amount = $payment_data['amount'];
        $wallet->payment_method = $payment_data['payment_method'];
        $wallet->payment_details = $payment_details;
        $wallet->save();

        Session::forget('payment_data');
        Session::forget('payment_type');

        flash(translate('Payment completed'))->success();
        return redirect()->route('wallet.index');
    }

    public function offline_recharge(Request $request)
    {
        $wallet = new Wallet;
        $wallet->user_id = Auth::user()->id;
        $wallet->amount = $request->amount;
        $wallet->payment_method = $request->payment_option;
        $wallet->payment_details = $request->trx_id;
        $wallet->approval = 0;
        $wallet->offline_payment = 1;
        $wallet->reciept = $request->photo;
        $wallet->save();
        flash(translate('Offline Recharge has been done. Please wait for response.'))->success();
        return redirect()->route('wallet.index');
    }

    public function offline_recharge_request()
    {
        $wallets = Wallet::where('offline_payment', 1)->paginate(10);
        return view('manual_payment_methods.wallet_request', compact('wallets'));
    }

    public function updateApproved(Request $request)
    {
        $wallet = Wallet::findOrFail($request->id);
        $wallet->approval = $request->status;
        if ($request->status == 1) {
            $user = $wallet->user;
            $user->balance = $user->balance + $wallet->amount;
            $user->save();
        } else {
            $user = $wallet->user;
            $user->balance = $user->balance - $wallet->amount;
            $user->save();
        }
        if ($wallet->save()) {
            return 1;
        }
        return 0;
    }

    public function topup_list(Request $request)
    {
        $user = Auth::user();
        $date_range = null;

        $topup_history = MobileTopupTransaction::where('user_id', '=', $user->id)
            ->orderBy('created_at', 'desc');
        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $topup_history = $topup_history->whereDate('created_at', '>=', $date_range1[0]);
            $topup_history = $topup_history->whereDate('created_at', '<=', $date_range1[1]);
        }
        $topups = $topup_history->paginate(10);

        return view('frontend.user.wallet.topup.index', compact('topups', 'date_range'));
    }

    public function topup(Request $request)
    {

        $user = Auth::user();
        if ($user->balance < $request->amount) {
            flash(translate('You do not have enough balance in your wallet'))->error();
            return redirect()->route('wallet.index');
        }

        if ($request->amount < 10 || $request->amount > 1000) {
            flash(translate('Invalid recharge amount'))->error();
            return redirect()->route('topup.index');
        }

        if ($request->recharge_pin == null) {
            flash(translate('You didn\'t enter a pin'))->error();
            return redirect()->route('topup.index');
        }

        if ($user->recharge_pin !== $request->recharge_pin) {
            flash(translate('Pin no you entered doesn\'t match'))->error();
            return redirect()->route('topup.index');
        }

        if (get_setting('use_kdrl') == 1) {
            $user_type  = 'api';
            $user_name  = env('KDRL_USER_NAME');
            $password   = env('KDRL_PASSWORD');
            $mobile     = $request->mobile;
            $amount     = $request->amount;
            $transtype  = $request->account_type == 'prepaid' ? 'pre' : 'post';
            $TrnxID     = 'BS' . time();

            $topup_transaction = new MobileTopupTransaction;
            $topup_transaction->user_id = $user->id;
            $topup_transaction->mobile_no = $request->mobile;
            $topup_transaction->topup_amount = $request->amount;
            $topup_transaction->tx_id = $TrnxID;
            $topup_transaction->save();


            $url = "http://rkingroup.com/Page/WebLogin.aspx?UserType=$user_type&UserName=$user_name&Password=$password&MobileNo=$mobile&Amount=$amount&TranType=$transtype&TrnxID=$TrnxID";
            $response = file_get_contents($url);

            if (str_contains($response, '101')) {
                $user->balance = $user->balance - $request->amount + ($request->amount * get_setting('recharge_commission') / 100);
                $user->save();
                $status_code = '101';
                $status_description =  $response;
                flash(translate($response))->success();
            } else {
                $status_code = '201';
                $status_description = $response;
                flash(translate($response))->error();
            }

            $topup_transaction->status_code = $status_code;
            $topup_transaction->status_description = $status_description;
            $topup_transaction->update();
        } else {
            // $user_name = 'deshshopbd.com@gmail.com';
            // $password = '3y216';
            // $pin = '8271';
            $user_name  = env('BDSMART_USERNAME');
            $password   = env('BDSMART_PASSWORD');
            $pin        = env('BDSMART_PIN');
            $amount = $request->amount;


            $topup_transaction = new MobileTopupTransaction;
            $topup_transaction->user_id = $user->id;
            $topup_transaction->mobile_no = $request->mobile;
            $topup_transaction->topup_amount = $request->amount;


            //TOPUP BALANCE CHECK REQUEST
            $data = array(
                'username'      => $user_name,
                'password'      => $password,
                'pin'           => $pin,
                'order_number'  => ''
            );

            $check_balance_url = 'http://bdsmartpay.com/sms/topupbalanceapi.php';

            $options_check_balance = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context_check_balance = stream_context_create($options_check_balance);
            $check_balance_response = file_get_contents($check_balance_url, false, $context_check_balance);
            $check_balance = (array) json_decode(base64_decode($check_balance_response));

            // echo 'Specific Response: <pre>';print_r($check_balance['status']);die;
            if (isset($check_balance['status']) && $check_balance['status'] == 'false') {
                flash(translate('Temporary Unavailable This Service'))->error();
                return redirect()->route('topup.index');
            }
            if (isset($check_balance['balance']) &&  $check_balance['balance'] < $amount) {
                flash(translate('Temporary Unavailable This Service'))->error();
                return redirect()->route('topup.index');
            }

            $data1 = array(
                'username'      => $user_name,
                'password'      => $password,
                'pin'           => $pin,
                'operator'      => $request->operator,
                'mobile'        => $request->mobile,
                'account_type'  => $request->account_type,
                'amount'        => $request->amount,
                'order_number'  => 'BS' . time()
            );

            $url = 'http://bdsmartpay.com/sms/topupapi.php';

            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data1)
                )
            );
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);

            $result = (array)json_decode(base64_decode($response));

            //echo 'Overall Response: <pre>';print_r($result);
            //TOPUP STATUS REQUEST
            $data = array(
                'username'      => $user_name,
                'password'      => $password,
                'pin'           => $pin,
                'order_number'  => $result['order_number']
            );
            $url = 'http://bdsmartpay.com/sms/topupstatusapi.php';
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
            $status_result = (array)json_decode(base64_decode($response));

            //echo 'Specific Response: <pre>';print_r($status_result);

            if ($status_result['error_code'] == '109') {
                $user->balance = $user->balance - $request->amount;
                $user->save();
                $status_code = $status_result['error_code'];
                $status_description = $status_result['description'];

                flash(translate($status_result['description']))->success();
            } else {
                $status_code = $status_result['error_code'];
                $status_description = $status_result['description'];

                flash(translate($status_result['description']))->error();
            }

            $topup_transaction->status_code = $status_code;
            $topup_transaction->status_description = $status_description;
            $topup_transaction->update();
        }
        return redirect()->route('topup.index');
    }
}
