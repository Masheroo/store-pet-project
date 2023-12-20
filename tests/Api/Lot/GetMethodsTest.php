<?php

namespace App\Tests\Api\Lot;

use App\DataFixtures\LotFixture;
use App\DataFixtures\UserFixture;
use App\Entity\Lot;
use App\Entity\User;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use App\Tests\Traits\ClientConfiguratorTrait;
use App\Tests\Traits\ClientHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;

class GetMethodsTest extends WebTestCase
{
    use ClientHelperTrait;
    use ClientConfiguratorTrait;

    /**
     * @throws \Exception
     */
    public function testGetAllLotsSuccessful(): void
    {
        self::ensureKernelShutdown();
        $client = self::createClient();
        $container = self::getContainer();
        /** @var User $user */
        $user = $container->get(UserRepository::class)->findOneBy(['email' => UserFixture::USER_EMAIL]);

        /** @var LotRepository $lotRepository */
        $lotRepository = $container->get(LotRepository::class);
        $countOfLots = count($lotRepository->findAll());

        $client->loginUser($user);
        $client->request('GET', 'api/lots');
        $response = $this->getJsonDecodedResponse($client);

        self::assertResponseStatusCodeSame(200);
        self::assertCount($countOfLots, $response);
    }

    /**
     * @throws \Exception
     * @throws ExceptionInterface
     */
    public function testGetOneLotSuccessful(): void
    {
        self::ensureKernelShutdown();
        $client = self::createClient();
        $container = self::getContainer();

        /** @var Serializer $serializer */
        $serializer = $container->get('serializer');
        /** @var User $user */
        $user = $container->get(UserRepository::class)->findOneBy(['email' => UserFixture::USER_EMAIL]);
        /** @var Lot $lot */
        $lot = $container->get(LotRepository::class)->getFirst();

        $client->loginUser($user);
        $client->request('GET', 'api/lot/'.$lot->getId());

        $response = $this->getJsonDecodedResponse($client);

        self::assertResponseStatusCodeSame(200);
        self::assertEquals($serializer->normalize($lot), $response);
    }
}
