<?php

declare(strict_types=1);

namespace App\Facade;

use App\Entity\Carrier;
use App\Http\Request\Dto\PureAddressRequestDto;
use App\Service\AddressService;
use Doctrine\ORM\EntityManager;

class AddressFacade
{
    public function __construct(
        private AddressService $addressService,
        private EntityManager $em,
    ) {}

    public function possibleDelivery(PureAddressRequestDto $address, ?string $carrierCode = null): array
    {
        if ($carrierCode) {
            return [$carrierCode => $this->addressService->possibleDelivery($address, $carrierCode)];
        } else {
            $result = [];
            foreach ($this->em->getRepository(Carrier::class)->createQueryBuilder('c')->select('c.code')->getQuery()->getSingleColumnResult() as $carrierCode) {
                $result[$carrierCode] = $this->addressService->possibleDelivery($address, $carrierCode);
            }
            return $result;
        }
    }
}