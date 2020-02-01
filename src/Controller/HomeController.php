<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class HomeController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route(path="", name="app_homepage")
     * @return \Symfony\Component\HttpFoundation\Response
    */
    public function homepage()
    {
        return $this->render('home/homepage.html.twig');
    }

    /**
     * @Route(path="/about", name="app_home_about")
    */
    public function about()
    {
        return $this->render('home/about.html.twig');
    }
}