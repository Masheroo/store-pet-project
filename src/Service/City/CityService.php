<?php

namespace App\Service\City;

use App\Entity\City;
use App\Repository\CityRepository;

class CityService
{
    public function __construct(
        private readonly CityRepository $repository
    ) {
    }

    public function create(string $name): City
    {
        $city = new City(null, $name);
        $this->repository->persist($city);
        return $city;
    }
}
