<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Test;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class QuestionRepository extends EntityRepository
{
    /**
     * @param Test $test
     */
    public function getRandomQuestions($test)
    {
        $query = "SELECT q.id FROM AppBundle:Question as q WHERE q.test = :test";

        $manager = $this->getEntityManager()->createQuery($query);
        $manager->setParameter("test", $test);
        $res = $manager->execute(null, Query::HYDRATE_SCALAR);

        shuffle($res);
        $res = array_slice($res, 0, $test->getQuestionsLimit());

        $result = array();
        foreach ($res as $r) {
            $result[]=$r['id'];
        }

        return $result;
    }
}
