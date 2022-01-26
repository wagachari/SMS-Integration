<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class SmsIntegration extends Controller
{
    public function sendMessage(Request $request){

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

}
}catch(\Exception $e){
    Log::info("couldn't get response error message is: ".$e->getMessage());

}
}
}
