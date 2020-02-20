<?php

namespace App\Command;

use App\Entity\CrawlerLink;
use App\Entity\Post;
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
        $limit = 100;

        $this->entityManager
            ->getRepository(Post::class)
            ->preparePostsBeforeParsing();


        do {
            /** @var CrawlerLink[] $links */
            $links = $this->entityManager->getRepository(CrawlerLink::class)->findBy(['processed' => 0], null, $limit);
            foreach ($links as $link) {
                $post = $this->entityManager
                    ->getRepository(Post::class)
                    ->findOneBy(['originalUrl' => $link->getUrl()]);

                if (!$post) {
                    $post = new Post();
                    $this->entityManager->persist($post);
                    $io->writeln('Создали новый пост по ссылке: ' . $link->getUrl());
                } else {
                    $io->writeln('Обновили пост по ссылке: ' . $link->getUrl());
                }

                $this->parser->fetchPost($link->getUrl(), $post);
                $post->setProcessed(true);
                $link->setProcessed(true);

                $this->entityManager->flush();


            }
        } while (count($links) > 0);

        $this->entityManager->getRepository(Post::class)->deleteOldPosts();

        return 0;
    }
}
