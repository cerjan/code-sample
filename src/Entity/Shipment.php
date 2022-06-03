<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ShipmentRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

#[ORM\Entity(repositoryClass: ShipmentRepository::class)]
#[ORM\UniqueConstraint(fields: ['deliveryNote', 'cancelledAt'])]
class Shipment
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Carrier::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Carrier $carrier;

    #[ORM\Column(type: Types::STRING)]
    private string $type;

    #[ORM\ManyToOne(targetEntity: Entrant::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Entrant $sender;

    #[ORM\ManyToOne(targetEntity: Entrant::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Entrant $recipient;

    #[ORM\Column(type: Types::STRING)]
    private string $deliveryNote;

    #[ORM\Column(type: Types::STRING, nullable: true, options: ['comment' => 'Shipment identification number of the carrier.'])]
    private ?string $shipmentNumber;

    #[ORM\Column(type: Types::STRING, nullable: true, options: ['comment' => 'Shipment note of the carrier.'])]
    private ?string $shipmentNote;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $variableSymbol;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $reference;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $registeredAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $cancelledAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $closedAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $labeledAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $lastStatusCheck;

    #[ORM\OneToMany(mappedBy: 'shipment', targetEntity: Package::class, cascade: ['persist'])]
    #[ORM\OrderBy(['number' => 'ASC'])]
    private ArrayCollection|PersistentCollection $packages;

    #[ORM\OneToMany(mappedBy: 'shipment', targetEntity: Service::class, cascade: ['persist'])]
    private ArrayCollection|PersistentCollection $services;

    #[ORM\OneToMany(mappedBy: 'shipment', targetEntity: ShipmentHistory::class, cascade: ['persist'])]
    private ArrayCollection|PersistentCollection $history;

    #[ORM\Column(type: Types::JSON)]
    private mixed $jsonRequest;

    /**
     * @param Carrier $carrier
     * @param string $type
     * @param Entrant $sender
     * @param Entrant $recipient
     * @param string $deliveryNote
     * @param string|null $variableSymbol
     * @param mixed $jsonRequest
     * @param string|null $reference
     * @param string|null $shipmentNote
     */
    public function __construct(Carrier $carrier, string $type, Entrant $sender, Entrant $recipient, string $deliveryNote, ?string $variableSymbol, mixed $jsonRequest, ?string $reference, ?string $shipmentNote)
    {
        $this->carrier = $carrier;
        $this->type = $type;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->deliveryNote = $deliveryNote;
        $this->variableSymbol = $variableSymbol;
        $this->jsonRequest = $jsonRequest;
        $this->reference = $reference;
        $this->shipmentNote = $shipmentNote;

        $this->createdAt = new DateTime();
        $this->packages = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->history = new ArrayCollection();

        $this->registeredAt = null;
        $this->cancelledAt = null;
        $this->closedAt = null;
        $this->labeledAt = null;
        $this->lastStatusCheck = null;

        $this->shipmentNumber = null;
    }

    public function addPackage(Package $package): void
    {
        $package->setShipment($this);
        $this->packages->add($package);
    }

    public function addService(Service $service): void
    {
        $service->setShipment($this);
        $this->services->add($service);
    }

    public function getService(string $code): ?Service
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('code', $code));

        return $this->services->matching($criteria)[0] ?? null;
    }

    public function addHistory(ShipmentHistory $shipmentHistory): void
    {
        if ($shipmentHistory->getStatusId()) {
            $shipmentHistory->setShipment($this);
            if (!$this->hasHistory($shipmentHistory)) {
                $this->history->add($shipmentHistory);
                if (in_array($shipmentHistory->getStatusId(), Status::CLOSING_STATUSES)) {
                    $this->setClosedAt(new DateTime());
                }
            }
        }
    }

    public function hasHistory(ShipmentHistory $shipmentHistory): bool
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('hash', $shipmentHistory->getHash()));

        return $this->history->matching($criteria)->count() > 0;
    }

    public function setLabeled(): void
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->isNull('label'));

        if ($this->packages->matching($criteria)->count() === 0) {
            $this->labeledAt = new DateTime();
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Carrier
     */
    public function getCarrier(): Carrier
    {
        return $this->carrier;
    }

    /**
     * @param Carrier $carrier
     */
    public function setCarrier(Carrier $carrier): void
    {
        $this->carrier = $carrier;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Entrant
     */
    public function getSender(): Entrant
    {
        return $this->sender;
    }

    /**
     * @param Entrant $sender
     */
    public function setSender(Entrant $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return Entrant
     */
    public function getRecipient(): Entrant
    {
        return $this->recipient;
    }

    /**
     * @param Entrant $recipient
     */
    public function setRecipient(Entrant $recipient): void
    {
        $this->recipient = $recipient;
    }

    /**
     * @return string
     */
    public function getDeliveryNote(): string
    {
        return $this->deliveryNote;
    }

    /**
     * @param string $deliveryNote
     */
    public function setDeliveryNote(string $deliveryNote): void
    {
        $this->deliveryNote = $deliveryNote;
    }

    /**
     * @return string|null
     */
    public function getVariableSymbol(): ?string
    {
        return $this->variableSymbol;
    }

    /**
     * @param string|null $variableSymbol
     */
    public function setVariableSymbol(?string $variableSymbol): void
    {
        $this->variableSymbol = $variableSymbol;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string|null $reference
     */
    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreated(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return ArrayCollection|PersistentCollection|Package[]
     */
    public function getPackages(): ArrayCollection|PersistentCollection|array
    {
        return $this->packages;
    }

    /**
     * @param ArrayCollection|PersistentCollection $packages
     */
    public function setPackages(ArrayCollection|PersistentCollection $packages): void
    {
        $this->packages = $packages;
    }

    /**
     * @return ArrayCollection|PersistentCollection|Service[]
     */
    public function getServices(): ArrayCollection|PersistentCollection|array
    {
        return $this->services;
    }

    /**
     * @param ArrayCollection|PersistentCollection $services
     */
    public function setServices(ArrayCollection|PersistentCollection $services): void
    {
        $this->services = $services;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getHistory(): ArrayCollection|PersistentCollection
    {
        return $this->history;
    }

    /**
     * @param ArrayCollection|PersistentCollection $history
     */
    public function setHistory(ArrayCollection|PersistentCollection $history): void
    {
        $this->history = $history;
    }

    /**
     * @return DateTime|null
     */
    public function getLastStatusCheck(): ?DateTime
    {
        return $this->lastStatusCheck;
    }

    /**
     * @param DateTime|null $lastStatusCheck
     */
    public function setLastStatusCheck(?DateTime $lastStatusCheck): void
    {
        $this->lastStatusCheck = $lastStatusCheck;
    }

    /**
     * @return string|null
     */
    public function getShipmentNumber(): ?string
    {
        return $this->shipmentNumber;
    }

    /**
     * @param string|null $shipmentNumber
     */
    public function setShipmentNumber(?string $shipmentNumber): void
    {
        $this->shipmentNumber = $shipmentNumber;
    }

    /**
     * @return DateTime|null
     */
    public function getRegisteredAt(): ?DateTime
    {
        return $this->registeredAt;
    }

    /**
     * @param DateTime|null $registeredAt
     */
    public function setRegisteredAt(?DateTime $registeredAt): void
    {
        $this->registeredAt = $registeredAt;
    }

    /**
     * @return DateTime|null
     */
    public function getCancelledAt(): ?DateTime
    {
        return $this->cancelledAt;
    }

    /**
     * @param DateTime|null $cancelledAt
     */
    public function setCancelledAt(?DateTime $cancelledAt): void
    {
        $this->cancelledAt = $cancelledAt;
    }

    /**
     * @return DateTime|null
     */
    public function getClosedAt(): ?DateTime
    {
        return $this->closedAt;
    }

    /**
     * @param DateTime|null $closedAt
     */
    public function setClosedAt(?DateTime $closedAt): void
    {
        $this->closedAt = $closedAt;
    }

    /**
     * @return DateTime|null
     */
    public function getLabeledAt(): ?DateTime
    {
        return $this->labeledAt;
    }

    /**
     * @param DateTime|null $labeledAt
     */
    public function setLabeledAt(?DateTime $labeledAt): void
    {
        $this->labeledAt = $labeledAt;
    }

    /**
     * @return string|null
     */
    public function getShipmentNote(): ?string
    {
        return $this->shipmentNote;
    }

    /**
     * @param string|null $shipmentNote
     */
    public function setShipmentNote(?string $shipmentNote): void
    {
        $this->shipmentNote = $shipmentNote;
    }
}