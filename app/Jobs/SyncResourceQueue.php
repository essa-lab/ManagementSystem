<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SyncResourceQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    
    public $resource;
    public function __construct($resource)
    {
        //
        $this->resource = $resource;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $host = env('MEILISEARCH_HOST');
        $apiKey = env('MEILISEARCH_KEY');

            try {

                $response = Http::withHeaders([
                    'Authorization' => "Bearer $apiKey",
                    'Content-Type' => 'application/json',
                ])->post("$host/indexes/resources/documents?primaryKey=id",  $this->resource);

                if ($response->failed()) {
                    info('Faild Response : '.json_encode($response->json()));
                }
    
            } catch (\Exception $e) {
                info('Exception Response : '.json_encode($e->getMessage()));
            }


    }

}