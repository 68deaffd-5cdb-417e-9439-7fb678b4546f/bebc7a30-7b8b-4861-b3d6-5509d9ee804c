<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\CampaignDonationApi;
use App\Models\Campaign;
use App\Models\CampaignDonation;
use App\Service\AuthService;
use App\Service\OmniPayService;
use Lcobucci\Clock\SystemClock;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

final class CampaignDonationProcessor implements ProcessorInterface
{
    public function __construct(private AuthService $authService, private OmniPayService $omniPayService)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $this->authService->checkPermissions($operation);

        if (!($data instanceof CampaignDonationApi)) {
            throw new \RuntimeException('$data must be an instance of CampaignDonationApi');
        }

        return match (get_class($operation)) {
            Post::class => $this->create($data, $operation, $uriVariables, $context),
            default => throw new MethodNotAllowedException(['POST']),
        };
    }

    private function create(CampaignDonationApi $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!isset($uriVariables['campaign_id'])) {
            throw new BadRequestHttpException();
        }

        $campaign = Campaign::where('id', $uriVariables['campaign_id'])->first();

        if ($campaign == null) {
            throw new NotFoundHttpException();
        }

        /** @var CampaignDonation $campaignDonation */
        $campaignDonation = CampaignDonation::create(array_merge(
            $data->toArray(),
            [
                'user_id' => $this->authService->getCurrentUser()->getAttribute('id'),
                'campaign_id' => $campaign->id,
                'donated_at' => SystemClock::fromUTC()->now(),
                'payment_status' => 'pending',
            ]
        ));

        $gateway = $this->omniPayService->getPaymentGateway($data->paymentMethod);

        if(!$gateway->pay($data->amount)) {
            $campaignDonation->setAttribute('payment_status', 'failed');
            throw new \RuntimeException("Could not make successful payment");
        }
        $campaignDonation->setAttribute('payment_status', 'completed');
        $campaignDonation->save();

        return CampaignDonationApi::from($campaignDonation);
    }

}
