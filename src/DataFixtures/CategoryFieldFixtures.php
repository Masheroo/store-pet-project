<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\CategoryField;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFieldFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $manager->getRepository(Category::class);
        $category = $categoryRepository->findAll()[0];

        $field = new CategoryField('Test field', $category);
        $field->setSlug($this->slugger->slug($field->getName())->toString());

        $manager->persist($field);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
