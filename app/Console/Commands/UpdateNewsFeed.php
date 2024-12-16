<?php

namespace App\Console\Commands;

use App\Models\NewsSource;
use App\Models\NewsItem;
use FeedReader;
use Illuminate\Console\Command;

class UpdateNewsFeed extends Command
{
    protected $signature = 'fetch:news';
    protected $description = 'Fetch news from all sources';

    public function handle()
    {
        $sources = NewsSource::where('is_active', true)->get();
        foreach ($sources as $source) {
            $this->info("Fetching news from {$source->name}");
            $this->fetchNewsFromSource($source);
        }
    }

    private function fetchNewsFromSource(NewsSource $source)
    {
        // First, get all news items that need to be deleted
        $newsToDelete = NewsItem::where('source_id', $source->id)->get();
        
        // Remove all relationships first
        foreach ($newsToDelete as $news) {
            $news->parentNews()->detach();
            $news->childNews()->detach();
        }

        // Then delete all existing news for this source
        NewsItem::where('source_id', $source->id)->delete();

        $reader = FeedReader::read($source->url);
        foreach ($reader->get_items() as $item) {
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
