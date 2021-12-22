#!/bin/bash -e

cd $(dirname $0)

./tests-smoke.sh $@
./tests-unit.sh $@
