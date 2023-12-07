<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\DataFixtures\User\ClientFixtures;
use App\Manager\SmsToken\SmsTokenManager;
use App\Manager\User\UserManager;
use App\Manager\User\UserToken;
use App\Repository\User\UserRepository;
use App\Service\Network\NetworkServiceFactory;
use App\Service\Network\NetworkServiceInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait ClientConfiguratorTrait
{
    private static $tokenStorage = [];

    public function configureAndroidClient(KernelBrowser $client): void
    {
        $token = self::$tokenStorage['android_client'] ?? null;
        if (null === $token) {
            $container = $client->getContainer();
            $networkService = $this->createConfiguredMock(NetworkServiceInterface::class, [
                'getIdentityByAuthToken' => ClientFixtures::CLIENT_LOGIN_ANDROID,
            ]);
            $factory = $this->createConfiguredMock(NetworkServiceFactory::class, [
                'createByToken' => $networkService,
            ]);
            $container->set(
                NetworkServiceFactory::class,
                $factory
            );
            $this->configureJsonClient($client);
            $client->request(
                'POST',
                '/api/user/auth',
                [],
                [],
                [],
                json_encode([
                    UserToken::GOOGLE_TOKEN_TYPE => 'token',
                ])
            );

            $data = json_decode($client->getResponse()->getContent(), true);
            $token = $data['access_token'];
            self::$tokenStorage['android_client'] = $token;
        }
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));
    }

    public function configureJsonClient(KernelBrowser $client): void
    {
        $client->setServerParameter('CONTENT_TYPE', 'application/json');
        $client->setServerParameter('HTTP_ACCEPT', 'application/json');
    }

    public function configureIosClient(KernelBrowser $client): void
    {
        $token = self::$tokenStorage['ios_client'] ?? null;
        if (null === $token) {
            $container = $client->getContainer();
            $networkService = $this->createConfiguredMock(NetworkServiceInterface::class, [
                'getIdentityByAuthToken' => ClientFixtures::CLIENT_LOGIN_IOS,
            ]);
            $factory = $this->createConfiguredMock(NetworkServiceFactory::class, [
                'createByToken' => $networkService,
            ]);
            $container->set(
                NetworkServiceFactory::class,
                $factory
            );
            $this->configureJsonClient($client);
            $client->request(
                'POST',
                '/api/user/auth',
                [],
                [],
                [],
                json_encode([
                    UserToken::APPLE_TOKEN_TYPE => 'token',
                ])
            );

            $data = json_decode($client->getResponse()->getContent(), true);
            $token = $data['access_token'];
            self::$tokenStorage['ios_client'] = $token;
        }
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));
    }

    public function configureRustoreClient(KernelBrowser $client): void
    {
        $token = self::$tokenStorage['rustore_client'] ?? null;

        if (null === $token) {
            $container = $client->getContainer();
            $phone = ClientFixtures::CLIENT_LOGIN_RUSTORE;
            $code = 9999;

            $rustoreUser = $container->get(UserRepository::class)->getByLogin(ClientFixtures::CLIENT_LOGIN_RUSTORE);

            $smsTokenManagerMock = $this->createMock(SmsTokenManager::class);
            $container->set(
                SmsTokenManager::class,
                $smsTokenManagerMock
            );
            $userManagerMock = $this->createConfiguredMock(UserManager::class, [
                'getOrCreateClientByPhone' => $rustoreUser,
            ]);
            $container->set(
                UserManager::class,
                $userManagerMock
            );

            $this->configureJsonClient($client);
            $client->request(
                'POST',
                '/api/user/auth/phone',
                [],
                [],
                [],
                json_encode([
                    'phone' => $phone,
                    'code' => $code,
                ])
            );

            $data = json_decode($client->getResponse()->getContent(), true);
            $token = $data['access_token'];
            self::$tokenStorage['rustore_client'] = $token;
        }
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));
    }
}
