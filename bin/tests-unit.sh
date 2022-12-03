#!/bin/bash -e

source $(dirname $0)/_helpers.sh
cd $(dirname $0)

# Select phpdbg if available, or php.
INTERPRETER=$(command -v phpdbg php | head -n1)

POSITIONAL=()
while [[ $# -gt 0 ]]; do
    key="$1"

    case $key in
        --php)
        INTERPRETER="$2"
        shift # past argument
        shift # past value
        ;;
        --coverage)
        COVERAGE=1
        COV_FORMAT="$2" || "vole"
        shift # past argument
        shift # past value
        ;;
        *)    # unknown option
        POSITIONAL+=("$1") # save it in an array for later
        shift # past argument
        ;;
    esac
done
set -- "${POSITIONAL[@]}" # restore positional parameters

# Default coverage format is "html".
COV_FORMAT=$([[ ! -z "$COV_FORMAT" ]] && echo "$COV_FORMAT" || echo "html")

if [[ ! -z "$COVERAGE" ]]; then
        COV="-d memory_limit=-1 --coverage ../coverage.$COV_FORMAT --coverage-src ../src/"
fi

header "Primi: Unit tests"
info "Using interpreter: "$(which $INTERPRETER)

./../vendor/nette/tester/src/tester -p $INTERPRETER $COV -C ../tests $@ # --coverage-exclude 'Compiled/PrimiParser.php'
