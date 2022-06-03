<?php

declare(strict_types=1);

namespace App\Service;

use App\Http\Request\Dto\PureAddressRequestDto;
use App\Service\Carrier\ICarrier;
use Psr\Container\ContainerInterface;

class AddressService
{
    public function __construct(
        private ContainerInterface $container,
    ) {}

    public function possibleDelivery(PureAddressRequestDto $address, string $carrierCode): ?bool
    {
        /** @var ICarrier $carrier */
        $carrier = $this->container->get('App\\Services\\Carrier\\' . $carrierCode . 'Carrier');

        return $carrier->possibleDelivery($address);
    }

    public function validate(PureAddressRequestDto $address): ?array
    {
        $encodedAddress = str_replace(' ', '%20', "{$address->street} {$address->number}, {$address->zipCode} {$address->city}, {$address->country}");
        $response = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=KEY&language=cs&address=' . $encodedAddress);

        $location = json_decode($response);

        if ($location->status === 'OK' && $results = $location->results ?? []) {
            if ($result = array_values(array_filter($results, fn($val) => (bool) count(array_intersect($val->types, ['premise', 'subpremise', 'street_address']))))[0] ?? null) {
                $googleAddress = [
                    'street' => self::getAddressComponentByTypes($result->address_components, ['route'])->short_name,
                    'number' => self::getAddressComponentByTypes($result->address_components, ['street_number'])->short_name,
                    'city' => self::getAddressComponentByTypes($result->address_components, ['locality', 'administrative_area_level_2'])->short_name,
                    'zipCode' => self::getAddressComponentByTypes($result->address_components, ['postal_code'])->short_name,
                    'country' => self::getAddressComponentByTypes($result->address_components, ['country'])->short_name,
                ];

                return [
                    'suggestions' => array_diff($googleAddress, (array) $address),
                    'origin' => (array) $address,
                ];
            }
        }

        return null;
    }

    private static function getAddressComponentByTypes(array $components, array $types): \stdClass
    {
        return array_values(array_filter($components, fn($val) => (bool) array_intersect($val->types, $types)))[0];
    }
}