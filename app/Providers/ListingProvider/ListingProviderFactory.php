<?php

namespace App\Providers\ListingProvider;

use App\Models\GunModel;
use InvalidArgumentException;

class ListingProviderFactory
{
    /**
     * Get the appropriate provider for the given provider name.
     *
     * @param string $providerName
     * @return ListingProviderInterface
     * @throws InvalidArgumentException
     */
    public static function getProvider(string $providerName): ListingProviderInterface
    {
        return match ($providerName) {
            'netgun' => new NetgunProvider(),
            'armybazar' => new ArmybazarProvider(),
            default => throw new InvalidArgumentException("Unsupported provider: {$providerName}"),
        };
    }

    /**
     * Get the appropriate provider for the given gun model.
     *
     * @param GunModel $gunModel
     * @return ListingProviderInterface
     */
    public static function getProviderForGunModel(GunModel $gunModel): ListingProviderInterface
    {
        // Default to netgun provider since we no longer store provider in GunModel
        return self::getProvider('netgun');
    }

    /**
     * Get all available providers.
     *
     * @return array
     */
    public static function getAllProviders(): array
    {
        return [
            'netgun' => new NetgunProvider(),
            'armybazar' => new ArmybazarProvider(),
        ];
    }
}
