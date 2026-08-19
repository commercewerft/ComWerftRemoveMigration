<?php declare(strict_types=1);

namespace ComWerft\RemoveMigration\Test\Profile\Magento2\PasswordEncoder;

use ComWerft\RemoveMigration\Profile\Magento2\PasswordEncoder\Magento2Md5Encoder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Magento2Md5EncoderTest extends TestCase
{
    private const PASSWORD = 'shopware';
    private const SALT = 'ijPMR9EmxsMLwWLQ';

    private Magento2Md5Encoder $encoder;

    protected function setUp(): void
    {
        $this->encoder = new Magento2Md5Encoder();
    }

    public function testNameMatchesTheValueStoredByTheMigration(): void
    {
        static::assertSame('Magento2Md5', $this->encoder->getName());
    }

    public function testCorrectPasswordIsValid(): void
    {
        static::assertTrue($this->encoder->isPasswordValid(self::PASSWORD, $this->hash()));
    }

    public function testWrongPasswordIsInvalid(): void
    {
        static::assertFalse($this->encoder->isPasswordValid('wrong', $this->hash()));
    }

    public function testEmptyPasswordIsInvalid(): void
    {
        static::assertFalse($this->encoder->isPasswordValid('', $this->hash()));
    }

    public function testOtherVersionIsRejected(): void
    {
        $hash = \md5(self::SALT . self::PASSWORD) . ':' . self::SALT . ':1';

        static::assertFalse($this->encoder->isPasswordValid(self::PASSWORD, $hash));
    }

    /**
     * Magento does not always store the version segment. Such a hash belongs to another
     * encoder and must be rejected quietly - it must not raise a PHP warning in the login path.
     */
    #[DataProvider('malformedHashProvider')]
    public function testMalformedHashIsRejectedWithoutWarning(string $hash): void
    {
        static::assertFalse($this->encoder->isPasswordValid(self::PASSWORD, $hash));
    }

    public static function malformedHashProvider(): \Generator
    {
        yield 'no colon at all' => [\md5(self::PASSWORD)];
        yield 'no version segment' => [\md5(self::SALT . self::PASSWORD) . ':' . self::SALT];
        yield 'trailing colon only' => [\md5(self::PASSWORD) . ':'];
        yield 'leading colon only' => [':' . self::SALT];
        yield 'empty hash' => [''];
        yield 'colons only' => ['::'];
    }

    private function hash(): string
    {
        return \md5(self::SALT . self::PASSWORD) . ':' . self::SALT . ':0';
    }
}
