<?php

namespace App\Command;

use App\Entity\CrawlerLink;
use http\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerStartCommand extends Command
{
    protected static $defaultName = 'app:crawler:start';

    protected $entityManager;

    public function __construct(ContainerInterface $container)
    {
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setDescription('Запускает основной процесс сбора данных');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $client = new \Goutte\Client();

        $this->entityManager->getRepository(CrawlerLink::class)->emptyTable();

        for ($page =0; $page < 5; $page++) {
            $mainUrl = 'https://stroka.kg/kupit-kvartiru/?p=' . $page;
            $this->getUrlFromPage($client, $mainUrl);

        }


        $io->success('Сбор данных успешно исполнен');

        return 0;
    }

    protected function getUrlFromPage($client, $url)
    {
        $crawler = $client->request('GET', $url);
        $crawler->filter('a.topics-item-view')->each(function (Crawler $node){
            $link = $node->attr('href');
            $newCrawlerLink = new CrawlerLink();
            $newCrawlerLink->setUrl($link);

            $this->entityManager->persist($newCrawlerLink);
        });

        $this->entityManager->flush();
    }
}
