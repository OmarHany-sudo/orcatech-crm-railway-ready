<?php

declare(strict_types=1);

namespace App\Support\OrcaTech;

use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

/**
 * Single source of truth for the OrcaTech commercial demo packages.
 *
 * The CRM always uses one application and one dataset. This service only
 * changes feature availability in the current session; it never provisions a
 * second tenant, database, or frontend.
 */
final class Feature
{
    public const SESSION_KEY = 'orcatech.demo_package';

    public static function packages(): array
    {
        return (array) config('orcatech.packages', []);
    }

    /** @return array<string, mixed> */
    public static function package(?string $key = null): array
    {
        $key ??= self::currentPackage();

        return self::packages()[$key] ?? self::packages()[(string) config('orcatech.default_package')];
    }

    public static function currentPackage(): string
    {
        $default = (string) config('orcatech.default_package', 'starter');
        $selected = Session::get(self::SESSION_KEY, $default);

        return is_string($selected) && isset(self::packages()[$selected])
            ? $selected
            : $default;
    }

    public static function switchPackage(string $key): void
    {
        if (! isset(self::packages()[$key])) {
            throw new InvalidArgumentException("Unknown OrcaTech demo package [{$key}].");
        }

        Session::put(self::SESSION_KEY, $key);
    }

    public static function isKnown(string $feature): bool
    {
        return array_key_exists($feature, self::features());
    }

    /** @return array<string, mixed> */
    public static function definition(string $feature): array
    {
        return (array) (self::features()[$feature] ?? []);
    }

    /**
     * The minimum package level required for the feature.
     * Unknown capabilities are intentionally treated as unavailable rather
     * than silently becoming Starter features.
     */
    public static function requiredLevel(string $feature): int
    {
        if (! self::isKnown($feature)) {
            return PHP_INT_MAX;
        }

        return (int) (self::definition($feature)['level'] ?? PHP_INT_MAX);
    }

    /** Whether a known feature is unlocked in the selected demo package. */
    public static function available(string $feature): bool
    {
        return self::isKnown($feature)
            && self::package()['level'] >= self::requiredLevel($feature);
    }

    /** Whether a known feature is intentionally locked in the selected demo. */
    public static function locked(string $feature): bool
    {
        return self::isKnown($feature) && ! self::available($feature);
    }

    /** @return array<string, array<string, mixed>> */
    public static function features(): array
    {
        return (array) config('orcatech.features', []);
    }

    /** @return array<int, string> */
    public static function lockedFeatures(): array
    {
        return collect(self::features())
            ->keys()
            ->filter(fn (string $key): bool => self::locked($key))
            ->values()
            ->all();
    }

    /**
     * Return the feature protecting a Filament resource/page slug, if any.
     */
    public static function featureForSlug(string $slug): ?string
    {
        foreach (self::features() as $key => $definition) {
            if (in_array($slug, (array) ($definition['route_slugs'] ?? []), true)) {
                return (string) $key;
            }
        }

        return null;
    }

    /** @return array<string, array<string, mixed>> */
    public static function businessFeatures(): array
    {
        return collect(self::features())
            ->filter(fn (array $definition): bool => (int) ($definition['level'] ?? 0) > 0)
            ->all();
    }
}
