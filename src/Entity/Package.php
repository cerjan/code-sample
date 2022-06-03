<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Index(fields: ['loadedAt'])]
#[ORM\Index(fields: ['carrierParcelNumber'])]
class Package
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $number;

    #[ORM\ManyToOne(targetEntity: Shipment::class, inversedBy: 'packages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Shipment $shipment;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $weight;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $carrierParcelNumber;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $trackingNumber;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $label;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $labelDir;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $height;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $width;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $length;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $volume;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $loadedAt;

    /**
     * @param int $number
     * @param float|null $weight
     * @param float|null $height
     * @param float|null $width
     * @param float|null $length
     * @param float|null $volume
     */
    public function __construct(int $number, ?float $weight, ?float $height, ?float $width, ?float $length, ?float $volume)
    {
        $this->number = $number;
        $this->weight = $weight;
        $this->height = $height;
        $this->width = $width;
        $this->length = $length;
        $this->volume = $volume;

        $this->label = null;
        $this->labelDir = null;
    }

    public function isFirst(): bool
    {
        return $this->number === 1;
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
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
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
     * @return float|null
     */
    public function getWeight(): ?float
    {
        return $this->weight;
    }

    /**
     * @param float|null $weight
     */
    public function setWeight(?float $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return string|null
     */
    public function getCarrierParcelNumber(): ?string
    {
        return $this->carrierParcelNumber;
    }

    /**
     * @param string|null $carrierParcelNumber
     */
    public function setCarrierParcelNumber(?string $carrierParcelNumber): void
    {
        $this->carrierParcelNumber = $carrierParcelNumber;
    }

    /**
     * @return string|null
     */
    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    /**
     * @param string|null $trackingNumber
     */
    public function setTrackingNumber(?string $trackingNumber): void
    {
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
        $this->labelDir = $label
            ? '/labels/' . $this->shipment->getCarrier()->getCode() . $this->shipment->getCreatedAt()->format('/Y/m/d/H/')
            : null;
    }

    /**
     * @return float|null
     */
    public function getHeight(): ?float
    {
        return $this->height;
    }

    /**
     * @param float|null $height
     */
    public function setHeight(?float $height): void
    {
        $this->height = $height;
    }

    /**
     * @return float|null
     */
    public function getWidth(): ?float
    {
        return $this->width;
    }

    /**
     * @param float|null $width
     */
    public function setWidth(?float $width): void
    {
        $this->width = $width;
    }

    /**
     * @return float|null
     */
    public function getLength(): ?float
    {
        return $this->length;
    }

    /**
     * @param float|null $length
     */
    public function setLength(?float $length): void
    {
        $this->length = $length;
    }

    /**
     * @return float|null
     */
    public function getVolume(): ?float
    {
        return $this->volume;
    }

    /**
     * @param float|null $volume
     */
    public function setVolume(?float $volume): void
    {
        $this->volume = $volume;
    }

    /**
     * @return string|null
     *
     * Returns the relative path to the image of the shipping label beginning and ending with a slash.
     */
    public function getLabelDir(): ?string
    {
        return $this->labelDir;
    }

    /**
     * @param string|null $labelDir
     */
    public function setLabelDir(?string $labelDir): void
    {
        $this->labelDir = $labelDir;
    }
}