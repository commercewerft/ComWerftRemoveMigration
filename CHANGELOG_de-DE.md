# 0.2.0

* Kompatibilität mit Shopware 6.5, 6.6 und 6.7 (Shopware 6.4 bitte weiterhin mit Version 0.1.0 nutzen)
* Behoben: Ein Argon2id13-Hash mit ungültiger Salt-Länge löste beim Login eine SodiumException und damit einen Serverfehler aus, statt die Anmeldung abzulehnen
* Behoben: Magento-2-Hashes ohne Versions-Segment erzeugten beim Login PHP-Warnungen im Fehlerprotokoll
* Passwörter werden als `#[\SensitiveParameter]` markiert und tauchen damit nicht mehr in Stacktraces auf
* Unit-Tests für alle vier Passwort-Encoder ergänzt

# 0.1.0

* Erstveröffentlichung
