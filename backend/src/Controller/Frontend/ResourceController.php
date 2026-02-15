<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
class ResourceController extends AbstractController
{
    #[Route('/{id}', name: 'resource', requirements: ['id' => '\d+'], methods: ['GET', 'HEAD'])]
    public function __invoke(int $id, HttpClientInterface $client, Request $request): Response
    {
        // get the current resource
        $apiResponse = $client->request('GET', sprintf('http://localhost/api/resources/%d', $id));
        $etag = $apiResponse->getHeaders()['etag'][0] ?? null;
        $data = $apiResponse->toArray();
        $updatedAt = new \DateTimeImmutable($data['date']);

        $html = $this->renderView('frontend/resource.html.twig', [
            'resource' => $data,
            'stats_url' => $this->generateUrl('api_resource_stats', ['id' => $data['id']]),
        ]);

        if (null === $etag) {
            $etag = $data['id'] .$updatedAt->getTimestamp();
        }
        $etag = 'page-resource-'.$etag;

        $response = new Response($html);
        $response->headers->set('ETag', $etag);
        $response->setPublic();
        $response->setMaxAge(31536000); // 1 year
        $response->setSharedMaxAge(31536000);
        $response->headers->addCacheControlDirective('immutable', true);
        $response->setLastModified($updatedAt);
        $response->isNotModified($request);

        return $response;
    }

}
