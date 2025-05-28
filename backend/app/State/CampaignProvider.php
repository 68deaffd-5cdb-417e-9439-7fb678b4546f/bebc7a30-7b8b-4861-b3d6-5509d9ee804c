<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\CampaignApi;
use App\Models\Campaign;
use App\Service\AuthService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

final class CampaignProvider implements ProviderInterface
{
    public function __construct(private AuthService $authService)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $this->authService->checkPermissions($operation);

        return match (get_class($operation)) {
            GetCollection::class => $this->collection($operation, $uriVariables, $context),
            Get::class, Put::class, Post::class, Delete::class => $this->single($operation, $uriVariables, $context),
            default => throw new MethodNotAllowedException(['GET', 'PUT', 'POST', 'DELETE']),
        };
    }

    private function collection(Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->authService->getCurrentUser();

        $page = $uriVariables['page'] ?? 1;

        /** @var Campaign[] $campaigns */
        $campaigns = Campaign::where('user_id', $user->getAttribute('id'))
            ->orderBy('id', 'desc')
            ->offset(($page - 1) * 10)
            ->limit(10)
            ->get();

        $resources = [];
        foreach ($campaigns as $campaign) {
            $resources[] = CampaignApi::from($campaign);
        }

        return $resources;
    }

    private function single(Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!isset($uriVariables['id'])) {
            throw new BadRequestHttpException();
        }

        /** @var Campaign $campaign */
        $campaign = Campaign::where('id', $uriVariables['id'])
            ->where('user_id', $this->authService->getCurrentUser()->getAttribute('id'))
            ->first();

        if ($campaign == null) {
            throw new NotFoundHttpException();
        }

        return CampaignApi::from($campaign);
    }
}
