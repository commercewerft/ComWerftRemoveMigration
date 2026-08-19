<?php declare(strict_types=1);

namespace ComWerft\RemoveMigration\Profile\Magento2\PasswordEncoder;

use Shopware\Core\Checkout\Customer\Password\LegacyEncoder\LegacyEncoderInterface;

class Magento2Argon2Id13Encoder implements LegacyEncoderInterface
{
    public const NAME = 'Magento2Argon2Id13';

    public function getName(): string
    {
        return self::NAME;
    }

    public function isPasswordValid(#[\SensitiveParameter] string $password, string $hash): bool
    {
        $parts = \explode(':', $hash, 3);

        if (\count($parts) !== 3) {
            return false;
        }

        [$expectedHash, $salt, $version] = $parts;

        if ($version !== '2'
            || $password === ''
            || !\extension_loaded('sodium')
            || !\defined('SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13')
        ) {
            return false;
        }

        // sodium_crypto_pwhash() throws on a salt of the wrong length. A stored hash must never
        // be able to turn a login attempt into a 500.
        if (\strlen($salt) !== \SODIUM_CRYPTO_PWHASH_SALTBYTES) {
            return false;
        }

        try {
            $challengeHash = \bin2hex(
                \sodium_crypto_pwhash(
                    \SODIUM_CRYPTO_SIGN_SEEDBYTES,
                    $password,
                    $salt,
                    \SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                    \SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
                    \SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
                )
            );
        } catch (\SodiumException) {
            return false;
        }

        return \hash_equals($expectedHash, $challengeHash);
    }
}
