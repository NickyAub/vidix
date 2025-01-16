<?php

namespace App\Controller;

use App\Entity\Character;
use App\Entity\Movie;
use App\Form\CharacterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CharacterController extends AbstractController
{
    /**
     * Controller to create a character (orphan or related to a movie)
     *
     * @param Movie|null $movie
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/character/new/{id}', name: 'app.character.new', defaults: ['id' => null], methods: ['GET', 'POST'])]
    public function new(?Movie $movie, Request $request, EntityManagerInterface $manager, TranslatorInterface $translator): Response
    {
        $character = new Character();
        if ($movie) {
            $character->setMovie($movie);
        }

        $form = $this->createForm(CharacterType::class, $character);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $character = $form->getData();

            $manager->persist($character);
            $manager->flush();

            $this->addFlash('success', $translator->trans('Character successfully created'));

            $route = $this->redirectToRoute('app.movies');
            if ($movie) {
                $route = $this->redirectToRoute('app.movie.view', ['id' => $movie->getId()]);
            }
            return $route;
        }
        else if ($form->isSubmitted()) {
            $this->addFlash('error', $translator->trans('An error occurred during character creation'.($form->getErrors()->count() ? '<br>'.$form->getErrors()->__toString(): '')));
        }

        return $this->render('views/character/new.html.twig', [
            'character' => $character,
            'form' => $form->createView(),
            'is_submitted' => $form->isSubmitted(),
            'is_valid' => $form->isSubmitted() && $form->isValid(),
        ]);
    }

    /**
     * Controller to edit a character
     *
     * @param Character $character
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/character/edit/{id}', name: 'app.character.edit', methods: ['GET', 'POST'])]
    public function edit(Character $character, Request $request, EntityManagerInterface $manager, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(CharacterType::class, $character);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $character = $form->getData();

            $manager->persist($character);
            $manager->flush();

            $this->addFlash('success', $translator->trans('Character successfully modified'));

            return $this->redirectToRoute('app.movie.view', ['id' => $character->getMovie()->getId()]);
        }
        else if ($form->isSubmitted()) {
            $this->addFlash('error', $translator->trans('An error occurred during character edit').($form->getErrors()->count() ? '<br>'.$form->getErrors()->__toString(): ''));
        }

        return $this->render('views/character/edit.html.twig', [
            'character' => $character,
            'form' => $form->createView(),
            'is_submitted' => $form->isSubmitted(),
            'is_valid' => $form->isSubmitted() && $form->isValid(),
        ]);
    }
}
