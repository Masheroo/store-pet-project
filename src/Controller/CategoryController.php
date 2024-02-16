<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\CategoryField;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Request\AddCategoryFieldRequest;
use App\Request\CategoryRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/category')]
#[IsGranted(User::ROLE_MANAGER)]
class CategoryController extends AbstractController
{
    #[Route('', name: 'create_category', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CategoryRequest $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $category = new Category($request->name);
        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json($category);
    }

    #[Route('', name: 'get_all_categories', methods: ['GET'])]
    public function getAll(CategoryRepository $categoryRepository): JsonResponse
    {
        return $this->json($categoryRepository->findAll());
    }

    #[Route('/{id}', name: 'delete_category', methods: ['DELETE'])]
    public function delete(Category $category, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->json('Category has been deleted');
    }

    #[Route('/{id}')]
    public function update(
        #[MapRequestPayload] CategoryRequest $request,
        Category $category,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $category->setName($request->name);
        $entityManager->flush();

        return $this->json($category);
    }

    #[Route('/{id}/field', name: 'add_category_field', methods: ['POST'])]
    public function addField(#[MapRequestPayload] AddCategoryFieldRequest $request, Category $category, EntityManagerInterface $entityManager): JsonResponse
    {
        $categoryField = new CategoryField($request->name, $category);
        $entityManager->persist($categoryField);
        $entityManager->flush();

        return $this->json($category->getFields());
    }
}
