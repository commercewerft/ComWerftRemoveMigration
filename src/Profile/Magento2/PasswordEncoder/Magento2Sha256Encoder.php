<?php declare(strict_types=1);

namespace ComWerft\RemoveMigration\Profile\Magento2\PasswordEncoder;

use Shopware\Core\Checkout\Customer\Password\LegacyEncoder\LegacyEncoderInterface;

class Magento2Sha256Encoder implements LegacyEncoderInterface
{
    public const NAME = 'Magento2Sha256';

    public function getName(): string
    {
        return self::NAME;
    }

    public function isPasswordValid(string $password, string $hash): bool
    {
        if (\mb_strpos($hash, ':') === false) {
            return false;
        }
        [$sha256, $salt, $version] = \explode(':', $hash, 3);

        if ($version !== '1') {
            return false;
        }

        return \hash_equals($sha256, \hash('sha256', $salt . $password));
    }
}
