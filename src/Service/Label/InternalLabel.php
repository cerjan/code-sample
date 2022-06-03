<?php

declare(strict_types=1);

namespace App\Service\Label;

use App\Entity\Package;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

final class InternalLabel extends BaseLabel
{
    public function create(Package $package): void
    {
        $sender = $package->getShipment()->getSender();
        $recipient = $package->getShipment()->getRecipient();

        $pdf = new \TCPDF();
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage(format: [110, 200]);
        $pdf->SetAutoPageBreak(false);

        $pdf->StartTransform();
        $pdf->TranslateY(200);
        $pdf->Rotate(90, 0, 0);

        $pdf->setCellPaddings(left: 3);

        $pdf->Rect(x: 0, y: 0, w: 200, h: 110, style: 'F', fill_color: [255]);

        $pdf->Rect(x: 70, y: 0, w: 130, h: 40);
        $pdf->Rect(x: 70, y: 40, w: 130, h: 32);
        $pdf->Rect(x: 70, y: 72, w: 65, h: 38);
        $pdf->Rect(x: 135, y: 72, w: 65, h: 38);
        $pdf->Rect(x: 0, y: 0, w: 70, h: 110);

        $pdf->setLeftMargin(70);
        $pdf->setXY(70, 0);

        $pdf->setFont(family: 'dejavusanscondensed', size: 28);
        $pdf->Cell(w: 130, h: 40, txt: Strings::upper($package->getShipment()->getCarrier()->getName()), ln: 1, align: 'C', stretch: 1, valign: 'C');

        $pdf->setFont(family: 'dejavusanscondensed', size: 10);
        $pdf->Cell(w: 40, h: 8, txt: 'Objednávka:', ignore_min_height: true, valign: 'C');
        $pdf->setFont(family: 'dejavusanscondensed', size: 14);
        $pdf->Cell(w: 110, h: 8, txt: $package->getShipment()->getShipmentNote() ?? '', ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');
        $pdf->setFont(family: 'dejavusanscondensed', size: 10);
        $pdf->Cell(w: 40, h: 8, txt: 'Výdajový list:', ignore_min_height: true, valign: 'C');
        $pdf->setFont(family: 'dejavusanscondensed', size: 14);
        $pdf->Cell(w: 110, h: 8, txt: $package->getShipment()->getDeliveryNote(), ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');
        $pdf->setFont(family: 'dejavusanscondensed', size: 10);
        $pdf->Cell(w: 40, h: 8, txt: 'Balík č. / celkem:', ignore_min_height: true, valign: 'C');
        $pdf->setFont(family: 'dejavusanscondensed', size: 14);
        $pdf->Cell(w: 110, h: 8, txt: $package->getNumber() . ' / ' . $package->getShipment()->getPackages()->count(), ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');

        $pdf->setY(72);
        $pdf->setFont(family: 'dejavusanscondensed', size: 8);
        $pdf->Cell(w: 65, h: 8, txt: 'Přijemce:', ignore_min_height: true, valign: 'C');
        $pdf->Cell(w: 65, h: 8, txt: 'Odesílatel:', ln: 1, ignore_min_height: true, valign: 'C');

        $pdf->setFont(family: 'dejavusanscondensed', size: 12);

        $pdf->Cell(w: 75, h: 5.5, txt: $recipient->getContact()->getFullName(), ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');
        $pdf->Cell(w: 75, h: 5.5, txt: $recipient->getCompany()->getName(), ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');
        $pdf->Cell(w: 75, h: 5.5, txt: $recipient->getAddress()->getAddressLine(), ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');
        $pdf->Cell(w: 75, h: 5.5, txt: $recipient->getAddress()->getZipCode() . '  ' . $recipient->getAddress()->getCity(), ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');
        $pdf->Cell(w: 75, h: 5.5, txt: $recipient->getAddress()->getCountry()->getName(), ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');

        $pdf->setLeftMargin(135);
        $pdf->setY(72 + 8);
        $pdf->Cell(w: 75, ln: 1, ignore_min_height: true);

        $pdf->Cell(w: 75, h: 5.5, txt: $sender->getCompany()->getName(), ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');
        $pdf->Cell(w: 75, h: 5.5, txt: $sender->getAddress()->getAddressLine(), ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');
        $pdf->Cell(w: 75, h: 5.5, txt: $sender->getAddress()->getZipCode() . '  ' . $sender->getAddress()->getCity(), ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');
        $pdf->Cell(w: 75, h: 5.5, txt: $sender->getAddress()->getCountry()->getName(), ln: 1, stretch: 1, ignore_min_height: true, valign: 'C');

        $pdf->StopTransform();

        $pdf->write1DBarcode(code: $package->getCarrierParcelNumber(), type: 'C128B', x: 10, y: 145, w: 90, h: 38, style: [
            'text' => $package->getCarrierParcelNumber(),
            'stretchtext' => 0,
            'align' => 'C',
            'stretch' => true,
        ]);

        $package->setLabel($package->getCarrierParcelNumber() . '.png');
        $dir = $this->config->shareDir . $package->getLabelDir();

        FileSystem::createDir($dir);

        $blob = $pdf->Output(dest: 'S');

        $im = new \Imagick();
        $im->setResolution(200, 200);
        $im->readImageBlob($blob);
        $im->setImageFormat('png');
        $im->writeImage($dir . $package->getLabel());
    }
}