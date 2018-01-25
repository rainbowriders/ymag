<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->default(null);
            $table->string('language', 2)->nullable()->default(null);
            $table->string('name', 100)->index();
            $table->string('url', 255);
            $table->string('categories')->nullable();
            $table->timestamps();
        });

        $this->seed();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected function seed()
    {
        DB::table('news_feeds')->truncate();

        foreach ($this->feeds() as $feed) {
            \App\NewsFeed::create($feed);
        }
    }

    private function feeds()
    {
        $feeds = [
            [
                'language' => 'en',
                'name' => 'BBC World News',
                'url' => 'http://feeds.bbci.co.uk/news/world/rss.xml',
                'categories' => 'world news',
            ],
            [
                'language' => 'en',
                'name' => 'BBC BBC Politics',
                'url' => 'http://feeds.bbci.co.uk/news/politics/rss.xml',
                'categories' => 'politics',
            ],
            [
                'language' => 'en',
                'name' => 'BBC Technology',
                'url' => 'http://feeds.bbci.co.uk/news/video_and_audio/technology/rss.xml',
                'categories' => 'technology',
            ],
            [
                'language' => 'en',
                'name' => 'BBC Health',
                'url' => 'http://feeds.bbci.co.uk/news/health/rss.xml',
                'categories' => 'health',
            ],
            [
                'language' => 'en',
                'name' => 'BBC Entertainment & Arts',
                'url' => 'http://feeds.bbci.co.uk/news/entertainment_and_arts/rss.xml',
                'categories' => 'entertainment,arts',
            ],
            [
                'language' => 'en',
                'name' => 'NY Post',
                'url' => 'http://nypost.com/news/feed/',
                'categories' => 'news',
            ],
            [
                'language' => 'en',
                'name' => 'NY Times',
                'url' => 'http://rss.nytimes.com/services/xml/rss/nyt/Baseball.xml',
                'categories' => 'sports, baseball',
            ],
            [
                'language' => 'en',
                'name' => 'UEFA Champions League',
                'url' => 'http://www.uefa.com/rssfeed/uefachampionsleague/rss.xml',
                'categories' => 'sports, football',
            ],
            [
                'language' => 'de',
                'name' => 'RTL News',
                'url' => 'http://www.rtlnieuws.nl/service/rss/nieuws/index.xml',
                'categories' => 'news',
            ],
            [
                'name' => 'RTL Sport',
                'url' => 'http://www.rtlnieuws.nl/service/rss/sport/algemeen/index.xml',
                'language' => 'de',
                'categories' => 'sport',
            ],
            [
                'name' => 'RTL Video',
                'url' => 'http://www.rtlnieuws.nl/service/rss/uitzendingen/index.xml',
                'language' => 'de',
                'categories' => 'video',
            ],
            [
                'name' => 'ARD Home',
                'url' => 'http://www.ard.de/home/ard/ARD_Startseite/21920/index.xml',
                'language' => 'de',
                'categories' => 'news',
            ],
            [
                'name' => 'ARD Culture',
                'url' => 'http://www.ard.de/home/kultur/ARD_Kultur/63274/index.xml',
                'language' => 'de',
                'categories' => 'culture',
            ],
        ];

        return $feeds;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('news_feeds');
    }
}
