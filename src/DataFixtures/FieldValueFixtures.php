<?php

namespace App\DataFixtures;

use App\Entity\FieldValue;
use App\Repository\CategoryFieldRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FieldValueFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
      private readonly CategoryFieldRepository $categoryFieldRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $field = $this->categoryFieldRepository->findAll()[0];

        $value = new FieldValue($field, 'test_value_1');
        $manager->persist($value);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFieldFixtures::class
        ];
    }
}
