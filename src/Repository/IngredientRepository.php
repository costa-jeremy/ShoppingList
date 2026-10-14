<?php

namespace App\Repository;

use App\Entity\Ingredient;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ingredient>
 *
 * @method Ingredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingredient[]    findAll()
 * @method Ingredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingredient::class);
    }

    /**
     * @return Ingredient[]
     */
    public function findByUserAndName(User $user, ?string $search): array
    {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.user = :user')
            ->setParameter('user', $user)
            ->orderBy('i.name', 'ASC');

        if ($search) {
            $qb->andWhere('LOWER(i.name) LIKE :search')
                ->setParameter('search', '%' . mb_strtolower(addcslashes($search, '%_')) . '%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Ingrédients de l'utilisateur dont le nom normalisé est identique à $name.
     * Avec $unit, seuls ceux de la même unité sont renvoyés (les vrais doublons) :
     * un même ingrédient peut exister dans plusieurs unités (ex. lait en ml et en L).
     *
     * @return Ingredient[]
     */
    public function findDuplicates(User $user, ?string $name, ?string $unit = null, ?int $excludeId = null): array
    {
        $normalized = Ingredient::normalizeName($name);
        if ($normalized === '') {
            return [];
        }

        return array_values(array_filter(
            $this->findBy(['user' => $user]),
            static fn (Ingredient $ingredient) => $ingredient->getId() !== $excludeId
                && ($unit === null || $ingredient->getUnit() === $unit)
                && Ingredient::normalizeName($ingredient->getName()) === $normalized
        ));
    }

    /**
     * Ids des ingrédients de l'utilisateur qui ont au moins un doublon (même nom normalisé et même unité).
     *
     * @return int[]
     */
    public function findDuplicateIds(User $user): array
    {
        $groups = [];
        foreach ($this->findBy(['user' => $user]) as $ingredient) {
            $groups[Ingredient::normalizeName($ingredient->getName()) . '|' . $ingredient->getUnit()][] = $ingredient->getId();
        }

        return array_merge([], ...array_values(array_filter($groups, static fn (array $ids) => count($ids) > 1)));
    }

    public function save(Ingredient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ingredient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
