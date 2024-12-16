<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsSource;
use App\Models\NewsItem;
use Vedmant\FeedReader\Facades\FeedReader;

class UpdateNewsFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command to fetch news from all active sources';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sources = NewsSource::where('is_active', true)->get();

        foreach ($sources as $source) {
            $this->info("Fetching news from {$source->name}");
            $this->fetchNewsFromSource($source);
        }

        $this->info('News fetched successfully');
        return 0;
    }

    private function fetchNewsFromSource(NewsSource $source)
    {
        $reader = FeedReader::read($source->url);
        foreach ($reader->get_items() as $item) {
            // if original id is already in the database, update
            if (NewsItem::where('original_id', $item->get_id())->exists()) {
                $newsItem = NewsItem::where('original_id', $item->get_id())->first();
                $newsItem->title = $item->get_title();
                $newsItem->description = $item->get_description();
                $newsItem->url = $item->get_link();
                $newsItem->published_at = $item->get_date();
                $newsItem->source_id = $source->id;
                $newsItem->original_id = $item->get_id();
                $newsItem->image = $item->get_enclosure()->thumbnails[0] ?? null;
                $newsItem->save();
            } else {
                $newsItem = new NewsItem();
                $newsItem->title = $item->get_title();
                $newsItem->description = $item->get_description();
                $newsItem->url = $item->get_link();
                $newsItem->published_at = $item->get_date();
                $newsItem->source_id = $source->id;
                $newsItem->original_id = $item->get_id();
                $newsItem->image = $item->get_enclosure()->thumbnails[0] ?? null;
                $newsItem->save();
            }

            
        }
    }
}
