<?php

namespace App\Repository;

use App\Entity\Benefit;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function add(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function foundSession($id)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.benefit' , 'b')
            ->andWhere('b.id = :id')
            ->setParameter('id' , $id)
            ->getQuery()
            ->getResult();
    }

//  a modifier pour adapter a mon projet  crée des boutton pour choisir la date et fair le changement
// ajouter dans un controller
// $pastSessions = $sr->findPastSession();
// $progressSessions = $sr->findProgressSession();
// $futurSessions = $sr->findFuturSession();
// return $this->render('home/index.html.twig', [
//     'pastSessions' => $pastSessions,
//     'progressSessions' => $progressSessions,
//     'futurSessions' => $futurSessions,
// ]);
// }
        // fonction DQL pour récupérer les dates de fin de session entérieur a la date actuel
        public function findPastSession()
        {
            $now = new \DateTime();
            return $this->createQueryBuilder('s')
                        ->andWhere('s.dateEnd < :val')
                        ->setParameter('val',$now)
                        ->orderBy('s.dateStart', 'ASC')
                        ->getQuery()
                        ->getResult()
                        ;
        }
        // fonction DQL pour récupérer les dates de debut de session supérieur a la date actuel
        public function findFuturSession()
        {
            $now = new \DateTime();
            return $this->createQueryBuilder('s')
                        ->andWhere('s.dateStart > :val')
                        ->setParameter('val',$now)
                        ->orderBy('s.dateStart', 'ASC')
                        ->getQuery()
                        ->getResult()
                        ;
        }
        // fonction DQL pour récupérer les dates de debut de session inférieur et dont la date de fin est supérieur a la date actuel
        public function findProgressSession()
        {
            $now = new \DateTime();
            return $this->createQueryBuilder('s')
                        ->andWhere('s.dateStart < :val AND s.dateEnd > :val')
                        ->setParameter('val', $now)
                        ->orderBy('s.dateStart', 'ASC')
                        ->getQuery()
                        ->getResult()
                        ;
        }
    
//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
