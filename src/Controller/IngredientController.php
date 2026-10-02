<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use App\Security\Voter\OwnerVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/ingredient')]
class IngredientController extends AbstractController
{
    #[Route('/', name: 'app_ingredient_index', methods: ['GET'])]
    public function index(Request $request, IngredientRepository $ingredientRepository): Response
    {
        $search = trim((string) $request->query->get('q', ''));

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredientRepository->findByUserAndName($this->getUser(), $search),
            'duplicate_ids' => $ingredientRepository->findDuplicateIds($this->getUser()),
            'search' => $search,
        ]);
    }

    #[Route('/check-duplicate', name: 'app_ingredient_check_duplicate', methods: ['GET'])]
    public function checkDuplicate(Request $request, IngredientRepository $ingredientRepository): JsonResponse
    {
        $excludeId = $request->query->getInt('exclude') ?: null;
        $duplicates = $ingredientRepository->findDuplicates($this->getUser(), $request->query->get('name'), $excludeId);

        return $this->json(array_map(static fn (Ingredient $ingredient) => [
            'id' => $ingredient->getId(),
            'name' => $ingredient->getName(),
            'unit' => $ingredient->getUnit(),
        ], $duplicates));
    }

    /**
     * Création d'un ingrédient en AJAX depuis le formulaire de recette.
     */
    #[Route('/quick-create', name: 'app_ingredient_quick_create', methods: ['POST'])]
    public function quickCreate(Request $request, EntityManagerInterface $entityManager, IngredientRepository $ingredientRepository): JsonResponse
    {
        if (!$this->isCsrfTokenValid('quick_create_ingredient', $request->headers->get('X-CSRF-Token'))) {
            return $this->json(['error' => 'Jeton de sécurité invalide, rechargez la page.'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $name = trim((string) ($data['name'] ?? ''));
        $unit = (string) ($data['unit'] ?? '');

        if ($name === '' || mb_strlen($name) > 255) {
            return $this->json(['error' => 'Le nom de l\'ingrédient est obligatoire (255 caractères maximum).'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (!in_array($unit, IngredientType::UNITS, true)) {
            return $this->json(['error' => 'Veuillez choisir une unité.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $duplicates = $ingredientRepository->findDuplicates($this->getUser(), $name);
        if ($duplicates) {
            $existing = $duplicates[0];

            return $this->json([
                'error' => sprintf('Un ingrédient similaire existe déjà : « %s ».', $existing->getName()),
                'existing' => ['id' => $existing->getId(), 'name' => $existing->getName(), 'unit' => $existing->getUnit()],
            ], Response::HTTP_CONFLICT);
        }

        $ingredient = new Ingredient();
        $ingredient->setUser($this->getUser());
        $ingredient->setName($name);
        $ingredient->setUnit($unit);
        $entityManager->persist($ingredient);
        $entityManager->flush();

        return $this->json([
            'id' => $ingredient->getId(),
            'name' => $ingredient->getName(),
            'unit' => $ingredient->getUnit(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/new', name: 'app_ingredient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, IngredientRepository $ingredientRepository): Response
    {
        $ingredient = new Ingredient();
        $ingredient->setUser($this->getUser());
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !$this->rejectDuplicate($form, $ingredient, $ingredientRepository)) {
            $entityManager->persist($ingredient);
            $entityManager->flush();

            $this->addFlash('success', 'L\'ingrédient a été ajouté avec succès.');
            return $this->redirectToRoute('app_ingredient_index');
        }

        return $this->render('ingredient/new.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ingredient_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ingredient $ingredient, EntityManagerInterface $entityManager, IngredientRepository $ingredientRepository): Response
    {
        $this->denyAccessUnlessGranted(OwnerVoter::OWNER, $ingredient);

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !$this->rejectDuplicate($form, $ingredient, $ingredientRepository)) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'ingrédient a été modifié avec succès.');
            return $this->redirectToRoute('app_ingredient_index');
        }

        return $this->render('ingredient/edit.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_ingredient_delete', methods: ['POST'])]
    public function delete(Request $request, Ingredient $ingredient, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(OwnerVoter::OWNER, $ingredient);

        if ($this->isCsrfTokenValid('delete'.$ingredient->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ingredient);
            $entityManager->flush();

            $this->addFlash('success', 'L\'ingrédient a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_ingredient_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/api', name: 'app_ingredient_api', methods: ['GET'])]
    public function getIngredientData(Ingredient $ingredient): Response
    {
        $this->denyAccessUnlessGranted(OwnerVoter::OWNER, $ingredient);

        return $this->json([
            'id' => $ingredient->getId(),
            'name' => $ingredient->getName(),
            'unit' => $ingredient->getUnit(),
        ]);
    }

    /**
     * Ajoute une erreur sur le champ nom si un ingrédient équivalent existe déjà.
     */
    private function rejectDuplicate(FormInterface $form, Ingredient $ingredient, IngredientRepository $ingredientRepository): bool
    {
        $duplicates = $ingredientRepository->findDuplicates($this->getUser(), $ingredient->getName(), $ingredient->getId());
        if (!$duplicates) {
            return false;
        }

        $form->get('name')->addError(new FormError(sprintf(
            'Un ingrédient similaire existe déjà : « %s ».',
            $duplicates[0]->getName()
        )));

        return true;
    }
}
