<?php


namespace App\Service;


use App\Entity\Post;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Constraints\Date;

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

    public function fetchPost(string $url, Post $post): Post
    {
        $this->post = $post;
        $this->crawler = $this->client->request('GET', $url);
        $this
            ->executeTextData('setTitle', '.topic-best-view-name')
            ->executeTextData('SetPrice', '.topic-view-best-topic_cost')
            ->executeArrayData('setPhones', '.topic-view-best-phone')
            ->executeTextData('setRoom', '.topic-view-best-topic_rooms')
            ->executeTextData('SetSerias', '.topic-view-best-topic_series')
            ->executeTextData('SetArea', '.topic-view-best-topic_area')
            ->executeTextData('SetWalls', '.topic-view-best-topic_walls')
            ->executeTextData('SetFloor', '.topic-view-best-topic_floor')
            ->executeTextData('SetFloorOf', '.topic-view-best-topic_floor_of')
            ->executeArrayData('SetOptions', '.topic-view-best-rows-item-all')
            ->executeTextData('SetDescription', '.topic-view-body', true)
            ->executeImageData('SetThumbnail')
            ->executeGeoData('setGeoloaction')
            ->executeDateData('setCreatedAt', '.topic-view-topic_date_create_row')
            ->executeDateData('setUpdatedAt', '.topic-view-topic_date_up')
            ->setUrl($url)
        ;
//    dump($this->post);

        return $this->post;
    }

    protected function executeTextData(string $setter, string $selector, bool $html = false): self
    {
        $node = $this->crawler->filter($selector);
        if ($node->count() > 0) {
            //$this->post->$setter($node->text());
            call_user_func(
                [$this->post, $setter],
                $html ? $node->html(): $node->text()
            );
        }
        return $this;
    }

    protected function executeArrayData(string $setter, string $selector): self
    {
        $data = [];
        $node = $this->crawler->filter($selector);
        $node->each(function (Crawler $current) use (&$data) {
            array_push($data, $current->text());
        });
        call_user_func([$this->post, $setter], $data);

        return $this;
    }

    protected function executeImageData(string $setter): self
    {
        $images = $this->crawler->filter('.topic-best-view-images-image');
        if ($images->count() > 0) {
            $imageStyle = $images->first()->attr('style');
//            dump($imageStyle);
            if (preg_match('/https:\/\/[\w.\/]+/', $imageStyle, $matches)) {
                call_user_func([$this->post, $setter], $matches[0]);
            }

        }
        return $this;
    }

    protected function executeGeoData(string $setter): self
    {
        $map = $this->crawler->filter('.topic-best-view-map-frame');
        if ($map->count() > 0) {
            $marker = [
                'lat'   => $map->attr('data-a'),
                'long'  => $map->attr('data-f')
            ];
            call_user_func([$this->post, $setter], $marker);
        }

        return $this;
    }

    protected function executeDateData(string $setter, string $selector): self
    {
        $node = $this->crawler->filter($selector);
        if ($node->count() > 0) {
            $dateText = $node->text();
            $dateText = substr($dateText, -10);
            call_user_func([$this->post, $setter], new \DateTime($dateText));
        }
        return $this;
    }

    protected function setUrl(string $url): self
    {
        $this->post->setOriginalUrl($url);

        return $this;
    }

}