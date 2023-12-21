<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class paymentController extends Controller
{
    public function payment(){
        echo 'pay now';

        $apiKey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
        $merchantId = 'PGTESTPAYUAT';
        $keyIndex = 1;
        $redirect_url = url('pay-success');
        
        // Prepare the payment request data (you should customize this)
        $paymentData = array(
            'merchantId' => $merchantId,
            'merchantTransactionId' => "MT7850590068188104",
            "merchantUserId"=>"MUID123",
            'amount' => 1000, // Amount in paisa (10 INR)
            'redirectUrl'=> url('pay-success'),
            'redirectMode'=>"POST",
            'callbackUrl'=> url('pay-success'),
            "merchantOrderId"=> "12315313",
            "mobileNumber"=>"123145612315",
            "message"=>"Order description",
            "email"=>"CUSTMER_EMAIL_ID",
            "shortName"=>"CUSTMER_Name",
            "paymentInstrument"=> array(    
                "type"=> "PAY_PAGE",
            )
        );

        
        $jsonencode = json_encode($paymentData);
        $payloadMain = base64_encode($jsonencode);

        $payload = $payloadMain . "/pg/v1/pay" . $apiKey;
        $sha256 = hash("sha256", $payload);
        $final_x_header = $sha256 . '###' . $keyIndex;
        $request = json_encode(array('request'=>$payloadMain));

        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $request,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "X-VERIFY: " . $final_x_header,
            "accept: application/json"
        ],
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
        echo "cURL Error #:" . $err;
        } else {
        $res = json_decode($response);
        

        // echo "<pre>";
        // print_r($res);

        if(isset($res->success) && $res->success=='1'){
            $paymentCode=$res->code;
            $paymentMsg=$res->message;
            $payUrl=$res->data->instrumentResponse->redirectInfo->url;
            
            // print_r($payUrl);
            echo "<a href='".$payUrl."'>Pay Nown ygyg kjbhk bkjhkj bjkbkj</a>";
            // header('Location:'.$payUrl) ;
        }
        }

    }
}
