#!/bin/bash -e

cd $(dirname $0)

../vendor/bin/phpstan analyze -c ../phpstan.neon $@

