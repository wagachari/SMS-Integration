<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Jobs\saveSendSMSRequest;
use App\Jobs\updateSendSMSRequest;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JWTAuth;
use Log;

class SmsIntegration extends Controller
{
    protected $user;

    public function __construct()
    {

        $this->user = JWTAuth::parseToken()->authenticate();

        $this->request_id = "smsRequest" . preg_replace('/\D/', '', date("Y-m-d H:i:s", explode(" ", microtime())[1]) . substr((string) explode(" ", microtime())[0], 1, 4)) . Str::random(10);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_address' => 'required|string|max:255',
            'dst_address' => 'required|digits_between:9,12',
            'message' => 'required|string|max:1500',

        ]);
        return $validator->fails()
        ?
        response()->json(['validation_errors' => $validator->errors()], 422)
        :
        $this->store($request);

    }

    public function store($request)
    {

        $this->dispatch(new saveSendSMSRequest($request->toArray(), $this->request_id));

        $token = Cache::remember('token', 3500, function () {
            return $this->authenticate();
        });

        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            'https://api.mojasms.dev/sendsms',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'from' => $request->source_address,
                    'phone' => $request->dst_address,
                    'message' => $request->message,
                    'message_id' => $this->request_id,
                    'webhook_url' => config('services.mojagate.webhook'),

                ],
            ]
        );
        $body = $response->getBody();
        if ($response->getStatusCode() == 200) {
            $arr = json_decode((string) $body, true);
            $this->dispatch(new updateSendSMSRequest($arr, $this->request_id));
        } else {

            Log::info("sending message to " . $request->dst_address . " failed " . $this->request_id);
            $response = array
                (
                'status_code' => 999,
                'description' => "Failed to send message. Please try again later",
                'dst_address' => $request->dst_address,
                'message' => $request->message,
                'message_id' => $this->request_id,
            );

            response()->json($response, 200)->send();

// TO DO: Fail the transaction or retry sending
        }

    }
    //TO DO move this to traits
    public function authenticate()
    {

        $email = config('services.mojagate.email');
        $password = config('services.mojagate.password');

        $client = new \GuzzleHttp\Client();
        try {
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
            if ($response->getStatusCode() == 200) {
                $arr = json_decode((string) $body, true);
                if ($arr['status']) {
                    $token = (($arr['data'])['token']);
                    $expires_at = (($arr['data'])['expires_at']);
                    var_dump($arr);
                    return $token;
                }
            } else {
                // Fail the transaction or retry authorizing
            }
        } catch (\Exception $e) {
            Log::info("couldn't get response error message is: " . $e->getMessage());

        }
    }
}
