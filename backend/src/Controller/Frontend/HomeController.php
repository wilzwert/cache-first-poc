<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET', 'HEAD'])]
    public function __invoke(HttpClientInterface $client, Request $request): Response
    {
        // get the current resource
        $currentResourceApiResponse = $client->request('GET', 'http://localhost/api/resources/current');
        $currentResource = $currentResourceApiResponse->toArray();

        $archiveApiResponse = $client->request('GET', 'http://localhost/api/resources');
        $archive = array_filter($archiveApiResponse->toArray(), fn ($r) => $r['id'] !== $currentResource['id']);

        $html = $this->renderView('frontend/home.html.twig', [
            'resource' => $currentResource,
            'archive' => $archive,
            'stats_url' => $this->generateUrl('api_resource_stats', ['id' => $currentResource['id']], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        $etag = 'page-home-'.md5($html);

        $response = new Response($html);
        $response->headers->set('ETag', $etag);
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
        $response->isNotModified($request);

        return $response;
    }

}
