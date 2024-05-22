<?php

namespace App\Infrastructure;

trait Filter
{
    public function filterByAge(array $data, int $minAge, int $maxAge): array
    {
        return array_filter($data, function ($item) use ($minAge, $maxAge) {
            return isset ($item['age']) && $item['age'] > $minAge && $item['age'] < $maxAge;
        });
    }
    public function
        filterByLocation(
        array $data,
        string $location
    ): array {
        return array_filter($data, function ($item) use ($location) {
            return isset ($item['location']) && strpos($item['location'], $location);
        });
    }
    public function
        filterByChildrenAndPets(
        array $data
    ): array {
        return array_filter($data, function ($item) {
            return
                intval($item['children']) && intval($item['pets']);
        });
    }
}