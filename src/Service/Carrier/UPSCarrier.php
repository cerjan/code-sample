<?php

declare(strict_types=1);

namespace App\Service\Carrier;

use App\Config;
use App\Entity\Package;
use App\Entity\Shipment;
use App\Service\Mapper\UPSStatusMapper;
use App\Utils\Arrays;
use App\Utils\HealthCheck;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Nette\Utils\FileSystem;
use Nette\Utils\Image;
use Slim\Exception\HttpBadRequestException;
use stdClass;

final class UPSCarrier extends Carrier
{
    private Client $client;

    public function __construct(
        private Config $config,
    ) {
        $this->client = new Client((array) $this->config->UPS->client);
    }

    public function registerShipment(Shipment $shipment): void
    {
        $payload = $this->request('POST', '/ship/v1/shipments', Arrays::removeNullValuesRecursive($this->getCreateRequestArray($shipment)), ['additionaladdressvalidation' => 'city']);

        $carrierPackages = $payload->ShipmentResponse->ShipmentResults->PackageResults;

        if (!is_array($carrierPackages)) {
            $carrierPackages = [$carrierPackages];
        }

        $shipment->setShipmentNumber($payload->ShipmentResponse->ShipmentResults->ShipmentIdentificationNumber);

        foreach ($shipment->getPackages() as $i => $package) {
            $package->setCarrierParcelNumber($carrierPackages[$i]->TrackingNumber);
            $package->setLabel($carrierPackages[$i]->TrackingNumber . '.png');
            $this->saveLabel($package, $carrierPackages[$i]->ShippingLabel->GraphicImage);
            $this->em->flush();
        }

        $shipment->setLabeled();
    }

    public function cancelShipment(Shipment $shipment): void
    {
        $this->request('DELETE', '/ship/v1/shipments/cancel/' . $shipment->getShipmentNumber());
    }

    public function retrieveStatus(Shipment $shipment): void
    {
        $payload = $this->request('GET', '/track/v1/details/' . $shipment->getShipmentNumber());

        if ($records = $payload->trackResponse->shipment[0]->package[0]->activity ?? null) {
            foreach ($records as $carrierHistoryRecord) {
                $shipment->addHistory(UPSStatusMapper::from($carrierHistoryRecord));
            }
        } else {
            $this->logger->warning('UPS - /track/v1/details/' . $shipment->getShipmentNumber(), ['response' => $payload]);
            throw new HttpBadRequestException($this->request, 'UPS - /track/v1/details/' . $shipment->getShipmentNumber());
        }
    }

    public function isHealthy(): ?bool
    {
        return HealthCheck::url(rtrim($this->config->UPS->client->base_uri, '/') . '/ship/v1/shipments');
    }

    private function getCreateRequestArray(Shipment $shipment): array
    {
        $packagesRequest = [];

        foreach ($shipment->getPackages() as $package) {
            $packagesRequest[] = [
                'Description' => $this->config->UPS->shipment->description,
                'Packaging' => [
                    'Code' => $this->config->UPS->shipment->packagingCode,
                ],
//                'Dimensions' => [
//                    'UnitOfMeasurement' => [
//                        'Code' => 'CM'
//                    ],
//                    'Length' => (string) self::mm2cm($package->getLength()),
//                    'Width' => (string) self::mm2cm($package->getWidth()),
//                    'Height' => (string) self::mm2cm($package->getHeight()),
//                ],
                'PackageWeight' => [
                    'UnitOfMeasurement' => [
                        'Code' => 'KGS',
                    ],
                    'Weight' => (string) $package->getWeight(),
                ],
            ];
        }

        return [
            'ShipmentRequest' => [
                'Shipment' => [
                    'Description' => $this->config->UPS->shipment->description,
                    'ReferenceNumber' => [
                        'Value' => $shipment->getDeliveryNote(),
                    ],
                    'Shipper' => [
                        'Name' => $shipment->getSender()->getCompany()->getName(),
                        'AttentionName' => $shipment->getSender()->getContact()->getFullName(),
                        'CompanyDisplayableName' => $shipment->getSender()->getCompany()->getName(),
                        'Phone' => [
                            'Number' => $shipment->getSender()->getContact()->getPhone(),
                        ],
                        'ShipperNumber' => $this->config->UPS->auth->transId,
                        'EMailAddress' => $shipment->getSender()->getContact()->getEmail(),
                        'Address' => [
                            'AddressLine' => $shipment->getSender()->getAddress()->getAddressLine(),
                            'City' => $shipment->getSender()->getAddress()->getCity(),
                            'PostalCode' => $shipment->getSender()->getAddress()->getZipCode(),
                            'CountryCode' => $shipment->getSender()->getAddress()->getCountry()->getCode(),
                        ],
                    ],
                    'ShipTo' => [
                        'Name' => $shipment->getRecipient()->getCompany()->getName(),
                        'TaxIdentificationNumber' => $shipment->getRecipient()->getCompany()->getIdVatNumber(),
                        'AttentionName' => $shipment->getRecipient()->getContact()->getFullName(),
                        'Phone' => [
                            'Number' => $shipment->getRecipient()->getContact()->getPhone(),
                        ],
                        'EMailAddress' => $shipment->getRecipient()->getContact()->getEmail(),
                        'Address' => [
                            'AddressLine' => $shipment->getRecipient()->getAddress()->getAddressLine(),
                            'City' => $shipment->getRecipient()->getAddress()->getCity(),
                            'PostalCode' => $shipment->getRecipient()->getAddress()->getZipCode(),
                            'CountryCode' => $shipment->getRecipient()->getAddress()->getCountry()->getCode(),
                        ],
                    ],
                    'PaymentInformation' => [
                        'ShipmentCharge' => [
                            'Type' => '01',
                            'BillShipper' => [
                                'AccountNumber' => $this->config->UPS->auth->transId,
                            ],
                        ],
                    ],
                    'Service' => [
                        'Code' => $this->config->UPS->shipment->serviceCode,
                    ],
                    'NumOfPiecesInShipment' => $shipment->getPackages()->count(),
                    'ShipmentServiceOptions' => [
                        'COD' => ($shipment->getService('cod') ? [
                            'CODFundsCode' => $this->config->UPS->shipment->codFundsCode,
                            'CODAmount' => [
                                'CurrencyCode' => $shipment->getService('cod')->getParams()['currency'],
                                'MonetaryValue' => (string) $shipment->getService('cod')->getParams()['value'],
                            ],
                        ] : null),
                    ],
                    'Package' => $packagesRequest,
                ],
                'LabelSpecification' => [
                    'LabelImageFormat' => [
                        'Code' => 'PNG',
                    ],
                ],
            ],
        ];
    }

    private function request(string $method, string $uri, ?array $json = null, ?array $query = null): stdClass
    {
        try {
            $response = $this->client->request($method, $uri, [
                'headers' => (array) $this->config->UPS->auth + ['Accept' => '*/*'],
                'json' => $json,
                'query' => $query,
            ]);

            $payload = json_decode($response->getBody()->getContents());
            $this->logger->info('UPS - ' . strtoupper($method) . ' - ' . $uri, ['response' => $payload, 'json' => $json]);

            return $payload;
        } catch (ClientException | ServerException $e) {
            $payload = json_decode($e->getResponse()->getBody()->getContents());

            if ($errors = $payload->response->errors ?? null) {
                $this->logger->error('UPS - ' . strtoupper($method) . ' - ' . $uri, $errors);
                throw new \Exception('UPS - ' . strtoupper($method) . ' - ' . $uri . ' - ' . implode(' | ', array_map(fn($v) => $v->message, $errors)));
            } else {
                throw new \Exception('UPS - ' . strtoupper($method) . ' - ' . $uri);
            }
        }
    }

    /**
     * @param Package $package
     * @param string $data Image data - base64 encoded.
     * @throws \Nette\Utils\ImageException
     */
    private function saveLabel(Package $package, string $data): void
    {
        $image = Image::fromString(base64_decode($data));
        $image->rotate(-90, Image::rgb(255, 255, 255));
        $image->cropAuto(IMG_CROP_WHITE);
        FileSystem::write($this->config->shareDir . $package->getLabelDir() . $package->getLabel(), $image->toString(Image::PNG));
    }

    private static function mm2cm(?float $x): ?int
    {
        return $x ? (int) round($x / 10) : null;
    }
}