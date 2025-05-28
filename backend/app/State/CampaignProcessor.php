<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\CampaignApi;
use App\Models\Campaign;
use App\Service\AuthService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CampaignProcessor implements ProcessorInterface
{
    public function __construct(private AuthService $authService) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $this->authService->checkPermissions($operation);

        if (!($data instanceof CampaignApi)) {
            throw new \RuntimeException('$data must be an instance of CampaignApi');
        }

        return match (get_class($operation)) {
            Post::class => $this->create($data, $operation, $uriVariables, $context),
            Put::class => $this->update($data, $operation, $uriVariables, $context),
            Delete::class => $this->delete($data, $operation, $uriVariables, $context),
            default => throw new BadRequestHttpException(),
        };
    }

    private function create(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $campaign = Campaign::create(array_merge(
            $data->toArray(),
            ['user_id' => $this->authService->getCurrentUser()->getAttribute('id')]
        ));

        return CampaignApi::from($campaign);
    }

    private function update(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
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

        $campaign->update($data->toArray());

        return CampaignApi::from($campaign);
    }

    private function delete(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
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

        $campaign->delete();

        return CampaignApi::from($campaign);
    }
}
