<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, 
    private PaginatorInterface $paginatorInterface
    ) {
        parent::__construct($registry, Post::class);
    }

    /**
     * Get published posts
     * 
     * @param int $page
     * @return PaginationInterface
     */
    public function findPublished(int $page) : PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
            ->where('p.state LIKE :state')
            ->setParameter('state', '%STATE_PUBLISHED')
            ->addOrderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

            $posts = $this->paginatorInterface->paginate($data, $page, 9);
            return $posts;
    }
}