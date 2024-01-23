<?php

namespace App\Tests\Controller;

use App\DataFixtures\ExternalApiTokenFixture;
use App\Tests\Traits\ClientHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExternalApiControllerTest extends WebTestCase
{
    use ClientHelperTrait;

    public function testGetAllLotsSuccessful(): void
    {
        $client = self::createClient();

        $client->request('get', '/external/api/lots', server: [
            'HTTP_External-Authentication' => ExternalApiTokenFixture::TOKEN,
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testGetAllLotsWithoutHeader(): void
    {
        $client = self::createClient();
        $client->request('get', '/external/api/lots');

        $response = $this->getJsonDecodedResponse($client);

        self::assertResponseStatusCodeSame(403);
        self::assertArrayHasKey('message', $response);
    }

    public function testGetAllLotsWithInvalidToken(): void
    {
        $client = self::createClient();
        $client->request('get', '/external/api/lots', server: [
            'HTTP_External-Authentication' => 'invalid token',
        ]);

        $response = $this->getJsonDecodedResponse($client);

        self::assertResponseStatusCodeSame(400);
        self::assertArrayHasKey('message', $response);
    }
}
