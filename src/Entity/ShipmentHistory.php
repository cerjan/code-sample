<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Index(fields: ['hash'], name: 'idx_hash')]
class ShipmentHistory
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Shipment::class, inversedBy: 'history')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Shipment $shipment;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $statusId;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $carrierStatusId;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $carrierCreatedAt;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $carrierDescription;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $createdAt;

    #[ORM\Column(type: Types::STRING)]
    private string $hash;

    /**
     * @param int|null $statusId
     * @param string|null $carrierStatusId
     * @param DateTime|null $carrierCreatedAt
     * @param string|null $carrierDescription
     */
    public function __construct(?int $statusId, ?string $carrierStatusId, ?DateTime $carrierCreatedAt, ?string $carrierDescription)
    {
        $this->statusId = $statusId;
        $this->carrierStatusId = $carrierStatusId;
        $this->carrierCreatedAt = $carrierCreatedAt;
        $this->carrierDescription = $carrierDescription;
        $this->hash = md5(json_encode(func_get_args()));
        $this->createdAt = new DateTime();
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
     * @return Shipment
     */
    public function getShipment(): Shipment
    {
        return $this->shipment;
    }

    /**
     * @param Shipment $shipment
     */
    public function setShipment(Shipment $shipment): void
    {
        $this->shipment = $shipment;
    }

    /**
     * @return int|null
     */
    public function getStatusId(): ?int
    {
        return $this->statusId;
    }

    /**
     * @param int|null $statusId
     */
    public function setStatusId(?int $statusId): void
    {
        $this->statusId = $statusId;
    }

    /**
     * @return string|null
     */
    public function getCarrierStatusId(): ?string
    {
        return $this->carrierStatusId;
    }

    /**
     * @param string|null $carrierStatusId
     */
    public function setCarrierStatusId(?string $carrierStatusId): void
    {
        $this->carrierStatusId = $carrierStatusId;
    }

    /**
     * @return DateTime|null
     */
    public function getCarrierCreatedAt(): ?DateTime
    {
        return $this->carrierCreatedAt;
    }

    /**
     * @param DateTime|null $carrierCreatedAt
     */
    public function setCarrierCreatedAt(?DateTime $carrierCreatedAt): void
    {
        $this->carrierCreatedAt = $carrierCreatedAt;
    }

    /**
     * @return string|null
     */
    public function getCarrierDescription(): ?string
    {
        return $this->carrierDescription;
    }

    /**
     * @param string|null $carrierDescription
     */
    public function setCarrierDescription(?string $carrierDescription): void
    {
        $this->carrierDescription = $carrierDescription;
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
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }
}