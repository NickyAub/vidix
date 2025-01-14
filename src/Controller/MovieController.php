<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\DependencyInjection\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class MovieController extends AbstractController
{
    /**
     * Controller to see the list of available movies
     *
     * @param MovieRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/movies', name: 'app.movies', methods: ['GET'])]
    public function index(MovieRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, []);
        $filter_field_name = $config['default_options']['filter_field_name'];
        $filter_value_name = $config['default_options']['filter_value_name'];
        $filter = [
            'field' => $request->query->get($filter_field_name),
            'value' => $request->query->get($filter_value_name),
        ];

        $movies = $paginator->paginate(
            $repository->findAllFiltered($filter),
            $request->query->getInt('page', 1),
            10
        );
    
        return $this->render('views/movie/index.html.twig', [
            'movies' => $movies,
            'filter' => $filter,
        ]);
    }

    /**
     * Controller to create a movie
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/movie/new', name: 'app.movie.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager, TranslatorInterface $translator): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movie = $form->getData();

            $characters = $movie->getCharacters();
            foreach($characters as $character) {
                $character->setMovie($movie);
                $manager->persist($character);
            }

            $manager->persist($movie);
            $manager->flush();

            $this->addFlash('success', $translator->trans('Movie successfully added'));

            return $this->redirectToRoute('app.movies');
        }

        return $this->render('views/movie/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Controller to edit movie details
     *
     * @param Movie $movie
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/movie/edit/{id}', name: 'app.movie.edit', methods: ['GET', 'POST'])]
    public function edit(Movie $movie, Request $request, EntityManagerInterface $manager, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movie = $form->getData();

            $characters = $movie->getCharacters();
            foreach($characters as $character) {
                $character->setMovie($movie);
                $manager->persist($character);
            }

            $manager->persist($movie);
            $manager->flush();

            $this->addFlash('success', $translator->trans('Movie successfully modified'));

            return $this->redirectToRoute('app.movie.view', ['id' => $movie->getId()]);
        }
        // else if ($form->isSubmitted()) {
        //     $this->addFlash('error', $translator->trans('An error occurred during movie edit'));

        //     return $this->redirectToRoute('app.movie.edit', ['id' => $movie->getId()]);
        // }

        return $this->render('views/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Controller to delete a movie
     *
     * @param Movie $movie
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/movie/delete/{id}', name: 'app.movie.delete', methods: ['GET'])]
    public function delete(Movie $movie, EntityManagerInterface $manager, TranslatorInterface $translator): Response
    {
        $manager->remove($movie);
        $manager->flush();

        $this->addFlash('success', $translator->trans('Movie successfully removed'));

        return $this->redirectToRoute('app.movies');
    }

    /**
     * Controller to view movie details
     *
     * @param Movie $movie
     * @return Response
     */
    #[Route('/movie/{id}', name: 'app.movie.view', methods: ['GET'])]
    public function view(Movie $movie): Response
    {
        return $this->render('views/movie/view.html.twig', [
            'movie' => $movie,
        ]);
    }
}
