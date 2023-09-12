# PayWithEasebuzz Laravel Package
This package exposes Easebuzz core payment apis as a Laravel package.

# Installation
```
composer require easebuzz/pay-with-easebuzz-laravel
```

## Create the Easebuzz Controller
```
php artisan make:controller EasebuzzController
```

## Easebuzz Controller
```
# EasebuzzController.php

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Easebuzz\PayWithEasebuzzLaravel\PayWithEasebuzzLib;


class EasebuzzController extends Controller
{
    //
    public function initiate_payment_show(): View
    {
        return view('initiate_payment', ['result' => '']);
    }
    public function initiate_payment_ebz(Request $request): View
    {
        $param = $request->post();
        $key = "YOUR_KEY_HERE";
        $salt = "YOUR_SALT_HERE";
        $env = "YOUR_ENV_HERE";
        $payebz = new PayWithEasebuzzLib($key, $salt, $env);
        $result = $payebz->initiatePaymentAPI($param, true);
        
        // var_dump($result);
        // die();

        return view('initiate_payment', ['result' => $result]);
    }
    public function ebz_response(Request $request): View
    {
        $param = $request->post();
        // $param will contain the response provided to you by Easebuzz. You can handle your success and failed scenarios based on the response.
        var_dump($param);
        die();
        return view('initiate_payment', ['result' => '']);
    }
}

```
### Please replace YOUR_KEY_HERE, YOUR_SALT_HERE and YOUR_ENV_HERE with your own key, salt and env provided by Easebuzz
#### Please note that this is a sample controller, please customize according to your needs then call initiatePaymentApi
```
// $param is an array. Parameter definitions can be found here - https://docs.easebuzz.in/docs/payment-gateway/8ec545c331e6f-initiate-payment-api

$result = $payebz->initiatePaymentAPI($param, true);

// Transaction Api 
// $param is an array. Parameter definitions can be found here - https://docs.easebuzz.in/docs/payment-gateway/910d60e2551c9-transaction-api
$result = $payebz->transactionAPI($param);

// Refund Api - v2
// $param is an array. Parameter definitions can be found here - https://docs.easebuzz.in/docs/payment-gateway/c2ac48618b3bd-refund-api-v2

$result = $payebz->refundAPIV2($param);


// Refund Status Api
// $param is an array. Parameter definitions can be found here - https://docs.easebuzz.in/docs/payment-gateway/de78eba8de53c-refund-status-api

$result = $payebz->refundStatus($param);
```