<?php

namespace App\Console\Commands;

use App\Jobs\SyncResourceQueue;
use App\Models\Resource\Resource;
use Illuminate\Console\Command;
use App\Http\Resources\DigitalResource\DigitalResource;
use App\Http\Resources\Meilisearch\MeilisearchResource;
use App\Models\Article\Article;
use App\Models\Book\Book;
use App\Models\Research\Research;


class ResourcesSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resources:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Resource::with([
            'curators',
            'medias' ,
            'editors',
            'library',
            'language',
            'subjects',
            'resourceable',
        ])
        ->chunk(10, function ($resource): void {
            

           
    foreach ($resource as $res) {
       $res->searchable();
    }
    
        $this->info('Resources Dispatched successfully.');
    });
}
}
