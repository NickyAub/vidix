<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    #[Route('/movies', name: 'app.movies', methods: ['GET'])]
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

    #[Route('/movie/{id}', name: 'app.movie.view', methods: ['GET'])]
    public function view(Movie $movie): Response
    {
        return $this->render('views/movie/view.html.twig', [
            'movie' => $movie,
        ]);
    }
}
