<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\Carrier;
use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\Country;
use App\Entity\Currency;
use App\Entity\Entrant;
use App\Entity\Package;
use App\Entity\Service;
use App\Entity\Shipment;
use App\Http\Request\Dto\CreateShipmentRequestDto;
use App\Repository\ShipmentRepository;
use Doctrine\ORM\EntityManager;
use Nyholm\Psr7\ServerRequest;
use Slim\Exception\HttpBadRequestException;

class ShipmentService
{
    public function __construct(
        private EntityManager      $em,
        private ServerRequest      $request,
        private ShipmentRepository $repository,
    ) {}

    public function create(CreateShipmentRequestDto $data): Shipment
    {
        if (!$this->repository->availableForUse($data->shipment->deliveryNote)) {
            throw new HttpBadRequestException($this->request, "Shipment `{$data->shipment->deliveryNote}` already exists.");
        }

        $sender = $this->em->getRepository(Entrant::class)->getOrCreate(
            contact: $this->em->getRepository(Contact::class)->getOrCreate((array) $data->sender->contact),
            company: $data->sender->company ? $this->em->getRepository(Company::class)->getOrCreate((array) $data->sender->company) : null,
            address: $this->em->getRepository(Address::class)->getOrCreate([
                'name' => $data->sender->address->name,
                'street' => $data->sender->address->street,
                'number' => $data->sender->address->number,
                'city' => $data->sender->address->city,
                'zipCode' => $data->sender->address->zipCode,
                'country' => $this->em->getRepository(Country::class)->getByCode($data->sender->address->country),
            ])
        );

        $recipient = $this->em->getRepository(Entrant::class)->getOrCreate(
            contact: $this->em->getRepository(Contact::class)->getOrCreate((array) $data->recipient->contact),
            company: $data->recipient->company ? $this->em->getRepository(Company::class)->getOrCreate((array) $data->recipient->company) : null,
            address: $this->em->getRepository(Address::class)->getOrCreate([
                'name' => $data->recipient->address->name,
                'street' => $data->recipient->address->street,
                'number' => $data->recipient->address->number,
                'city' => $data->recipient->address->city,
                'zipCode' => $data->recipient->address->zipCode,
                'country' => $this->em->getRepository(Country::class)->getByCode($data->recipient->address->country),
            ])
        );

        $shipment = new Shipment(
            $this->em->getRepository(Carrier::class)->getByCode($data->shipment->carrier),
            $data->shipment->type,
            $sender,
            $recipient,
            $data->shipment->deliveryNote,
            $data->shipment->variableSymbol,
            $data,
            $data->shipment->reference,
            $data->shipment->shipmentNote
        );

        foreach ($data->packages as $key => $package) {
            $package->number = $key + 1;
            $shipment->addPackage(new Package(...(array) $package));
        }

        foreach ($data->services ?? [] as $code => $service) {
            if ($service) {
                if (isset($service->currency)) {
                    $this->em->getRepository(Currency::class)->getByCode($service->currency);
                }
                $shipment->addService(new Service($code, (array)$service));
            }
        }

        $this->em->persist($shipment);
        $this->em->flush($shipment);

        return $shipment;
    }
}