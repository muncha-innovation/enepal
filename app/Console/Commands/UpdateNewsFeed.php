<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsSource;
use App\Models\NewsItem;
use App\Models\UserGender;
use App\Models\AgeGroup;
use Vedmant\FeedReader\Facades\FeedReader;

class UpdateNewsFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news';
    protected $defaultSources = [
        [
            'name' => 'Ratopati',
            'url' => 'https://www.ratopati.com/feed',
            'is_active' => true,
            'language' => 'np',
        ],
        [
            'name' => 'Setopati',
            'url' => 'https://www.setopati.com/feed',
            'is_active' => true,
            'language' => 'np',
        ],
        
        [
            'name' => 'Setopati English',
            'url' => 'https://en.setopati.com/feed',
            'is_active' => true,
            'language' => 'en',
        ],
        [
            'name' => 'Onlinekhabar English',
            
            'url' => 'https://english.onlinekhabar.com/feed',
            'is_active' => true,
            'language' => 'en',
            'logo' => 'https://english.onlinekhabar.com/wp-content/themes/onlinekhabar-english-2020/img/site-main-logo.png'
        ]
    ];

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
        $this->updateSources();
        $sources = NewsSource::where('is_active', true)->get();

        foreach ($sources as $source) {
            $this->info("Fetching news from {$source->name}");
            $this->fetchNewsFromSource($source);
        }

        $this->info('News fetched successfully');
        return 0;
    }
    private function updateSources()
    {
        $sources = NewsSource::all();
        if ($sources->isEmpty()) {
            foreach ($this->defaultSources as $source) {
                NewsSource::create($source);
            }
        }
    }
    private function fetchNewsFromSource(NewsSource $source)
    {
        $reader = FeedReader::read($source->url);
        foreach ($reader->get_items() as $item) {
            
            if (NewsItem::where('original_id', $item->get_id())->exists()) {
                $newsItem = NewsItem::where('original_id', $item->get_id())->first();
                $newsItem->sourceable_id = $source->id;
                $newsItem->sourceable_type = NewsSource::class;
                $newsItem->title = $item->get_title();
                $newsItem->description = $item->get_description();
                $newsItem->url = $item->get_link();
                $newsItem->published_at = $item->get_date();
                $newsItem->original_id = $item->get_id();
                $newsItem->image = $this->extractImageFromItem($item);
                $newsItem->language = $source->language;
                $newsItem->is_active = false;
                $newsItem->is_featured = true;
                $newsItem->save();
                
                $this->attachDefaultSelections($newsItem);
            } else {
                $newsItem = new NewsItem();
                $newsItem->sourceable_id = $source->id;
                $newsItem->sourceable_type = NewsSource::class;
                $newsItem->title = $item->get_title();
                $newsItem->description = $item->get_description();
                $newsItem->url = $item->get_link();
                $newsItem->published_at = $item->get_date();
                $newsItem->is_active = false;
                $newsItem->is_featured = true;
                $newsItem->original_id = $item->get_id();
                $newsItem->image = $this->extractImageFromItem($item);
                $newsItem->language = $source->language;
                $newsItem->save();
                
                $this->attachDefaultSelections($newsItem);
            }
        }
    }

    /**
     * Extract image from RSS feed item using multiple approaches
     */
    private function extractImageFromItem($item)
    {
        $imageUrl = null;

       
        try {
            $enclosure = $item->get_enclosure();
            if ($enclosure) {
                // Check for thumbnails
                if (isset($enclosure->thumbnails) && !empty($enclosure->thumbnails)) {
                    $imageUrl = $enclosure->thumbnails[0];
                } 
                // Check for direct link if it's an image
                elseif ($enclosure->get_link() && $this->isImageUrl($enclosure->get_link())) {
                    $imageUrl = $enclosure->get_link();
                }
            }
        } catch (\Exception $e) {
            // Continue to next method if enclosure fails
        }

        // Method 2: Try to get from media:content or media:thumbnail (Media RSS)
        if (!$imageUrl) {
            try {
                // Get the raw feed item data
                $rawItem = $item->get_feed()->get_item($item->get_id());
                $data = $rawItem->get_item_tags('http://search.yahoo.com/mrss/', 'thumbnail');
                
                if ($data && isset($data[0]['attribs']['']['url'])) {
                    $imageUrl = $data[0]['attribs']['']['url'];
                }
                
                // Try media:content if thumbnail didn't work
                if (!$imageUrl) {
                    $data = $rawItem->get_item_tags('http://search.yahoo.com/mrss/', 'content');
                    if ($data && isset($data[0]['attribs']['']['url'])) {
                        $url = $data[0]['attribs']['']['url'];
                        if ($this->isImageUrl($url)) {
                            $imageUrl = $url;
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }

        if (!$imageUrl) {
            $description = $item->get_description();
            if ($description) {
                $imageUrl = $this->extractImageFromContent($description);
            }
        }

        if (!$imageUrl) {
            try {
                $rawData = $item->get_item_tags('', 'image');
                if ($rawData && isset($rawData[0]['data'])) {
                    $possibleUrl = $rawData[0]['data'];
                    if ($this->isImageUrl($possibleUrl)) {
                        $imageUrl = $possibleUrl;
                    }
                }
            } catch (\Exception $e) {
          
            }
        }

        if ($imageUrl && $this->isValidImageUrl($imageUrl)) {
            return $imageUrl;
        }

        return null;
    }

    /**
     * Extract image URL from HTML content
     */
    private function extractImageFromContent($content)
    {
        // Look for img tags in the content
        preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);
        
        if (isset($matches[1])) {
            $imageUrl = $matches[1];
            
            // Clean up the URL
            $imageUrl = html_entity_decode($imageUrl);
            
            // Check if it's a valid image URL
            if ($this->isValidImageUrl($imageUrl)) {
                return $imageUrl;
            }
        }
        
        return null;
    }

    /**
     * Check if a URL points to an image based on extension
     */
    private function isImageUrl($url)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        
        return in_array($extension, $imageExtensions);
    }

    /**
     * Validate if the image URL is accessible and valid
     */
    private function isValidImageUrl($url)
    {
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check if it's likely an image URL
        if (!$this->isImageUrl($url)) {
            // Also check for common image hosting patterns
            $imageHosts = ['imgur.com', 'cloudinary.com', 'amazonaws.com', 'wp.com'];
            $host = parse_url($url, PHP_URL_HOST);
            
            foreach ($imageHosts as $imageHost) {
                if (strpos($host, $imageHost) !== false) {
                    return true;
                }
            }
            
            return false;
        }

        return true;
    }

    /**
     * Attach all genders and age groups to the news item by default
     */
    private function attachDefaultSelections(NewsItem $newsItem)
    {
        $allGenders = UserGender::all();
        $allAgeGroups = AgeGroup::all();
        
        if ($allGenders->isNotEmpty()) {
            $newsItem->genders()->sync($allGenders->pluck('id')->toArray());
        }
        
        if ($allAgeGroups->isNotEmpty()) {
            $newsItem->ageGroups()->sync($allAgeGroups->pluck('id')->toArray());
        }
    }
}
