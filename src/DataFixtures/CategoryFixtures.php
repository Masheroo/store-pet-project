<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $category = new Category('Машина');
        $category->setSlug($this->slugger->slug($category->getName())->toString());
        $manager->persist($category);

        $category = new Category('Тест1');
        $category->setSlug($this->slugger->slug($category->getName())->toString());
        $manager->persist($category);

        $manager->flush();
    }
}
