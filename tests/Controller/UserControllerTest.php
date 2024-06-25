<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixture;
use App\Entity\User;
use App\Repository\ExternalApiTokenRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Tests\Traits\ClientHelperTrait;
use App\Tests\Traits\UserGetterTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;

use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertEquals;

class UserControllerTest extends WebTestCase
{
    use ClientHelperTrait;
    use UserGetterTrait;

    /**
     * @covers \App\Controller\UserController::replenishBalance
     */
    public function testReplenishBalance(): void
    {
        $client = self::createClient();
        $container = self::getContainer();
        $client = $this->getLoginByAdminClient($client, $container);

        $user = $this->getUserByEmail(UserFixture::USER_EMAIL, $container);
        $userBalanceBeforeReplenish = $user->getBalance();

        $client->request('post', 'api/user/'.$user->getId(), [
            'amount' => 1000,
        ]);

        self::assertResponseIsSuccessful();

        assertEquals($userBalanceBeforeReplenish + 1000, $user->getBalance());
    }

    /**
     * @throws ExceptionInterface
     *
     * @covers \App\Controller\UserController::getAllUsers
     */
    public function testGetAllUsers(): void
    {
        $client = self::createClient();
        $container = self::getContainer();
        $client = $this->getLoginByAdminClient($client, $container);

        $client->request('get', 'api/users');

        self::assertResponseIsSuccessful();

        $response = $this->getJsonDecodedResponse($client);

        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        $users = $userRepository->findAll();

        /** @var Serializer $serializer */
        $serializer = $container->get('serializer');

        $users = $serializer->normalize($users);

        assertEquals($users, $response);
    }

    /** @covers \App\Controller\UserController::switchUserRole */
    public function testSwitchUserRole(): void
    {
        $client = self::createClient();
        $container = self::getContainer();
        $client = $this->getLoginByAdminClient($client, $container);

        $user = $this->getUserByEmail(UserFixture::USER_EMAIL, $container);

        $client->request('post', 'api/user/'.$user->getId().'/role/admin');

        self::assertResponseIsSuccessful();
        assertContains(User::ROLE_ADMIN, $user->getRoles());
    }

    /** @covers  \App\Controller\UserController::switchUserRole */
    public function testSwitchUserRoleWithIncorrectRoleName(): void
    {
        $client = self::createClient();
        $container = self::getContainer();
        $client = $this->getLoginByAdminClient($client, $container);

        $user = $this->getUserByEmail(UserFixture::USER_EMAIL, $container);

        $client->request('post', 'api/user/'.$user->getId().'/role/test');

        self::assertResponseStatusCodeSame(400);
    }

    public function testTokenCreateSuccessful(): void
    {
        $client = self::createClient();
        $container = self::getContainer();
        $client = $this->getLoginByAdminClient($client, $container);

        $client->request('post', 'api/token', [
            'token_name' => 'test_token',
        ]);

        self::assertResponseIsSuccessful();

        $response = $this->getJsonDecodedResponse($client);

        self::assertArrayHasKey('token', $response);
        self::assertArrayHasKey('token_name', $response);

        /** @var ExternalApiTokenRepository $tokenRepository */
        $tokenRepository = $container->get(ExternalApiTokenRepository::class);
        $token = $tokenRepository->findByToken($response['token']);
        self::assertNotNull($token);
    }

    public function testGetShoppingListSuccessful(): void
    {
        $client = self::createClient();
        $container = self::getContainer();
        $client = $this->getLoginClient($client, $container);

        $client->request('get', 'api/user/shopping-list');

        $response = $this->getJsonDecodedResponse($client);
        self::assertNotNull($response[0]);

        $user = $this->getUserByEmail(UserFixture::USER_EMAIL, $container);

        /** @var OrderRepository $orderRepository */
        $orderRepository = $container->get(OrderRepository::class);
        $ordersCount = count($orderRepository->findBy(['user' => $user]));

        assertEquals($ordersCount, count($response));
    }
}
