<?php declare(strict_types=1);

namespace ComWerft\RemoveMigration\Profile\Magento19\PasswordEncoder;

use Shopware\Core\Checkout\Customer\Password\LegacyEncoder\LegacyEncoderInterface;

class MagentoEncoder implements LegacyEncoderInterface
{
    public function getName(): string
    {
        return 'Magento19';
    }

    public function isPasswordValid(string $password, string $hash): bool
    {
        if (\mb_strpos($hash, ':') !== false) {
            [$hash, $salt] = \explode(':', $hash);
            $password = $salt . $password;
        }

        return \hash_equals($hash, \md5($password))
            || \hash_equals($hash, \hash('sha256', $password))
            || \hash_equals($hash, \hash('sha512', $password))
            || \password_verify($password, $hash);
    }
}
