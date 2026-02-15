<?php

namespace App\Repository;

/**
 * @author Wilhelm Zwertvaegher
 */
class ResourceRepository
{
    use RandomLatencyTrait;

    private array $resources;

    public function __construct()
    {
        // current resource date must be past "last noon"
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'))->modify('-1 hour');

        $this->resources = [
            ['id' => 123, 'name' => 'Archived resource 123', 'date' => '2025-12-26'],
            ['id' => 124, 'name' => 'Archived resource 124', 'date' => '2025-12-15'],
            ['id' => 125, 'name' => 'Archived resource 125', 'date' => '2025-12-08'],
            ['id' => 126, 'name' => 'Archived resource 126', 'date' => '2025-12-01'],
            ['id' => 123456, 'name' => 'Current resource', 'date' => $now->format('Y-m-d')],
        ];
    }

    public function findAll(): array
    {
        $this->randomLatency();
        return $this->resources;
    }

    public function findCurrent(): ?array
    {
        $this->randomLatency();
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'))->format('Y-m-d');
        return array_find($this->resources, fn ($arr) => $arr['date'] === $now);
    }


    public function findById(int $id): ?array
    {
        $this->randomLatency();
        return array_find($this->resources, fn ($arr) => $arr['id'] === $id);
    }

}
