<?php

namespace App\Repositories;

use App\NewsFeed;
use App\Services\FeedReader;
use Cache;
use Carbon\Carbon;
use Faker\Provider\DateTime;
use Guzzle\Http\Message\Request;
use Illuminate\Support\Collection;
use Zend\Feed\Reader\Reader;
use Log;

class FeedsRepository
{
    public function feeds($lang, $me)
    {
        return NewsFeed::where('user_id', $me->id)->orderBy('name')->get(['id', 'name']);
    }

    public function news(array $feeds = [], $take = 1000)
    {
        $collection = NewsFeed::whereIn('id', $feeds)->get(['url']);

        $news = Collection::make([]);
        foreach ($collection as $feed) {
            $news = $news->merge($this->parse($feed));
        }

//        $news->sort(function ($a, $b) {
//            return $a['pubDate']->lt($b['pubDate']);
//        });

        return $news->take($take);
    }

    protected function parse(NewsFeed $feed)
    {
//        $cacheKey = md5($feed->url);

//        Cache::forget($cacheKey);

//        return Cache::remember($cacheKey, 20, function () use ($feed) {
            $reader = Reader::importString(
                file_get_contents($feed->url)
            );
            $data = [];
            foreach ($reader as $key => $item) {
                if($item->getTitle()) {
                    array_push($data, [
                        'title' => $item->getTitle(),
                        'link' => $item->getLink(),
                        'content' => strip_tags(html_entity_decode($item->getContent())),
                        'enclosure' => $item->getEnclosure(),
//                    'pubDate' => $item->getDateModified() ? Carbon::parse(strtotime($item->getDateModified())->format('Y-m-d H:i:s')) : Carbon::today(),
                        'pubDate' => $item->getDateModified(),
                        'media' => get_rss_media($item, $key + 1),
                    ]);
                }

            }

            return $data;
//        });
    }
}