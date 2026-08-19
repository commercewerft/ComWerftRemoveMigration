<?php declare(strict_types=1);

namespace ComWerft\RemoveMigration\Test\Profile\Magento2\PasswordEncoder;

use ComWerft\RemoveMigration\Profile\Magento2\PasswordEncoder\Magento2Sha256Encoder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Magento2Sha256EncoderTest extends TestCase
{
    private const PASSWORD = 'shopware';
    private const SALT = 'ijPMR9EmxsMLwWLQ';

    private Magento2Sha256Encoder $encoder;

    protected function setUp(): void
    {
        $this->encoder = new Magento2Sha256Encoder();
    }

    public function testNameMatchesTheValueStoredByTheMigration(): void
    {
        static::assertSame('Magento2Sha256', $this->encoder->getName());
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
        $hash = \hash('sha256', self::SALT . self::PASSWORD) . ':' . self::SALT . ':0';

        static::assertFalse($this->encoder->isPasswordValid(self::PASSWORD, $hash));
    }

    #[DataProvider('malformedHashProvider')]
    public function testMalformedHashIsRejectedWithoutWarning(string $hash): void
    {
        static::assertFalse($this->encoder->isPasswordValid(self::PASSWORD, $hash));
    }

    public static function malformedHashProvider(): \Generator
    {
        yield 'no colon at all' => [\hash('sha256', self::PASSWORD)];
        yield 'no version segment' => [\hash('sha256', self::SALT . self::PASSWORD) . ':' . self::SALT];
        yield 'trailing colon only' => [\hash('sha256', self::PASSWORD) . ':'];
        yield 'leading colon only' => [':' . self::SALT];
        yield 'empty hash' => [''];
        yield 'colons only' => ['::'];
    }

    private function hash(): string
    {
        return \hash('sha256', self::SALT . self::PASSWORD) . ':' . self::SALT . ':1';
    }
}
