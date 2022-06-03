<?php

declare(strict_types=1);

namespace App\Facade;

use App\Entity\Shipment;
use App\Http\Request\Dto\CreateShipmentRequestDto;
use App\Http\Response\Dto\ShipmentDto;
use App\Repository\ShipmentRepository;
use App\Service\Carrier\ICarrier;
use App\Service\ShipmentService;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Nyholm\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Slim\Exception\HttpBadRequestException;

class ShipmentFacade
{
    private Shipment $shipment;
    private ICarrier $carrier;

    public function __construct(
        private EntityManager $em,
        private ServerRequest $request,
        private ContainerInterface $container,
        private ShipmentService $service,
        private ShipmentRepository $repository,
    ) {}

    public function getShipment(): Shipment
    {
        return $this->shipment;
    }

    public function fromRequest(CreateShipmentRequestDto $request): self
    {
        $this->carrier = $this->getCarrierService($request->shipment->carrier);

        try {
            $this->shipment = $this->service->create($request);
        } catch (Exception $e) {
            throw new HttpBadRequestException($this->request, $e->getMessage());
        }

        return $this;
    }

    public function fromDeliveryNote(string $deliveryNote): self
    {
        $this->shipment = $this->repository->findOneBy(['deliveryNote' => $deliveryNote])
            ?? throw new HttpBadRequestException($this->request, sprintf('Shipment with deliveryNote `%s` not found.', $deliveryNote));

        $this->carrier = $this->getCarrierService($this->shipment->getCarrier()->getCode());

        return $this;
    }

    public function fromShipmentId(int $id): self
    {
        $this->shipment = $this->repository->findOneBy(['id' => $id])
            ?? throw new HttpBadRequestException($this->request, sprintf('Shipment with id `%d` not found.', $id));

        $this->carrier = $this->getCarrierService($this->shipment->getCarrier()->getCode());

        return $this;
    }

    public function register(): void
    {
        !$this->shipment->getRegisteredAt()
            ?: throw new HttpBadRequestException($this->request, sprintf('Shipment with deliveryNote `%s` already registered.', $this->shipment->getDeliveryNote()));

        $this->carrier->registerShipment($this->shipment);
        $this->shipment->setRegisteredAt(new DateTime());
        $this->em->flush();
    }

    public function retrieveStatus(): void
    {
        !$this->shipment->getCancelledAt()
            ?: throw new HttpBadRequestException($this->request, sprintf('Shipment with deliveryNote `%s` already canceled.', $this->shipment->getDeliveryNote()));

        !$this->shipment->getClosedAt()
            ?: throw new HttpBadRequestException($this->request, sprintf('Shipment with deliveryNote `%s` already closed.', $this->shipment->getDeliveryNote()));

        $this->carrier->retrieveStatus($this->shipment);
        $this->shipment->setLastStatusCheck(new DateTime());
        $this->em->flush($this->shipment);
    }

    public function cancel(): void
    {
        !$this->shipment->getCancelledAt()
            ?: throw new HttpBadRequestException($this->request, sprintf('Shipment with deliveryNote `%s` is already canceled.', $this->shipment->getDeliveryNote()));

        $this->carrier->cancelShipment($this->shipment);
        $this->shipment->setCancelledAt(new DateTime());
        $this->em->flush($this->shipment);
    }

    public function getResponse(): ShipmentDto
    {
        return ShipmentDto::from($this->shipment);
    }

    private function getCarrierService(string $carrierCode): ICarrier
    {
        return $this->container->get('App\\Services\\Carrier\\' . $carrierCode . 'Carrier');
    }
}