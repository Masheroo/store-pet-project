<?php

namespace App\Tests\Controller;

use App\Repository\CategoryFieldRepository;
use App\Repository\CategoryRepository;
use App\Tests\Traits\ClientHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    use ClientHelperTrait;

    /** @covers \App\Controller\CategoryController::update */
    public function testUpdateSuccessful(): void
    {
        $client = $this->getLoginByManagerClient(self::createClient(), $container = self::getContainer());

        $newName = 'test1';
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $container->get(CategoryRepository::class);
        $category = $categoryRepository->findAll()[0];

        self::assertNotEquals($category->getName(), $newName);

        $client->request('post', '/api/category/'.$category->getId(), [
            'name' => $newName,
        ]);

        self::assertResponseIsSuccessful();

        $category = $categoryRepository->find($category->getId());

        self::assertEquals($newName, $category->getName());
    }

    /** @covers \App\Controller\CategoryController::delete */
    public function testDeleteSuccessful(): void
    {
        $client = $this->getLoginByManagerClient(self::createClient(), $container = self::getContainer());

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $container->get(CategoryRepository::class);
        $category = $categoryRepository->findOneBy(['name' => 'Тест1']);
        $categoryId = $category->getId();

        $client->request('delete', '/api/category/'.$category->getId());

        self::assertResponseIsSuccessful();

        self::assertNull($categoryRepository->find($categoryId));
    }

    /** @covers \App\Controller\CategoryController::create */
    public function testCreateSuccessful(): void
    {
        $client = $this->getLoginByManagerClient(self::createClient(), $container = self::getContainer());

        $client->request('post', '/api/category', [
            'name' => 'test_create',
        ]);

        self::assertResponseIsSuccessful();

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $container->get(CategoryRepository::class);

        self::assertNotNull($categoryRepository->findOneBy(['name' => 'test_create']));
    }

    /** @covers \App\Controller\CategoryController::getAll */
    public function testGetAllSuccessful(): void
    {
        $client = $this->getLoginByManagerClient(self::createClient(), self::getContainer());

        $client->request('get', '/api/category');

        self::assertResponseIsSuccessful();

        $response = $this->getJsonDecodedResponse($client);

        self::assertNotEmpty($response);
    }

    /** @covers \App\Controller\CategoryController::getAll */
    public function testAddCategoryFieldSuccessful(): void
    {
        $client = $this->getLoginByManagerClient(self::createClient(), $container = self::getContainer());

        $fieldName = 'test1-'.(new \DateTimeImmutable())->getTimestamp();
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $container->get(CategoryRepository::class);
        $category = $categoryRepository->findAll()[0];

        $client->request('post', '/api/category/'.$category->getId().'/field', [
            'name' => $fieldName,
        ]);

        self::assertResponseIsSuccessful();

        /** @var CategoryFieldRepository $categoryFieldRepository */
        $categoryFieldRepository = $container->get(CategoryFieldRepository::class);
        $categoryField = $categoryFieldRepository->findOneBy(['name' => $fieldName]);

        self::assertNotNull($categoryField);
    }
}
