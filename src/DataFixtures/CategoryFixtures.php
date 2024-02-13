<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category = new Category('Машина');
        $manager->persist($category);

        $category = new Category('Тест1');
        $manager->persist($category);

        $manager->flush();
    }
}
