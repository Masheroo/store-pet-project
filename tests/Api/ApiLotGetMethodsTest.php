<?php

namespace App\Tests\Api;

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

class ApiLotGetMethodsTest extends WebTestCase
{
    use ClientHelperTrait;
    use ClientConfiguratorTrait;

    /**
     * @throws \Exception
     */
    public function testGetAllLotsSuccessful(): void
    {
        $client = self::createClient();
        /** @var User $user */
        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email' => UserFixture::EMAIL_USER1]);

        $client->loginUser($user);
        $client->request('GET', 'api/lots');
        $response = $this->getJsonDecodedResponse($client);

        self::assertResponseStatusCodeSame(200);
        self::assertCount(LotFixture::COUNT_OF_LOTS, $response);
    }

    /**
     * @throws \Exception
     * @throws ExceptionInterface
     */
    public function testGetOneLotSuccessful(): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        /** @var Serializer $serializer */
        $serializer = $container->get('serializer');
        /** @var User $user */
        $user = $container->get(UserRepository::class)->findOneBy(['email' => UserFixture::EMAIL_USER1]);
        /** @var Lot $lot */
        $lot = $container->get(LotRepository::class)->getFirst();

        $client->loginUser($user);
        $client->request('GET', 'api/lot/'.$lot->getId());

        $response = $this->getJsonDecodedResponse($client);

        self::assertResponseStatusCodeSame(200);
        self::assertEquals($serializer->normalize($lot), $response);
    }

}
