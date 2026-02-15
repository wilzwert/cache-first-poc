<?php

namespace App\Controller\Api;

use App\Repository\ResourceRepository;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @author Wilhelm Zwertvaegher
 */
class ResourcesController extends AbstractController
{
    public function __construct(private readonly ResourceRepository $repository)
    {
    }

    #[Route('/api/resources', name: 'api_resource', methods: ['GET', 'HEAD'])]
    public function __invoke(Request $request): JsonResponse
    {
        $resources = $this->repository->findAll();

        $now = new \DateTimeImmutable('now', new DateTimeZone('UTC'));
        $response = $this->json($resources);
        $response->setPublic();
        $response->setMaxAge(3600 * 12); // 12 hours
        $response->setSharedMaxAge(3600 * 12);
        $response->headers->addCacheControlDirective('immutable', true);
        $response->headers->set('ETag', 'resources-'.$now->format('Y-m-d'));
        $response->setLastModified($now);
        $response->isNotModified($request);

        return $response;
    }
}
