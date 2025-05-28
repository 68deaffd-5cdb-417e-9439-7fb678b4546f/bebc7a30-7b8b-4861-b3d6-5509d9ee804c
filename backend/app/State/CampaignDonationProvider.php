<?php

namespace App\State;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\CampaignDonationApi;
use App\Models\CampaignDonation;
use App\Service\AuthService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

final class CampaignDonationProvider implements ProviderInterface
{
    public function __construct(private AuthService $authService)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $this->authService->checkPermissions($operation);

        return match (get_class($operation)) {
            GetCollection::class => $this->collection($operation, $uriVariables, $context),
            default => throw new MethodNotAllowedException(['GET']),
        };
    }

    private function collection(Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!isset($uriVariables['campaign_id'])) {
            throw new BadRequestHttpException();
        }

        $user = $this->authService->getCurrentUser();

        $page = $uriVariables['page'] ?? 1;

        $campaignDonations = CampaignDonation::where('user_id', $user->getAttribute('id'))
            ->where('campaign_id', $uriVariables['campaign_id'])
            ->orderBy('id', 'desc')
            ->offset(($page - 1) * 10)
            ->limit(10)
            ->get();


        $resources = [];
        foreach ($campaignDonations as $campaignDonation) {
            $resources[] = CampaignDonationApi::from($campaignDonation);
        }

        return $resources;
    }
}
