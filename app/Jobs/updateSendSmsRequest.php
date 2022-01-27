<?php

namespace App\Jobs;

use App\Models\Sms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class updateSendSmsRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;
    public $request_id;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $request_id)
    {
        $this->request = $request;
        $this->request_id = $request_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try
        {

            $request = json_decode(json_encode($this->request), false);

            $updated_at = $this->request['data']['recipients'][0]["credit_bucket"]["updated_at"];

            $id = Sms::where("message_id", $this->request_id)->value('id');

            $transaction = Sms::find($id);
            $transaction->response_desc = $request->status ?? null;
            $transaction->response_message = $request->message ?? null;
            $transaction->sent_at = $updated_at ?? null;

            $transaction->update();

            $responseToClient = array
                (
                'status_code' => $request->status ?? null,
                'description' => $request->message ?? null,
                'message_id' => $this->request_id,
            );

            response()->json($responseToClient, 200)->send();
            Log::info("Updated Request with id: ", [$transaction->id]);

        } catch (Exception $e) {
            Log::info("Failed to update Request with id: ", [$transaction->id]);
        }

    }
}
