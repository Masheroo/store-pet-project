<?php

namespace App\Controller;

use App\Repository\LotRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/external/api')]
class ExternalApiController extends AbstractController
{
    public const ALL_LOTS_CACHE_KEY = 'external-lots';

    /**
     * @throws InvalidArgumentException
     * @throws ExceptionInterface
     */
    #[Route('/lots', name: 'app_external_api', methods: ['GET'])]
    public function getAllLots(LotRepository $repository, SerializerInterface $serializer, CacheInterface $oCache): JsonResponse
    {
        $data = $oCache->get(self::ALL_LOTS_CACHE_KEY, function () use ($serializer, $repository) {
            return $serializer->normalize($repository->findAll());
        });

        return $this->json($data);
    }
}
