<?php declare(strict_types=1);

namespace ComWerft\RemoveMigration\Profile\Magento2\PasswordEncoder;

use Shopware\Core\Checkout\Customer\Password\LegacyEncoder\LegacyEncoderInterface;

class Magento2Md5Encoder implements LegacyEncoderInterface
{
    public const NAME = 'Magento2Md5';

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

        [$md5, $salt, $version] = $parts;

        if ($version !== '0') {
            return false;
        }

        return \hash_equals($md5, \md5($salt . $password));
    }
}
