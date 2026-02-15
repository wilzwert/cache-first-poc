<?php

namespace App\Controller\Api;

use App\Repository\ResourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @author Wilhelm Zwertvaegher
 */
class ArchivedResourceController extends AbstractController
{
    public function __construct(private readonly ResourceRepository $repository)
    {
    }

    #[Route('/api/resources/{id}', name: 'api_archived_resource', requirements: ['id' => '\d+'], methods: ['GET', 'HEAD'])]
    public function __invoke(Request $request, int $id): JsonResponse
    {
        $resource = $this->repository->findById($id);
        if (null === $resource) {
            throw new NotFoundHttpException();
        }

        $updatedAt = new \DateTimeImmutable($resource['date']);

        $response = $this->json($resource);
        $response->setPublic();
        $response->setMaxAge(31536000); // 1 year
        $response->setSharedMaxAge(31536000);
        $response->headers->addCacheControlDirective('immutable', true);
        $response->headers->set('ETag', 'resource-'.$id.'-'.$updatedAt->getTimestamp());
        $response->setLastModified($updatedAt);
        $response->isNotModified($request);

        return $response;
    }
}
