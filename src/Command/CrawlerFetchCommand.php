<?php

namespace App\Command;

use App\Entity\CrawlerLink;
use App\Service\StrokaKgPostParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CrawlerFetchCommand extends Command
{
    protected static $defaultName = 'app:crawler:fetch';
    protected $entityManager;
    protected $parser;

    public function __construct(ContainerInterface $container, StrokaKgPostParser $parser)
    {
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->parser = $parser;
        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setDescription('Вытягиваем по ссылкам посты');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = 1;
        $links = $this->entityManager->getRepository(CrawlerLink::class)->findBy(['processed' => 0], null, $limit);
        foreach ($links as $link) {
            $post = $this->parser->fetchPost($link->getUrl());
        }


        return 0;
    }
}
