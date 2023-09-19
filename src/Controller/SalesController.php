<?php
// src/Controller/SalesController.php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Sales;
use App\Form\Type\SalesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SalesController extends AbstractController {

    #[Route("/sale-product/{id}", name: "sale_product")]
    public function saleProduct(Request $request, EntityManagerInterface $entityManager, $id, ValidatorInterface $validator): Response {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }
        $stock = $product->getStock();

        $sales = new Sales();
        $sales->setIdProduct($product);
        $sales->setUnits(0);
 
        $form = $this->createForm(SalesType::class, $sales);
        $errors = [
            'out_of_stock' => FALSE,
        ];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sales = $form->getData();
            $units = $sales->getUnits();
            $new_stock = $stock - $units;
            if ($new_stock >= 0) {
                // Update product.
                $product->setStock($new_stock);
                $entityManager->persist($product);
                $entityManager->flush();
                // Save sale.
                $sales->setDate(time());
                $entityManager->persist($sales);
                $entityManager->flush();
                return $this->redirectToRoute('main');
            }
            else {
                $errors ['out_of_stock'] = TRUE;
            }
            
        }

        return $this->render('sales/sales.html.twig', [
            'form' => $form,
            'errors' => $errors,
        ]);
        return $this->redirectToRoute('main');
    }
}
