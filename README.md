# ComWerftRemoveMigration

Enables you to remove the Shopware 6 Migration Assistant including the Magento migration profile.

When you have done a migration from Magento 1 or Magento 2 with customers, you are not allowed to
disable those plugins, as they are needed for the login of the customers created within Magento.

After a customer logs in, Shopware replaces the encoder for the password and uses its internal
encoder. So you have to wait until all customers have logged in.

If you disable the migration plugins, you will get an error message when a migrated customer logs
in for the first time.

This small plugin ships only the password encoders. So you are able to uninstall
`SwagMigrationAssistant` and `SwagMigrationMagento`.

## Compatibility

| Plugin version | Shopware      | PHP   |
|----------------|---------------|-------|
| 0.2.0          | 6.5, 6.6, 6.7 | >=8.1 |
| 0.1.0          | 6.4           | >=7.4 |

## Installation

1. Deactivate or uninstall the Shopware Migration Assistant (`SwagMigrationAssistant`).
2. Deactivate or uninstall the Magento migration profile (`SwagMigrationMagento`).
3. Install and activate this plugin.

Do not run this plugin alongside `SwagMigrationMagento`: both register encoders under the same
names (`Magento19`, `Magento2Md5`, `Magento2Sha256`, `Magento2Argon2Id13`), so one silently
overrides the other.

## Encoders

The encoder names are the values the migration wrote into `customer.legacy_encoder`, so they must
never change.

| Name                 | Source                                                       |
|----------------------|--------------------------------------------------------------|
| `Magento19`          | Magento 1.9: md5, sha256, sha512 and bcrypt, salted or not   |
| `Magento2Md5`        | Magento 2, hash version `0`: `md5(salt + password)`          |
| `Magento2Sha256`     | Magento 2, hash version `1`: `sha256(salt + password)`       |
| `Magento2Argon2Id13` | Magento 2, hash version `2`: Argon2id13 via ext-sodium       |

## Development

```bash
composer install
composer test                 # phpunit
shopware-cli extension validate .
shopware-cli extension zip .
```

## Links

The code is maintained on GitHub: https://github.com/commercewerft/ComWerftRemoveMigration.git
If you have issues, please use: https://github.com/commercewerft/ComWerftRemoveMigration/issues
