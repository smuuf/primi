#!/bin/bash

cd $(dirname $0)

if [[ "$1" == "--coverage" ]]; then
        # "-p phpdbg"
        COV="-d memory_limit=-1 --coverage ../coverage.xml --coverage-src ../src/"
fi

./../vendor/nette/tester/src/tester $COV -C ../tests
