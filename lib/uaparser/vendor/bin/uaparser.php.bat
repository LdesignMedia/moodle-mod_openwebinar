@ECHO OFF
SET BIN_TARGET=%~dp0/../ua-parser/uap-php/bin/uaparser.php
php "%BIN_TARGET%" %*
