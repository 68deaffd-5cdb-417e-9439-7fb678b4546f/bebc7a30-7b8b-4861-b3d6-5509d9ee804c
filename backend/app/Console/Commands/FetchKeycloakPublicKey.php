<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchKeycloakPublicKey extends Command
{
    protected $signature = 'keycloak:fetch-public-key
                            {url : The JWKS endpoint URL, e.g. https://keycloak.example.com/realms/myrealm/protocol/openid-connect/certs}
                            {--output= : Output file path, default: storage/oauth-public.pem}';

    protected $description = 'Fetch Keycloak public RSA key from JWKS endpoint and save as PEM file';

    public function handle()
    {
        $jwksUrl = $this->argument('url');
        $outputPath = $this->option('output') ?? storage_path('oauth-public.pem');

        $this->info("Fetching JWKS from: {$jwksUrl}");

        $response = Http::get($jwksUrl);

        if (!$response->ok()) {
            $this->error("Failed to fetch JWKS: HTTP {$response->status()}");
            return 1;
        }

        $jwks = $response->json();

        // Filter for RSA keys with alg=RS256
        $key = collect($jwks['keys'] ?? [])->first(function ($k) {
            return ($k['kty'] ?? null) === 'RSA' && ($k['alg'] ?? null) === 'RS256';
        });

        if (!$key) {
            $this->error("No RSA key with alg=RS256 found in JWKS");
            return 1;
        }

        $pem = $this->convertJwkToPem($key);

        if (!$pem) {
            $this->error("Failed to convert JWK to PEM");
            return 1;
        }

        file_put_contents($outputPath, $pem);

        $this->info("Public key saved to {$outputPath}");

        return 0;
    }

    protected function convertJwkToPem(array $jwk): ?string
    {
        $modulus = $this->base64UrlDecode($jwk['n']);
        $exponent = $this->base64UrlDecode($jwk['e']);

        $modulusEncoded = $this->encodeAsn1Integer($modulus);
        $exponentEncoded = $this->encodeAsn1Integer($exponent);

        // Sequence of modulus and exponent
        $sequence = chr(0x30) . $this->encodeLength(strlen($modulusEncoded) + strlen($exponentEncoded)) . $modulusEncoded . $exponentEncoded;

        // RSA Encryption OID
        $rsaOid = pack('H*', '300d06092a864886f70d0101010500');

        // Bit string wrapping the sequence
        $bitString = chr(0x03) . $this->encodeLength(strlen($sequence) + 1) . chr(0x00) . $sequence;

        // Final sequence: AlgorithmIdentifier + BitString
        $publicKey = chr(0x30) . $this->encodeLength(strlen($rsaOid) + strlen($bitString)) . $rsaOid . $bitString;

        // PEM format
        $pem = "-----BEGIN PUBLIC KEY-----\n"
            . chunk_split(base64_encode($publicKey), 64)
            . "-----END PUBLIC KEY-----\n";

        return $pem;
    }

    protected function encodeAsn1Integer(string $data): string
    {
        // Add leading zero if high bit is set (to indicate positive number)
        if (ord($data[0]) > 0x7f) {
            $data = "\x00" . $data;
        }

        return chr(0x02) . $this->encodeLength(strlen($data)) . $data;
    }

    protected function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padLen = 4 - $remainder;
            $data .= str_repeat('=', $padLen);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    protected function encodeLength(int $length): string
    {
        if ($length <= 0x7F) {
            return chr($length);
        }
        $temp = ltrim(pack('N', $length), "\x00");
        return chr(0x80 | strlen($temp)) . $temp;
    }
}
