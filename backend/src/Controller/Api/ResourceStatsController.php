<?php

namespace App\Controller\Api;

use App\Repository\ResourceStatsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @author Wilhelm Zwertvaegher
 */
class ResourceStatsController extends AbstractController
{
    public function __construct(private readonly ResourceStatsRepository $resourceStatsRepository)
    {}

    #[Route('/api/resources/{id}/stats', name: 'api_resource_stats', requirements: ['id' => '\d+'], methods: ['GET', 'HEAD'])]
    public function __invoke(int $id, Request $request): JsonResponse
    {
        $stats = $this->resourceStatsRepository->findByResourceId($id);
        if (null === $stats) {
            throw new NotFoundHttpException();
        }

        $response = $this->json($stats);
        // stats should expire according to their current age
        // the more ancient the update is, the more chances there are that it will rarely be updated
        // now - last update => max age
        // e.g. if stats have been updated 10 minutes ago, then they expire in 10 minutes
        // of course this is suboptimal, but for this POC it will do
        $response->setPublic();
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $updatedAt = new \DateTimeImmutable($stats['updated'], new \DateTimeZone('UTC'));
        $diff = $now->getTimestamp() - $updatedAt->getTimestamp();
        $response->setMaxAge($diff);
        $response->setSharedMaxAge($diff); // for shared caches (e.g. Varnish, CDN)
        $response->headers->set('ETag', 'resource-stats-'.$id.'-'.$updatedAt->getTimestamp());
        $response->setVary(['Accept-Encoding']); // gzip
        $response->setLastModified($updatedAt);
        return $response;

    }

}
