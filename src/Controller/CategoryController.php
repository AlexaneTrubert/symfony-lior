<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request,
                           CategoryRepository $categoryRepository,
                           SluggerInterface $slugger
    ): Response
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $category = $form->getData();
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $categoryRepository->save($category, true);


            return $this->redirectToRoute('home_index');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/category/{id}/edit', name: 'category_edit')]
    public function edit(Category $category, CategoryRepository $categoryRepository, Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $category = $form->getData();
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('home_index');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
}
