<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    #[Route('/movie', name: 'app.movie')]
    public function index(MovieRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $movies = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
    
        return $this->render('views/movie/index.html.twig', [
            'movies' => $movies,
        ]);
    }
}
