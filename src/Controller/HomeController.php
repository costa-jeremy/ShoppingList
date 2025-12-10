<?php
namespace App\Controller;

use App\Repository\ShoppingListRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ShoppingListRepository $shoppingListRepository, RecipeRepository $recipeRepository): Response
    {
        $user = $this->getUser();

        // Récupérer les 5 dernières listes de courses
        $recentShoppingLists = $shoppingListRepository->createQueryBuilder('s')
            ->where('s.user = :user')
            ->setParameter('user', $user)
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Récupérer les 5 dernières recettes
        $recentRecipes = $recipeRepository->createQueryBuilder('r')
            ->where('r.user = :user')
            ->setParameter('user', $user)
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->render('home/index.html.twig', [
            'recentShoppingLists' => $recentShoppingLists,
            'recentRecipes' => $recentRecipes,
        ]);
    }
}
