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
class CurrentResourceController extends AbstractController
{
    public function __construct(private readonly ResourceRepository $repository)
    {
    }

    #[Route('/api/resources/current', name: 'api_current_resource', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse {

        $resource = $this->repository->findCurrent();
        if (null === $resource) {
            throw new NotFoundHttpException();
        }


        $updatedAt = new \DateTimeImmutable($resource['date'], new \DateTimeZone('UTC'));
        // current resource should expire every day at noon
        $response = $this->json($resource);
        $response->setPublic();
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $nextMidday = $now->setTime(12, 0);
        if ($now >= $nextMidday) {
            // noon is past = expiration should occur on the next day
            $nextMidday = $nextMidday->modify('+1 day');
        }
        $secondsUntilNextMidday = $nextMidday->getTimestamp() - $now->getTimestamp();
        $response->setMaxAge($secondsUntilNextMidday);
        $response->setSharedMaxAge($secondsUntilNextMidday); // for shared caches (e.g. Varnish, CDN)
        $response->headers->set('ETag', 'resource-current-'.$updatedAt->getTimestamp());
        $response->setVary(['Accept-Encoding']); // gzip
        $response->setLastModified($updatedAt);
        $response->isNotModified($request);
        return $response;
    }
}
