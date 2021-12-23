#!/bin/bash -e

cd $(dirname $0)

../vendor/bin/phpstan analyze --level=6 ../src -c ../phpstan.neon $@

