# Enables you to remove the shopware migration plugin including magento migration 

When you have done a Migration from Magento 1 or Magento 2 with customers, you are not allowed to disable these modules, as they are needed for the login of the customers created within Magento.

After the customers login, Shopware replaces the encoder for the password and uses its internal encoder. So you have to wait untill all customers have logged in.

If you disable the migration plugins, you will get an error message, when a migrated customer logs in first.

This small plugin uses only the password-encoders. So you are able to uninstall SwagMigrationAssistant and SwagMigrationMagento.

The code will be maintained on github. You can find it here: https://github.com/commercewerft/ComWerftRemoveMigration.git
If you have issues, please use this site: https://github.com/commercewerft/ComWerftRemoveMigration/issues
