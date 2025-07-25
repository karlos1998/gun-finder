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
        return self::getProvider($gunModel->provider ?? 'netgun');
    }
}
