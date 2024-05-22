<?php

namespace App\Domain\Helpers;

use DateTime;

class FieldMap
{
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function apply(array $data): array
    {
        $birthDate = new DateTime($data['Bbirthday']);
        $currentDate = new DateTime('today');
        $age = $birthDate->diff($currentDate)->y;
        $transformedData = ['name' => $data['Nname'] . ' ' . $data['Surname'], 'age' => $age, 'location' => $data['Aaddress']['Street'] . ', ' . $data['Aaddress']['Zip'] . ', ' . $data['Aaddress']['City'] . ', ' . $data['Aaddress']['Country'], 'children' => $data['Aaddress']['Kids'], 'pets' => $data['Aaddress']['Pets']];

        return $transformedData;
    }
}
