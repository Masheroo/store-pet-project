<?php

namespace Api\Lot;

use App\Repository\FieldValueRepository;
use App\Repository\LotRepository;
use App\Tests\Traits\ClientHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddFieldValueTest extends WebTestCase
{
    use ClientHelperTrait;
    public function testAddFieldSuccessful(): void
    {
        $client = $this->getLoginByManagerClient(self::createClient(), $container = self::getContainer());

        /** @var FieldValueRepository $fieldValueRepository */
        $fieldValueRepository = $container->get(FieldValueRepository::class);
        $fieldValue = $fieldValueRepository->findAll()[0];

        /** @var LotRepository $lotRepository */
        $lotRepository = $container->get(LotRepository::class);
        $lot = $lotRepository->findAll()[0];

        $client->request('POST', '/api/lot/'.$lot->getId().'/field/'.$fieldValue->getId().'/add');

        self::assertResponseIsSuccessful();
    }
}