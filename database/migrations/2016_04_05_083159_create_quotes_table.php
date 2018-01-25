<?php

use App\Quote;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('language', 2)->default('en')->index();
            $table->string('quote')->unique();
            $table->string('author', 200);
            $table->date('show_at')->nullable()->index();
        });

        $this->seed();

        $this->seedDe();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('quotes');
    }

    private function seed()
    {
        $page = 1;
        $pages = 1;

        $client = new Guzzle\Http\Client();
        $client->setBaseUrl('http://www.goodreads.com/quotes/tag');

        $total = 0;
        $max = 10;
        while ($page++ <= $pages) {
            if ($total >= $max) break;

            $body = file_get_contents($client->getBaseUrl() . '/life?page=' . $page);

            $crawler = new Symfony\Component\DomCrawler\Crawler();
            $crawler->addHtmlContent($body);

            $quotes = $crawler->filter('.leftContainer div.quoteText');

            $quotes->each(function ($node) use (&$total) {
                $parts = array_filter(explode(PHP_EOL, trim(strip_tags($node->text()))), function (&$value) {
                    $value = trim($value);

                    if (empty($value) || '―' == $value)
                        return false;

                    return $value;
                });
                $parts = array_map('trim', $parts);

                $quote = array_shift($parts);
                $author = array_shift($parts);

                Quote::create([
                    'language' => 'en',
                    'quote' => trim($quote),
                    'author' => trim($author, '., '),
                ]);

                $total++;
            });
        }
    }

    private function seedDe()
    {
        $quotes = [
            [
                'language' => 'de',
                'quote' => 'Gleiche Gemüter suchen sich.',
                'author' => 'Gemüter',
            ],
            [
                'language' => 'de',
                'quote' => 'Geteilte Freude ist doppelte Freude, geteilter Schmerz ist halber Schmerz.',
                'author' => 'Freude Schmerz',
            ],
            [
                'language' => 'de',
                'quote' => 'An den Früchten erkennt man den Baum.',
                'author' => 'Früchten',
            ],
            [
                'language' => 'de',
                'quote' => 'Andere Länder, andere Sitten.',
                'author' => 'Andere Länder',
            ],
            [
                'language' => 'de',
                'quote' => 'Anfangen ist leicht, beharren eine Kunst.',
                'author' => "Eine Kunst",
            ],
        ];
        foreach ($quotes as $quote) {
            Quote::create($quote);
        }
    }
}
