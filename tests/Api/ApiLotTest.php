<?php

namespace App\Tests\Api;

use App\DataFixtures\LotFixture;
use App\DataFixtures\UserFixture;
use App\Repository\LotRepository;
use App\Repository\UserRepository;
use App\Tests\Traits\ClientConfiguratorTrait;
use App\Tests\Traits\ClientHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;

class ApiLotTest extends WebTestCase
{
    use ClientHelperTrait;
    use ClientConfiguratorTrait;

    /**
     * @throws \Exception
     */
    public function testGetAllLotsSuccessful(): void
    {
        $client = self::createClient();
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
        $user = $container->get(UserRepository::class)->findOneBy(['email' => UserFixture::EMAIL_USER1]);
        $lot = $container->get(LotRepository::class)->getFirst();

        $client->loginUser($user);
        $client->request('GET', 'api/lot/'.$lot->getId());

        $response = $this->getJsonDecodedResponse($client);

        self::assertResponseStatusCodeSame(200);
        self::assertEquals($serializer->normalize($lot), $response);
    }
}
