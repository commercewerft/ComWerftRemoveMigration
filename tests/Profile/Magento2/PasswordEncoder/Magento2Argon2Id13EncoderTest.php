<?php declare(strict_types=1);

namespace ComWerft\RemoveMigration\Test\Profile\Magento2\PasswordEncoder;

use ComWerft\RemoveMigration\Profile\Magento2\PasswordEncoder\Magento2Argon2Id13Encoder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class Magento2Argon2Id13EncoderTest extends TestCase
{
    private const PASSWORD = 'shopware';

    /**
     * sodium_crypto_pwhash() requires a salt of exactly SODIUM_CRYPTO_PWHASH_SALTBYTES (16) bytes.
     */
    private const SALT = 'ijPMR9EmxsMLwWLQ';

    private Magento2Argon2Id13Encoder $encoder;

    protected function setUp(): void
    {
        $this->encoder = new Magento2Argon2Id13Encoder();
    }

    public function testNameMatchesTheValueStoredByTheMigration(): void
    {
        static::assertSame('Magento2Argon2Id13', $this->encoder->getName());
    }

    public function testCorrectPasswordIsValid(): void
    {
        $this->requireSodium();

        static::assertTrue($this->encoder->isPasswordValid(self::PASSWORD, $this->hash()));
    }

    public function testWrongPasswordIsInvalid(): void
    {
        $this->requireSodium();

        static::assertFalse($this->encoder->isPasswordValid('wrong', $this->hash()));
    }

    public function testEmptyPasswordIsInvalid(): void
    {
        $this->requireSodium();

        static::assertFalse($this->encoder->isPasswordValid('', $this->hash()));
    }

    public function testOtherVersionIsRejected(): void
    {
        $this->requireSodium();

        $hash = \explode(':', $this->hash())[0] . ':' . self::SALT . ':1';

        static::assertFalse($this->encoder->isPasswordValid(self::PASSWORD, $hash));
    }

    #[DataProvider('malformedHashProvider')]
    public function testMalformedHashIsRejectedWithoutWarning(string $hash): void
    {
        static::assertFalse($this->encoder->isPasswordValid(self::PASSWORD, $hash));
    }

    public static function malformedHashProvider(): \Generator
    {
        yield 'no colon at all' => [\str_repeat('a', 64)];
        yield 'no version segment' => [\str_repeat('a', 64) . ':' . self::SALT];
        yield 'trailing colon only' => [\str_repeat('a', 64) . ':'];
        yield 'leading colon only' => [':' . self::SALT];
        yield 'empty hash' => [''];
        yield 'colons only' => ['::'];
    }

    /**
     * A salt of the wrong length makes sodium_crypto_pwhash() throw. A stored hash must never
     * be able to turn a login attempt into a 500.
     */
    public function testWrongSaltLengthIsRejectedWithoutThrowing(): void
    {
        $this->requireSodium();

        $hash = \str_repeat('a', 64) . ':tooshort:2';

        static::assertFalse($this->encoder->isPasswordValid(self::PASSWORD, $hash));
    }

    private function hash(): string
    {
        $hash = \bin2hex(
            \sodium_crypto_pwhash(
                \SODIUM_CRYPTO_SIGN_SEEDBYTES,
                self::PASSWORD,
                self::SALT,
                \SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
                \SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
                \SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
            )
        );

        return $hash . ':' . self::SALT . ':2';
    }

    private function requireSodium(): void
    {
        if (!\extension_loaded('sodium') || !\defined('SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13')) {
            static::markTestSkipped('ext-sodium with Argon2id13 support is required');
        }
    }
}
