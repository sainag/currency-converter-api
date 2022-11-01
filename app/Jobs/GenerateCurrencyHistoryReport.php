<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\CurrencyHistoryReport;

class GenerateCurrencyHistoryReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fields;
    public $user;
    public $reportId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fields, $user, $reportId)
    {
        $this->fields = $fields;
        $this->user = $user;
        $this->reportId = $reportId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {        
            $timeStamp = new \DateTime('now');
            $endDate = $timeStamp->format('Y-m-d');
            if($this->fields['range'] == 'one-year') {
                $timeStamp->sub(new \DateInterval('P1Y'));
                $startDate = $timeStamp->format('Y-m-d');
                $interval = 'monthly';
            } elseif($this->fields['range'] == 'six-months') {
                $timeStamp->sub(new \DateInterval('P6M'));
                $startDate = $timeStamp->format('Y-m-d');
                $interval = 'weekly';
            }  elseif($this->fields['range'] == 'one-month') {
                $timeStamp->sub(new \DateInterval('P1M'));
                $startDate = $timeStamp->format('Y-m-d');
                $interval = 'daily';
            }
            Log::debug(json_encode([
                'reportId' => $this->reportId,
                'userId' => $this->user['id'],
                'range' => $this->fields['range'],
                'interval' => $interval,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'source' => $this->fields['source'],
                'currency' => $this->fields['currency']
            ], true));

            $response = Http::withHeaders([
                'apiKey' => getenv('CURRENCY_DATA_API_KEY')
            ])->get(getenv('CURRENCY_DATA_API_URL').'/timeframe', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'source' => strtoupper($this->fields['source']),
                'currencies' => strtoupper($this->fields['currency'])
            ]);

            $responseBody = $response->body();
            
            $historyReport = CurrencyHistoryReport::findOrFail($this->reportId);
            $historyReport->interval = $interval;
            $historyReport->startDate = $startDate;
            $historyReport->endDate = $endDate;
            $historyReport->report = json_encode(json_decode($responseBody), true);
            $historyReport->save();

            Log::info("Successfully generated currency history report id ".$this->reportId." for user id ".$this->user['id']);
        } catch(Throwable $e) {
            Log::error("Unable to generate currency history report ".json_encode($e));
        }
    }
}
