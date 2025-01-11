<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    #[Route('/movie', name: 'app.movie')]
    public function index(MovieRepository $repository): Response
    {
        $movies = $repository->findAll();
        return $this->render('views/movie/index.html.twig', [
            'movies' => $movies,
        ]);
    }
}
