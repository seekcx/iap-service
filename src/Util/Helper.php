<?php

namespace Simplephp\IapService\Util;

class Helper
{
    /**
     * @param string $input
     * @return string
     */
    public static function base64Encode(string $input): string
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * @param string $input
     * @return string
     */
    public static function base64Decode(string $input): string
    {
        $remainder = strlen($input) % 4;

        if ($remainder) {
            $input .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * @param string $certificate
     * @return string
     */
    public static function formatPEM(string $certificate): string
    {
        if (strpos($certificate, "-----BEGIN CERTIFICATE-----\n") !== false) {
            return $certificate;
        }

        return join("\n", [
            "-----BEGIN CERTIFICATE-----",
            $certificate,
            "-----END CERTIFICATE-----"
        ]);
    }

    /**
     * @param string $binary
     * @return string
     */
    public static function toPEM(string $binary): string
    {
        return trim(chunk_split(base64_encode($binary), 64));
    }
}