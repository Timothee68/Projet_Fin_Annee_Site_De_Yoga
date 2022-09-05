<?php

namespace App\Repository;

use App\Entity\ImgCollectionBenefit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImgCollectionBenefit>
 *
 * @method ImgCollectionBenefit|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImgCollectionBenefit|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImgCollectionBenefit[]    findAll()
 * @method ImgCollectionBenefit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImgCollectionBenefitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImgCollectionBenefit::class);
    }

    public function add(ImgCollectionBenefit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ImgCollectionBenefit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findById($id): array
    {
     $entityManager = $this->getEntityManager();
     $sub =  $entityManager->createQueryBuilder();
         $sub->select('i.img , i.alt')
             ->from('App\Entity\ImgCollectionBenefit', 'i')
             ->innerJoin('App\entity\Benefit' , 'b')
             ->where('b.id = :id')
             ->setParameter('id',$id)
             ->orderBy('i.img');
         //    ->setMaxResults(10)
         $query = $sub->getQuery() ;
         return $query->getResult();  
        ;
    }

//    public function findOneBySomeField($value): ?ImgCollectionBenefit
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
