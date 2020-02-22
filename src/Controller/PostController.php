<?php

namespace App\Controller;

use App\Entity\Post;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/post/{page}", name="app_post", defaults={"page": 1}, requirements={"page": "\d+"})
     */
    public function index(int $page)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository(Post::class)
            // -> find($id);                        # Находит только 1 запись по id.
            // -> findOneBy('id' => $id);           # Находит только 1 запись по критериях в массиве.
            // -> findBy(['field') => $value];      # Находит несколько записей по критериях в массиве.
            // -> findAll($id);                     # Получает все записи из БД.
            ->findBy([], null, 12, $page * 12 - 12);
        $lastPage = $entityManager->getRepository(Post::class)
            ->countPages(12);
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'page' => $page,
            'lastPage' => $lastPage
        ]);
    }
    /**
     * @Route(path="/single/{id}", name="app_post_single")
     */
    public function single(int $id): \Symfony\Component\HttpFoundation\Response
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);

        if (null === $post) {
            $this->createNotFoundException();
        }

        return $this->render('post/single.html.twig', ['post' => $post]);
    }

//    /**
//     * @Route(path="/post/search" name="app_post_search")
//     */
    public function search(Request $request): Response
    {
        return $this->render('post/search.html.twig');
    }
}
