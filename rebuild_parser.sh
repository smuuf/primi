#!/bin/bash

cd $(dirname $0)

php ./vendor/hafriedlander/php-peg/cli.php ./src/parser/Grammar.peg ./src/parser/CompiledParser.php
rm -f ./temp/*.json
