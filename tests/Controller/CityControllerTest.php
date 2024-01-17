<?php

namespace App\Tests\Controller;

use App\Controller\CityController;
use App\Repository\CityRepository;
use App\Tests\Traits\ClientHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;

class CityControllerTest extends WebTestCase
{

    use ClientHelperTrait;

    /** @covers \App\Controller\CityController::getAll
     * @throws ExceptionInterface
     */
    public function testGetAll(): void
    {
        self::ensureKernelShutdown();
        $client = self::createClient();
        $container = self::getContainer();
        $client = $this->getLoginClient($client, $container);

        $client->jsonRequest('GET', 'api/cities');

        $response = $this->getJsonDecodedResponse($client);

        /** @var Serializer $serializer */
        $serializer = $container->get('serializer');

        /** @var CityRepository $cityRepository */
        $cityRepository = $container->get(CityRepository::class);
        $cities = $serializer->normalize($cityRepository->findAll()) ;

        self::assertEquals($cities, $response);
    }
}
