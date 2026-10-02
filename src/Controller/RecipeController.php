<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\Voter\OwnerVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recipe')]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'app_recipe_index', methods: ['GET'])]
    public function index(Request $request, RecipeRepository $recipeRepository): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $order = $request->query->get('order') === 'oldest' ? 'oldest' : 'newest';

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipeRepository->findByUserAndName($this->getUser(), $search, $order === 'oldest' ? 'ASC' : 'DESC'),
            'search' => $search,
            'order' => $order,
        ]);
    }

    #[Route('/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = new Recipe();
        $recipe->setUser($this->getUser());
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recipe);
            $entityManager->flush();

            return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_show', methods: ['GET'])]
    public function show(Recipe $recipe): Response
    {
        $this->denyAccessUnlessGranted(OwnerVoter::OWNER, $recipe);

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(OwnerVoter::OWNER, $recipe);

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_delete', methods: ['POST'])]
    public function delete(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(OwnerVoter::OWNER, $recipe);

        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($recipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/mark-as-done', name: 'app_recipe_mark_as_done', methods: ['POST'])]
    public function markAsDone(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(OwnerVoter::OWNER, $recipe);

        if ($this->isCsrfTokenValid('mark_done'.$recipe->getId(), $request->request->get('_token'))) {
            $recipe->markAsDone();
            $entityManager->flush();

            $this->addFlash('success', sprintf('Bravo ! La recette "%s" a été marquée comme réalisée. C\'est la %de fois !',
                $recipe->getName(),
                $recipe->getNumberOfTimes()
            ));
        }

        return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/number-of-times', name: 'app_recipe_update_number_of_times', methods: ['POST'])]
    public function updateNumberOfTimes(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(OwnerVoter::OWNER, $recipe);

        if ($this->isCsrfTokenValid('number_of_times'.$recipe->getId(), $request->request->get('_token'))) {
            $numberOfTimes = filter_var($request->request->get('numberOfTimes'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

            if ($numberOfTimes === false) {
                $this->addFlash('danger', 'Le nombre de réalisations doit être un entier positif ou nul.');
            } else {
                $recipe->setNumberOfTimes($numberOfTimes);
                // Une recette jamais réalisée n'a pas de date de dernière préparation
                if ($numberOfTimes === 0) {
                    $recipe->setLastTimeDone(null);
                }
                $entityManager->flush();

                $this->addFlash('success', sprintf('Le nombre de réalisations de "%s" est maintenant de %d.', $recipe->getName(), $numberOfTimes));
            }
        }

        return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()], Response::HTTP_SEE_OTHER);
    }
}
