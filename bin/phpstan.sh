#!/bin/bash -e

cd $(dirname $0)

../vendor/bin/phpstan analyze --level=5 ../src -c ../phpstan.neon $@

