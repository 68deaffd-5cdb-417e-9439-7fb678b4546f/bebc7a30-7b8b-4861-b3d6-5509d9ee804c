<?php

namespace App\Enums;

enum Permissions: string
{
    const PERMISSION = 'permission';
    case CAMPAIGN_READ = 'campaign:read';
    case CAMPAIGN_CREATE = 'campaign:create';
    case CAMPAIGN_UPDATE = 'campaign:update';
    case CAMPAIGN_DELETE = 'campaign:delete';

    case CAMPAIGN_DONATION_READ = 'campaign:donation:read';
    case CAMPAIGN_DONATION_CREATE = 'campaign:donation:create';
    case CAMPAIGN_DONATION_DELETE = 'campaign:donation:delete';
}
