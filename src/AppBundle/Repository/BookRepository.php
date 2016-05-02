<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * BookInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BookRepository extends EntityRepository
{

    function getBookAndDealImages($bookId){

        return $this->getEntityManager()
            ->createQueryBuilder('b')
            ->select('b.bookImage ,bdi.imageUrl')

            ->from('AppBundle:Book', 'b')
            ->innerJoin('AppBundle:BookDeal', 'bd', 'WITH', 'b.id = bd.book')
            ->innerJoin('AppBundle:BookDealImage', 'bdi', 'WITH', 'bd.id = bdi.bookDeal')
            ->andwhere('b.id = :bookId')

            ->andwhere('bd.bookStatus = ' . "'Activated'")
            ->andwhere('bd.bookSellingStatus = ' . "'Selling'")
            ->setParameter('bookId', $bookId)
            ->getQuery()
            ->getResult();

    }
}
