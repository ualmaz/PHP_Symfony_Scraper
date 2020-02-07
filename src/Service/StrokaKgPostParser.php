<?php


namespace App\Service;


use App\Entity\Post;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class StrokaKgPostParser
{
    /**
     * @var Client
     */

    protected $client;

    /**
     * @var Crawler
     */

    protected $crawler;
        /**
         * @var Post
         */
    protected $post;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function fetchPost(string $url): Post
    {
        $this->post = new Post();
        $this->crawler = $this->client->request('GET', $url);
        $this->executeTextData('setTitle', '.topic-best-view-name');

        return $this->post;
    }

    protected function executeTextData(string $setter, string $selector): self
    {
        $node = $this->crawler->filter($selector);
        if ($node->count() > 0) {
            //$this->post->$setter($node->text());
            var_dump($node->text());
            call_user_func([$this->post, $setter], $node->text());
        }
        return $this;
    }
}