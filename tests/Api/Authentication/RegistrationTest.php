<?php

namespace App\Tests\Api\Authentication;

use App\DataFixtures\UserFixture;
use App\Repository\UserRepository;
use App\Tests\Traits\ClientConfiguratorTrait;
use App\Tests\Traits\ClientHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationTest extends WebTestCase
{
    use ClientHelperTrait;
    use ClientConfiguratorTrait;

    public const REGISTRATION_EMAIL = 'testRegistration@mail.ru';
    public const REGISTRATION_PASSWORD = 'password12345678';

    /**
     * @throws \Exception
     */
    public function testRegistrationSuccessful(): void
    {
        $client = $this->createClient();
        $this->configureJsonClient($client);

        $client->request(
            'POST',
            '/api/registration',
            content: json_encode([
                'email' => self::REGISTRATION_EMAIL,
                'password' => self::REGISTRATION_PASSWORD,
            ])
        );
        $response = $this->getJsonDecodedResponse($client);

        self::assertResponseStatusCodeSame(200);
        self::assertArrayHasKey('access_token', $response);
        self::assertArrayHasKey('refresh_token', $response);

        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => self::REGISTRATION_EMAIL]);

        self::assertNotEmpty($user);
    }

    public function testRegistrationWithAlreadyUsedEmail(): void
    {
        $client = $this->createClient();
        $this->configureJsonClient($client);

        $client->request(
            'POST',
            '/api/registration',
            content: json_encode([
                'email' => UserFixture::EMAIL_USER1,
                'password' => UserFixture::PASSWORD_USER1,
            ])
        );
        $response = $this->getJsonDecodedResponse($client);

        self::assertResponseStatusCodeSame(400);
        self::assertArrayHasKey('errors', $response);
        self::assertArrayHasKey('code', $response);
    }
}
