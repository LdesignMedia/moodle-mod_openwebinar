#!/usr/bin/env sh
SRC_DIR="`pwd`"
cd "`dirname "$0"`"
cd "../ua-parser/uap-php/bin"
BIN_TARGET="`pwd`/uaparser.php"
cd "$SRC_DIR"
"$BIN_TARGET" "$@"
