<?php

namespace App\Libraries;

class PropertyStatus
{
    public const AVAILABLE         = 'Available';
    public const RESERVED          = 'Reserved';
    public const UNDER_NEGOTIATION = 'Under Negotiation';
    public const BOOKED            = 'Booked';
    public const SOLD              = 'Sold';
    public const RENTED            = 'Rented';
    public const UNDER_MAINTENANCE = 'Under Maintenance';
    public const UNAVAILABLE       = 'Unavailable';

    /**
     * Get all valid property and unit statuses
     */
    public static function all(): array
    {
        return [
            self::AVAILABLE,
            self::RESERVED,
            self::UNDER_NEGOTIATION,
            self::BOOKED,
            self::SOLD,
            self::RENTED,
            self::UNDER_MAINTENANCE,
            self::UNAVAILABLE,
        ];
    }

    /**
     * Get badge color class / styles
     */
    public static function getBadgeClass(string $status): string
    {
        return match ($status) {
            self::AVAILABLE         => 'badge bg-success text-white',
            self::RESERVED          => 'badge bg-warning text-dark',
            self::UNDER_NEGOTIATION => 'badge bg-info text-dark',
            self::BOOKED            => 'badge bg-primary text-white',
            self::SOLD              => 'badge bg-secondary text-white',
            self::RENTED            => 'badge bg-dark text-white',
            self::UNDER_MAINTENANCE => 'badge bg-danger text-white',
            self::UNAVAILABLE       => 'badge bg-secondary text-white',
            default                 => 'badge bg-light text-dark',
        };
    }

    /**
     * Render an HTML badge
     */
    public static function renderBadge(string $status): string
    {
        $class = self::getBadgeClass($status);
        $escaped = esc($status);
        return "<span class=\"{$class}\">{$escaped}</span>";
    }

    /**
     * Check if a status string is valid
     */
    public static function isValid(string $status): bool
    {
        return in_array($status, self::all(), true);
    }
}
