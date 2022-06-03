<?php

declare(strict_types=1);

namespace App\Service\Label;

use App\Config;

abstract class BaseLabel
{
    protected \TCPDF $pdf;
    protected Config $config;

    protected const ORIGIN_X = 0;
    protected const ORIGIN_Y = 0;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->pdf = new \TCPDF();
        $this->pdf->SetFont(family: 'dejavusanscondensedb', size: 12);
        $this->pdf->SetPrintHeader(false);
        $this->pdf->SetPrintFooter(false);
    }

    protected function addText(string $text, float $w, float $h, float $x = null, float $y = null, float $fontSize = 12, $align = 'C', $vertical = 0, $border = 0): void
    {
        static $lastX, $lastY;

        $this->pdf->SetXY(($x ?? $lastX) + self::ORIGIN_X, ($y ?? $lastY) + self::ORIGIN_Y);

        if ($vertical !== 0) {
            $this->pdf->StartTransform();
            if ($vertical > 0) {
                $this->pdf->TranslateX($w);
            } else {
                $this->pdf->TranslateY($h);
            }
            $this->pdf->Rotate(-90 * $vertical);
        }

        $this->pdf->SetFontSize($fontSize);
        $this->pdf->SetLineWidth($border);
        $this->pdf->Cell($vertical !== 0 ? $h : $w, $vertical !== 0 ? $w : $h, $text, $border ? 1 : 0, 1, $align, false, '', 1, true, 'T', 'M');

        if ($vertical) {
            $this->pdf->StopTransform();
        }

        $lastX = $x ?? $lastX;
        $lastY = $this->pdf->GetY() - self::ORIGIN_Y;
    }

    protected function addFrame($x, $y, $w, $h, $filled = false): void
    {
        $this->pdf->SetLineWidth(.2);
        $this->pdf->Rect($x + self::ORIGIN_X, $y + self::ORIGIN_Y, $w, $h, 'DF', [], $filled ? [255] : []);
    }

    protected function addBarcode(string $type, string $code, float $x, float $y, float $w, float $h, bool $text = true,  $vertical = 0): void
    {
        $this->pdf->SetXY($x + self::ORIGIN_X, $y + self::ORIGIN_Y);

        if ($vertical !== 0) {
            $this->pdf->StartTransform();
            if ($vertical > 0) {
                $this->pdf->TranslateX($w);
            } else {
                $this->pdf->TranslateY($h);
            }
            $this->pdf->Rotate(-90 * $vertical);
        }

        $this->pdf->write1DBarcode($code, $type, '', '', $vertical !== 0 ? $h : $w, $vertical !== 0 ? $w : $h, 0.1, [
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => 0,
            'hpadding' => 0,
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'text' => $text,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 3,
        ], align: 'T');

        if ($vertical) {
            $this->pdf->StopTransform();
        }
    }
}