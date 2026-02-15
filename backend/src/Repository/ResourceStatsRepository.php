<?php

namespace App\Repository;

/**
 * @author Wilhelm Zwertvaegher
 */
class ResourceStatsRepository
{

    use RandomLatencyTrait;
    private array $stats;

    public function __construct()
    {
        $this->stats = [
            ['id' => 456, 'resource_id' => 123, 'updated' => '2025-12-26 15:00:00', 'data' => ['success' => 10, 'failure' => 5]],
            ['id' => 457, 'resource_id' => 124, 'updated' => '2026-02-01 14:00:00', 'data' => ['success' => 4, 'failure' => 15]],
            ['id' => 458, 'resource_id' => 125, 'updated' => '2026-01-08 13:00:00', 'data' => ['success' => 8, 'failure' => 6]],
            ['id' => 459, 'resource_id' => 126, 'updated' => '2026-01-15 12:00:00', 'data' => ['success' => 3, 'failure' => 7]],
            ['id' => 987654, 'resource_id' => 123456, 'updated' => new \DateTimeImmutable('now', new \DateTimeZone('UTC'))->format('Y-m-d H:i:s'), 'data' => ['success' => 3, 'failure' => 1]],
        ];
    }

    public function findAll(): array
    {
        $this->randomLatency();
        return $this->stats;
    }

    public function findById(int $id): ?array
    {
        $this->randomLatency();
        return array_find($this->stats, fn ($arr) => $arr['id'] === $id);
    }

    public function findByResourceId(int $resourceId): ?array
    {
        $this->randomLatency();
        return array_find($this->stats, fn ($arr) => $arr['resource_id'] === $resourceId);
    }
}
