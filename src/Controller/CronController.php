<?php

namespace App\Controller;

use App\Command\CrawlerStartCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    /**
     * @Route("/cron", name="app_cron")
     */
    public function index(KernelInterface $kernel)
    {
        $app = new Application($kernel);
        $app->setAutoExit(false);
        $input = new ArrayInput([
            'command' => CrawlerStartCommand::getDefaultName()
        ]);
        $output = new BufferedOutput();
        $app->run($input, $output);
        $content = $output->fetch();

        return new Response($content);
    }
}
