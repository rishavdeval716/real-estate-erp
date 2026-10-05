<?php

namespace App\Libraries;

class LeadStatus
{
    // Lead Statuses
    public const STATUS_NEW         = 'New';
    public const STATUS_CONTACTED   = 'Contacted';
    public const STATUS_QUALIFIED   = 'Qualified';
    public const STATUS_UNQUALIFIED = 'Unqualified';
    public const STATUS_CONVERTED   = 'Converted';
    public const STATUS_LOST        = 'Lost';

    // Lead Stages (Pipeline Stages)
    public const STAGE_NEW                   = 'New';
    public const STAGE_CONTACTED             = 'Contacted';
    public const STAGE_QUALIFIED             = 'Qualified';
    public const STAGE_SITE_VISIT_SCHEDULED  = 'Site Visit Scheduled';
    public const STAGE_SITE_VISIT_COMPLETED  = 'Site Visit Completed';
    public const STAGE_NEGOTIATION           = 'Negotiation';
    public const STAGE_TOKEN_PENDING         = 'Token Pending';
    public const STAGE_READY_FOR_BOOKING     = 'Ready for Booking';
    public const STAGE_WON                   = 'Won';
    public const STAGE_LOST                  = 'Lost';

    // Priorities
    public const PRIORITY_LOW    = 'Low';
    public const PRIORITY_MEDIUM = 'Medium';
    public const PRIORITY_HIGH   = 'High';
    public const PRIORITY_URGENT = 'Urgent';

    /**
     * Get all supported statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW,
            self::STATUS_CONTACTED,
            self::STATUS_QUALIFIED,
            self::STATUS_UNQUALIFIED,
            self::STATUS_CONVERTED,
            self::STATUS_LOST,
        ];
    }

    /**
     * Get all pipeline stages in sequence
     */
    public static function getStages(): array
    {
        return [
            self::STAGE_NEW,
            self::STAGE_CONTACTED,
            self::STAGE_QUALIFIED,
            self::STAGE_SITE_VISIT_SCHEDULED,
            self::STAGE_SITE_VISIT_COMPLETED,
            self::STAGE_NEGOTIATION,
            self::STAGE_TOKEN_PENDING,
            self::STAGE_READY_FOR_BOOKING,
            self::STAGE_WON,
            self::STAGE_LOST,
        ];
    }

    /**
     * Get priorities
     */
    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW,
            self::PRIORITY_MEDIUM,
            self::PRIORITY_HIGH,
            self::PRIORITY_URGENT,
        ];
    }

    /**
     * Render HTML badge for status
     */
    public static function renderStatusBadge(string $status): string
    {
        $colors = [
            self::STATUS_NEW         => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
            self::STATUS_CONTACTED   => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0'],
            self::STATUS_QUALIFIED   => ['bg' => '#faf5ff', 'text' => '#7e22ce', 'border' => '#e9d5ff'],
            self::STATUS_UNQUALIFIED => ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1'],
            self::STATUS_CONVERTED   => ['bg' => '#ecfdf5', 'text' => '#047857', 'border' => '#a7f3d0'],
            self::STATUS_LOST        => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fecaca'],
        ];

        $c = $colors[$status] ?? ['bg' => '#f8fafc', 'text' => '#64748b', 'border' => '#e2e8f0'];
        return sprintf(
            '<span style="display: inline-flex; align-items: center; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: %s; color: %s; border: 1px solid %s;">%s</span>',
            $c['bg'],
            $c['text'],
            $c['border'],
            htmlspecialchars($status, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Render HTML badge for pipeline stage
     */
    public static function renderStageBadge(string $stage): string
    {
        $colors = [
            self::STAGE_NEW                  => ['bg' => '#f0f9ff', 'text' => '#0369a1', 'border' => '#bae6fd'],
            self::STAGE_CONTACTED            => ['bg' => '#ecfeff', 'text' => '#0e7490', 'border' => '#a5f3fc'],
            self::STAGE_QUALIFIED            => ['bg' => '#f5f3ff', 'text' => '#6d28d9', 'border' => '#ddd6fe'],
            self::STAGE_SITE_VISIT_SCHEDULED => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a'],
            self::STAGE_SITE_VISIT_COMPLETED => ['bg' => '#fef3c7', 'text' => '#92400e', 'border' => '#fcd34d'],
            self::STAGE_NEGOTIATION          => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#fed7aa'],
            self::STAGE_TOKEN_PENDING        => ['bg' => '#fdf4ff', 'text' => '#a21caf', 'border' => '#f5d0fe'],
            self::STAGE_READY_FOR_BOOKING    => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'border' => '#c7d2fe'],
            self::STAGE_WON                  => ['bg' => '#ecfdf5', 'text' => '#047857', 'border' => '#6ee7b7'],
            self::STAGE_LOST                 => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fca5a5'],
        ];

        $c = $colors[$stage] ?? ['bg' => '#f8fafc', 'text' => '#475569', 'border' => '#e2e8f0'];
        return sprintf(
            '<span style="display: inline-flex; align-items: center; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: %s; color: %s; border: 1px solid %s;">%s</span>',
            $c['bg'],
            $c['text'],
            $c['border'],
            htmlspecialchars($stage, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Render HTML badge for priority
     */
    public static function renderPriorityBadge(string $priority): string
    {
        $colors = [
            self::PRIORITY_LOW    => ['bg' => '#f8fafc', 'text' => '#64748b', 'border' => '#cbd5e1'],
            self::PRIORITY_MEDIUM => ['bg' => '#eff6ff', 'text' => '#2563eb', 'border' => '#bfdbfe'],
            self::PRIORITY_HIGH   => ['bg' => '#fff7ed', 'text' => '#ea580c', 'border' => '#fed7aa'],
            self::PRIORITY_URGENT => ['bg' => '#fef2f2', 'text' => '#dc2626', 'border' => '#fca5a5'],
        ];

        $c = $colors[$priority] ?? ['bg' => '#f8fafc', 'text' => '#64748b', 'border' => '#e2e8f0'];
        return sprintf(
            '<span style="display: inline-flex; align-items: center; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; background: %s; color: %s; border: 1px solid %s;">%s</span>',
            $c['bg'],
            $c['text'],
            $c['border'],
            htmlspecialchars($priority, ENT_QUOTES, 'UTF-8')
        );
    }
}
