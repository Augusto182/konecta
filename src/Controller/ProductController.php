<?php
// src/Controller/ProductController.php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Type\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController {

    #[Route("/create-product", name: "create_product")]
    public function createProduct(Request $request, EntityManagerInterface $entityManager, $edit = FALSE): Response {

        if ($edit) {
            $product = $entityManager->getRepository(Product::class)->find($edit);
            if (!$product) {
                throw $this->createNotFoundException('Product not found');
            }     
            $operation = 'Edit';
        }
        else {
            $product = new Product();
            $operation = 'New';
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $product->setDate(time());

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('main');
        }

        return $this->render('product/product.html.twig', [
            'form' => $form,
            'operation' => $operation,
        ]);
    }


    #[Route("/edit-product/{id}", name: "edit_product")]
    public function editProduct(Request $request, EntityManagerInterface $entityManager, $id): Response {
        return $this->createProduct($request, $entityManager, $id);
    }

    #[Route("/delete-product/{id}", name: "delete_product")]
    public function deleteProduct(Request $request, EntityManagerInterface $entityManager, $id): Response {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }
        $entityManager->remove($product);
        $entityManager->flush();
        return $this->redirectToRoute('main');
    }

}
