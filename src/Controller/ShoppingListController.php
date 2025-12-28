<?php

namespace App\Controller;

use App\Entity\ShoppingList;
use App\Entity\ShoppingListIngredientAdjustment;
use App\Form\ShoppingListType;
use App\Repository\IngredientRepository;
use App\Repository\ShoppingListRepository;
use App\Repository\ShoppingListIngredientAdjustmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shopping/list')]
class ShoppingListController extends AbstractController
{
    #[Route('/', name: 'app_shopping_list_index', methods: ['GET'])]
    public function index(ShoppingListRepository $shoppingListRepository): Response
    {
        return $this->render('shopping_list/index.html.twig', [
            'shopping_lists' => $shoppingListRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'app_shopping_list_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $shoppingList = new ShoppingList();
        $shoppingList->setUser($this->getUser());
        $form = $this->createForm(ShoppingListType::class, $shoppingList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($shoppingList);
            $entityManager->flush();

            $this->addFlash('success', 'La liste de courses a été créée avec succès.');

            return $this->redirectToRoute('app_shopping_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shopping_list/new.html.twig', [
            'shopping_list' => $shoppingList,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_shopping_list_show', methods: ['GET'])]
    public function show(ShoppingList $shoppingList): Response
    {
        return $this->render('shopping_list/show.html.twig', [
            'shopping_list' => $shoppingList,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_shopping_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ShoppingList $shoppingList, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(ShoppingListType::class, $shoppingList);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La liste de courses a été modifiée avec succès.');

            return $this->redirectToRoute('app_shopping_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shopping_list/edit.html.twig', [
            'shopping_list' => $shoppingList,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_shopping_list_delete', methods: ['POST'])]
    public function delete(Request $request, ShoppingList $shoppingList, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shoppingList->getId(), $request->request->get('_token'))) {
            $entityManager->remove($shoppingList);
            $entityManager->flush();

            $this->addFlash('success', 'La liste de courses a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_shopping_list_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('{id}/download-pdf', name: 'app_shopping_list_download_pdf', methods: ['GET'])]
    public function downloadPdf(ShoppingList $shoppingList, Pdf $pdf)
    {
        $html = $this->renderView('shopping_list/pdf.html.twig', ['shoppingList' => $shoppingList]);

        // Créer un nom de fichier basé sur le nom de la liste ou la date
        $listName = $shoppingList->getName()
            ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $shoppingList->getName())
            : 'liste_courses';
        $filename = sprintf('%s_%s.pdf', $listName, date('Y-m-d'));

        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    #[Route('/{id}/adjust-ingredient', name: 'app_shopping_list_adjust_ingredient', methods: ['POST'])]
    public function adjustIngredient(
        Request $request,
        ShoppingList $shoppingList,
        IngredientRepository $ingredientRepository,
        ShoppingListIngredientAdjustmentRepository $adjustmentRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $ingredientId = $data['ingredientId'] ?? null;
        $customItemId = $data['customItemId'] ?? null;
        $quantity = $data['quantity'] ?? null;
        $isHidden = $data['isHidden'] ?? false;
        $isInCart = $data['inCart'] ?? null;

        if (!$ingredientId) {
            // Si ingredientId absent mais customItemId présent, c'est pour un article libre
            if (!$customItemId) {
                return new JsonResponse(['error' => 'Ingredient ID ou customItemId manquant'], 400);
            }
        }

        // Si customItemId fourni, traiter l'article libre
        if ($customItemId) {
            $customItem = $entityManager->getRepository(\App\Entity\ShoppingListItem::class)->find($customItemId);
            if (!$customItem) {
                return new JsonResponse(['error' => 'Article libre non trouvé'], 404);
            }

            if ($isInCart !== null) {
                $customItem->setIsInCart((bool)$isInCart);
                $entityManager->persist($customItem);
                $entityManager->flush();
                return new JsonResponse(['success' => true]);
            }

            return new JsonResponse(['success' => false, 'error' => 'Aucune donnée à mettre à jour pour l\'article libre']);
        }

        $ingredient = $ingredientRepository->find($ingredientId);
        if (!$ingredient) {
            return new JsonResponse(['error' => 'Ingrédient non trouvé'], 404);
        }

        // Chercher ou créer l'ajustement
        $adjustment = $adjustmentRepository->findOneBy([
            'shoppingList' => $shoppingList,
            'ingredient' => $ingredient
        ]);

        if (!$adjustment) {
            $adjustment = new ShoppingListIngredientAdjustment();
            $adjustment->setShoppingList($shoppingList);
            $adjustment->setIngredient($ingredient);
        }

        // Si quantity est fournie, c'est une modification de quantité
        if ($quantity !== null) {
            $adjustment->setAdjustedQuantity((float)$quantity);
        }

        // Si isHidden est true, on masque l'ingrédient
        $adjustment->setIsHidden($isHidden);

        // Si inCart est fourni, on met à jour l'état dans le réglage
        if ($isInCart !== null) {
            $adjustment->setIsInCart((bool)$isInCart);
        }

        // Si on dé-masque ET qu'il n'y a pas de quantité ajustée ni d'état "inCart", on supprime l'ajustement
        if (!$isHidden && $adjustment->getAdjustedQuantity() === null && !$adjustment->isInCart() && $adjustment->getId()) {
            $entityManager->remove($adjustment);
        } else {
            $entityManager->persist($adjustment);
        }

        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

}
