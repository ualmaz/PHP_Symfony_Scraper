<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    /**
     * @Route("/cron", name="cron")
     */
    public function index()
    {
        return $this->render('cron/index.html.twig', [
            'controller_name' => 'CronController',
        ]);
    }
}
