<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class SmsIntegration extends Controller
{
    public function sendMessage(Request $request){
//validation

//insert request to database


//send request to queue

//capture response and update the database

//
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            'https://api.mojasms.dev/sendsms',
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->authenticate(),
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'from' => $request->sender_name,
                    'phone' => $request->dst_address,
                    'message' => $request->message,
                    'message_id' => $request->message_id,
                    'webhook_url' => config('services.mojagate.webhook')
                    
                ],
            ]
        );
$body = $response->getBody();
print_r(json_decode((string) $body));

    }
 
    public function authenticate(){
    
        $email = config('services.mojagate.email');
        $password = config('services.mojagate.password');
        Log::info('username and password ',[$email, $password]);

        // die();

        $client = new \GuzzleHttp\Client();
        try{
        $response = $client->post(
            'https://api.mojasms.dev/login',
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ],
            ]
        );
        $body = $response->getBody();
        if($response->getStatusCode()==200){
            $arr = json_decode((string)$body, true); 
            if($arr['status'])
            {
                $token=(($arr['data'])['token']);
                $expires_at=(($arr['data'])['expires_at']);
                    return $token;
            }
        }
        else{
            // Fail the transaction or retry authorizing
        }
}catch(\Exception $e){
    Log::info("couldn't get response error message is: ".$e->getMessage());

}
}
}
