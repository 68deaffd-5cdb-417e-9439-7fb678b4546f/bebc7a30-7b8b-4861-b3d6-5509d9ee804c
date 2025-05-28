<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Enums\Permissions;
use App\State\CampaignProcessor;
use App\State\CampaignProvider;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ApiResource(
    shortName: 'campaign',
    operations: [
        new GetCollection(
            extraProperties: [
                Permissions::PERMISSION => Permissions::CAMPAIGN_READ
            ]
        ),
        new Get(
            extraProperties: [
                Permissions::PERMISSION => Permissions::CAMPAIGN_READ
            ],
        ),
        new Post(
            extraProperties: [
                Permissions::PERMISSION => Permissions::CAMPAIGN_CREATE
            ],
        ),
        new Put(
            extraProperties: [
                Permissions::PERMISSION => Permissions::CAMPAIGN_UPDATE
            ],
        ),
        new Delete(
            extraProperties: [
                Permissions::PERMISSION => Permissions::CAMPAIGN_DELETE
            ],
        ),
    ],

    normalizationContext: ['groups' => ['campaign', 'campaign:read']],
    denormalizationContext: ['groups' => ['campaign', 'campaign:write']],

    provider: CampaignProvider::class,
    processor: CampaignProcessor::class,

    rules: [
        'title' => 'required|string',
        'description' => 'nullable|string',
        'goalAmount' => 'nullable|numeric|min:0.01|max:1000000',
        'startsAt' => 'required|date',
        'endsAt' => 'required|date|after:startsAt',
        'status' => ['required', 'in:draft,active,completed,cancelled,deleted'],
    ],
    middleware: 'keycloak.auth'
)]
#[MapOutputName(SnakeCaseMapper::class)]
class CampaignApi extends Data
{
    public function __construct(
        #[Groups(['campaign:read'])]
        public ?int $id = null,

        #[Groups(['campaign'])]
        #[ApiProperty(example: "Campaign title")]
        public ?string $title = null,

        #[Groups(['campaign'])]
        #[ApiProperty(example: "Campaign description")]
        public ?string $description = null,

        #[Groups(['campaign'])]
        #[ApiProperty(example: "100.00")]
        public ?float $goalAmount = null,

        #[Groups(['campaign'])]
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
        #[ApiProperty(example: "2025-01-01 00:00:00")]
        public ?\DateTimeInterface $startsAt = null,

        #[Groups(['campaign'])]
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
        #[ApiProperty(example: "2025-01-02 00:00:00")]
        public ?\DateTimeInterface $endsAt = null,

        #[Groups(['campaign'])]
        #[ApiProperty(example: "draft")]
        public ?string $status = null,
    ) {}
}
