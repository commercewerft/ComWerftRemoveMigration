<?php declare(strict_types=1);

namespace ComWerft\RemoveMigration\Test\Profile\Magento19\PasswordEncoder;

use ComWerft\RemoveMigration\Profile\Magento19\PasswordEncoder\MagentoEncoder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MagentoEncoderTest extends TestCase
{
    private const PASSWORD = 'shopware';
    private const SALT = 'ijPMR9Em';

    private MagentoEncoder $encoder;

    protected function setUp(): void
    {
        $this->encoder = new MagentoEncoder();
    }

    public function testNameMatchesTheValueStoredByTheMigration(): void
    {
        static::assertSame('Magento19', $this->encoder->getName());
    }

    #[DataProvider('validHashProvider')]
    public function testCorrectPasswordIsValid(string $hash): void
    {
        static::assertTrue($this->encoder->isPasswordValid(self::PASSWORD, $hash));
    }

    #[DataProvider('validHashProvider')]
    public function testWrongPasswordIsInvalid(string $hash): void
    {
        static::assertFalse($this->encoder->isPasswordValid('wrong', $hash));
    }

    public static function validHashProvider(): \Generator
    {
        yield 'salted md5' => [\md5(self::SALT . self::PASSWORD) . ':' . self::SALT];
        yield 'salted sha256' => [\hash('sha256', self::SALT . self::PASSWORD) . ':' . self::SALT];
        yield 'salted sha512' => [\hash('sha512', self::SALT . self::PASSWORD) . ':' . self::SALT];
        yield 'unsalted md5' => [\md5(self::PASSWORD)];
        yield 'unsalted sha256' => [\hash('sha256', self::PASSWORD)];
        yield 'unsalted sha512' => [\hash('sha512', self::PASSWORD)];
        yield 'bcrypt' => [\password_hash(self::PASSWORD, \PASSWORD_BCRYPT)];
        yield 'salted bcrypt' => [\password_hash(self::SALT . self::PASSWORD, \PASSWORD_BCRYPT) . ':' . self::SALT];
    }

    #[DataProvider('malformedHashProvider')]
    public function testMalformedHashIsRejectedWithoutWarning(string $hash): void
    {
        static::assertFalse($this->encoder->isPasswordValid(self::PASSWORD, $hash));
    }

    public static function malformedHashProvider(): \Generator
    {
        yield 'empty hash' => [''];
        yield 'colon only' => [':'];
        yield 'leading colon only' => [':' . self::SALT];
        yield 'garbage' => ['not-a-hash'];
    }
}
