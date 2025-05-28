<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Enums\Permissions;
use App\State\CampaignDonationProcessor;
use App\State\CampaignDonationProvider;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/campaigns/{campaign_id}/donations',
            uriVariables: ['campaign_id'],
            extraProperties: [
                Permissions::PERMISSION => Permissions::CAMPAIGN_DONATION_READ
            ]
        ),
        new Post(
            uriTemplate: '/campaigns/{campaign_id}/donations',
            uriVariables: ['campaign_id'],
            read: false,
            extraProperties: [
                Permissions::PERMISSION => Permissions::CAMPAIGN_DONATION_CREATE
            ],
        ),
    ],

    normalizationContext: ['groups' => ['campaign:donation', 'campaign:donation:read']],
    denormalizationContext: ['groups' => ['campaign:donation', 'campaign:donation:write']],

    provider: CampaignDonationProvider::class,
    processor: CampaignDonationProcessor::class,

    rules: [
        'message' => 'nullable|string',
        'amount' => 'nullable|numeric|min:0.01|max:1000000',
        'paymentMethod' => ['required', 'in:dummy'],
    ],
    middleware: 'keycloak.auth'
)]
#[MapOutputName(SnakeCaseMapper::class)]
class CampaignDonationApi extends Data
{
    public function __construct(
        #[Groups(['campaign:donation:read'])]
        public ?int $id = null,

        #[Groups(['campaign:donation'])]
        #[ApiProperty(example: "Donation message")]
        public ?string $message = null,

        #[Groups(['campaign:donation'])]
        #[ApiProperty(example: "100.00")]
        public ?float $amount = null,

        #[Groups(['campaign:donation'])]
        #[ApiProperty(example: "dummy")]
        public ?string $paymentMethod = null,

        #[Groups(['campaign:donation:read'])]
        #[ApiProperty(example: "pending")]
        public ?string $paymentStatus = null,

        #[Groups(['campaign:donation:read'])]
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
        #[ApiProperty(example: "2025-01-01 00:00:00")]
        public ?\DateTimeInterface $donatedAt = null,
    ) {}
}
