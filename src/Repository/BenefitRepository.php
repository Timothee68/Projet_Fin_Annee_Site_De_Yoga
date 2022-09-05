<?php

namespace App\Repository;

use App\Entity\Benefit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Benefit>
 *
 * @method Benefit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Benefit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Benefit[]    findAll()
 * @method Benefit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BenefitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Benefit::class);
    }

    public function add(Benefit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Benefit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     // fonction pour retrouver les stagiaires qui ne sont pas dans la session en cour 
     public function findInputNotInBenefit(int $id)
     {
         // on ce connecte a l'entité 
         $em = $this->getEntityManager();
         //on crée une requête que $sub prend pour valeur 
         $sub = $em->createQueryBuilder();
         // $qb prend la meme valeur que $sub pour faire une sous requête préparer
         $qb = $sub; 
         // on fait une première requête qui dit select all from benefit left join intern_session where id = id envoyer
         // on récupère donc tout les stagiaire qui sont dans la session 
             $qb->select('i')
                 ->from('App\Entity\Input', 'i')
                 // ici on passe par sessions délcarer dans l'entité benefit pour faire la liaison via la relation ManyToMany 
                 ->leftJoin('i.benefits', 's')
                 ->where('s.id = :id');
                 
             $sub = $em->createQueryBuilder();
             // la sous requete reprend la selection il faut changer l'alias sinon il y a un soucis de compréhension 
             //on selectionne tout les stagiaires 
             // select all from benefit 
             $sub->select('e')
                 ->from('App\Entity\Input', 'e')     
                 // where ( on récupère tout les stagiaires ) not in ( 1er requête qui contient ceux dans la session )         
                 ->where($sub->expr()->notIn('e.id', $qb->getDQL()))
                 // on prend en paramêtre de comparaison les id 
                 ->setParameter('id',$id)
                 // on ordonne par nom 
                 ->orderBy('e.description');
 
         $query = $sub->getQuery() ;
         return $query->getResult();       
     }

//    /**
//     * @return Benefit[] Returns an array of Benefit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Benefit
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
