<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Str;

class QrSignatureService
{
    /**
     * Generate a unique QR verification token.
     */
    public function generateToken(): string
    {
        return Str::random(32);
    }

    /**
     * Generate QR code as base64 data URI.
     */
    public function generateQrCode(string $token, int $size = 5): string
    {
        $verificationUrl = url("/verifikasi/{$token}");

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => $size,
            'imageBase64' => true,
            'quietzoneSize' => 2,
        ]);

        return (new QRCode($options))->render($verificationUrl);
    }

    /**
     * Generate QR code as raw PNG binary (for PDF embedding).
     */
    public function generateQrCodePng(string $token, int $size = 5): string
    {
        $verificationUrl = url("/verifikasi/{$token}");

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale' => $size,
            'imageBase64' => false,
            'quietzoneSize' => 2,
        ]);

        return (new QRCode($options))->render($verificationUrl);
    }
}
