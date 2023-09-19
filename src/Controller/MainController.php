<?php
// src/Controller/MainController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;


class MainController extends AbstractController {

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'main')]
    public function index(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        return $this->render('main/main.html.twig', [
            'products' => $products,
            'largest_stock' => $this->getLargestStockProduct(),
            'best_selling' => $this->bestSellingProduct(),
        ]);
    }

    public function getLargestStockProduct() {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('p.name AS name, p.reference AS reference, MAX(p.stock) AS max_stock')
            ->from('App\Entity\Product', 'p')
            ->groupBy('p.name', 'p.reference')
            ->setMaxResults(1);
    
        $result = $queryBuilder->getQuery()->getOneOrNullResult();
        
        return [
            'product' => $result['name']. ' ' . $result['reference'],
            'value' => $result['max_stock'],
        ];
    }

    public function bestSellingProduct() {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder
            ->select('p.name AS name, p.reference AS reference, SUM(s.units) AS total_sales')
            ->from('App\Entity\Product', 'p')
            ->leftJoin('p.sales', 's')
            ->groupBy('p.id')
            ->orderBy('total_sales', 'DESC')
            ->setMaxResults(1);
    
        $result = $queryBuilder->getQuery()->getOneOrNullResult();
        
        return [
            'product' => $result['name']. ' ' . $result['reference'],
            'value' => $result['total_sales'],
        ];
    }
}