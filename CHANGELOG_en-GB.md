# 0.2.0

* Compatible with Shopware 6.5, 6.6 and 6.7 (for Shopware 6.4 please keep using version 0.1.0)
* Fixed: an Argon2id13 hash with an invalid salt length raised a SodiumException during login, causing a server error instead of rejecting the login
* Fixed: Magento 2 hashes without a version segment produced PHP warnings in the error log during login
* Passwords are marked as `#[\SensitiveParameter]` and no longer appear in stack traces
* Added unit tests for all four password encoders

# 0.1.0

* First release
