<?php

namespace App\Repository;

use App\Entity\ProductData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProductData|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductData|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductData[]    findAll()
 * @method ProductData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductDataRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProductData::class);
    }
}
