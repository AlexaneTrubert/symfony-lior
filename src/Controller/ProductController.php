<?php

namespace App\Controller;

use App\Entity\Product;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category', methods: ['GET'], priority: -1)]
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }

    #[Route('/{category_slug}/{slug}', name: 'product_show', methods: ['GET'], priority: -1)]
    public function show($slug, ProductRepository $productRepository, EventDispatcherInterface $dispatcher): Response
    {
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit n'existe pas");
        }

        $dispatcher->dispatch(new ProductViewEvent($product), 'product.view');

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/admin/product/create', name: 'product_create', methods: ['GET', 'POST'])]
    public function create(Request $request, SluggerInterface $slugger, ProductRepository $productRepository): RedirectResponse|Response
    {
        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $productRepository->save($product, true);

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug(),
            ]);
        }


        return $this->render('product/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/product/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    public function edit(Product $id, ProductRepository $productRepository,
                         Request $request, ValidatorInterface $validator
    ): RedirectResponse|Response
    {
        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug(),
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
}
