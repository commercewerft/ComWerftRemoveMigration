<?php declare(strict_types=1);

namespace ComWerft\RemoveMigration\Profile\Magento19\PasswordEncoder;

use Shopware\Core\Checkout\Customer\Password\LegacyEncoder\LegacyEncoderInterface;

class MagentoEncoder implements LegacyEncoderInterface
{
    public const NAME = 'Magento19';

    public function getName(): string
    {
        return self::NAME;
    }

    public function isPasswordValid(#[\SensitiveParameter] string $password, string $hash): bool
    {
        if (\str_contains($hash, ':')) {
            [$hash, $salt] = \explode(':', $hash);
            $password = $salt . $password;
        }

        return \hash_equals($hash, \md5($password))
            || \hash_equals($hash, \hash('sha256', $password))
            || \hash_equals($hash, \hash('sha512', $password))
            || \password_verify($password, $hash);
    }
}
