<?php

namespace App\Jobs;

use App\Models\SMSIntegration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class saveSendSmsRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;
    public $message_id;

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
    public function __construct($request, $message_id)
    {
        $this->request = $request;
        $this->message_id = $message_id;
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

            $transaction = new SMSIntegration;
            $transaction->message_id = $this->message_id;
            $transaction->message = $request->message ?? null;
            $transaction->dst_address = $request->dst_address ?? null;
            $transaction->source_address = $request->source_address ?? null;
            $transaction->amount = $this->smsLength($request->message ?? null) ?? null;

            $transaction->save();
//TO DO: add log levels
            Log::info("Saved Request with id: ", [$transaction->id]);

        } catch (Exception $e) {
            Log::info("Could not save request", [$e]);
        }

    }

    /**
     * calculate the sms length
     *
     * number of sms for billing
     * @return int
     */
    public function smsLength($message)
    {

        if (isset($message)) {
            return ceil((strlen($message) / 160));
        }

    }
}
