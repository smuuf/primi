#!/bin/bash -e

source $(dirname $0)/_helpers.sh
cd $(dirname $0)

# Default interpreter is "php" (whatever version is primary).
INTERPRETER='php'

POSITIONAL=()
while [[ $# -gt 0 ]]; do
    key="$1"

    case $key in
        --php)
        INTERPRETER="$2"
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

# If the interpreter is phpdbg, we need to add "-qrr" arguments to actually
# run the files.
# https://www.php.net/manual/en/intro.phpdbg.php
if [[ "$INTERPRETER" =~ 'phpdbg' ]]; then
	INTERPRETER="$INTERPRETER -qrrb"
fi

header "Primi: Smoke tests"
info "Using interpreter: "$(which $INTERPRETER)

$INTERPRETER ../tests/smoke/examples.phpt
$INTERPRETER ../tests/smoke/uglies.phpt
