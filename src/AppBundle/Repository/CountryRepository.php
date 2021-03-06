<?php

namespace AppBundle\Repository;

/**
 * CountryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CountryRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllCountry(){
        return $this->getEntityManager()
            ->createQueryBuilder('c')
            ->select('c.id,c.countryName, c.countryCode, c.countryCurrency, c.countryCurrencyShort')
            ->from('AppBundle:Country', 'c')
            ->getQuery()
            ->getResult();
    }
}
