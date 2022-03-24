#!/bin/bash

source $(dirname $0)/_helpers.sh
cd $(dirname $0)/..

header "Primi performance benchmarks"

INTERPRETER=$([[ ! -z "$1" ]] && echo "$1" || echo "php")

# Time format for 'time' command.
TIMEFORMAT=%R

info "Using interpreter: "$(which $INTERPRETER)
$INTERPRETER --version

info "Clearing AST cache ..."
rm ./temp/ast*.json 2>/dev/null
rm bench.out 2>/dev/null

ITERS=3
PERF_STANDARD_PATH='./tests/bench/perf_bench_php.php'
PERF_BENCH_FILE=$(find ./tests/bench/perf_bench_primi.primi)
OTHER_BENCH_FILES=$(find ./tests/bench/ -iname "bench_*.primi")
SIMPLE_TIME=0
TOTAL_TIME=0
AVG_SCORE=0

function get_precise_time {
	date +%s.%N
}

function measure_time {
	# We probably should measure only user+kernel time our process really took
	# (without I/O waits and stuff), but /usr/bin/time, which is handy, returns
	# insufficient precision (only 2 decimal points).
	# So lets measure everything.
	START=`get_precise_time`
	$1 1>&2
	END=`get_precise_time`
	perl -e "printf('%.8f', $END - $START);"
}

function timeit_php {
	echo `measure_time "$INTERPRETER $1"`
}

function timeit_primi {
	echo `measure_time "$INTERPRETER ./primi $1"`
}

header "Measuring perf standard (PHP code) ... "

echo -en "${CLR_YELLOW}${PERF_STANDARD_PATH}${CLR_RESET} ... "
PERF_STD_TIME=`timeit_php $PERF_STANDARD_PATH`
echo -e "\n█ PHP perf took $PERF_STD_TIME s"

header "Running perf benchmarks (Primi code) ..."
for i in $(seq $ITERS)
do
	[[ "$i" == "1" ]] && STATE='parsing' || STATE='cached'

	[[ "$STATE" == "parsing" ]] && DESC="With AST parsing" || DESC="With cached AST"
	info "Perf benchmark $DESC ($i / $ITERS)"

	echo -en "${CLR_YELLOW}${PERF_BENCH_FILE}${CLR_RESET} ... "
	SIMPLE_TIME=$(timeit_primi $PERF_BENCH_FILE)
	SCORE=$(perl -e "printf('%.2f', $SIMPLE_TIME / $PERF_STD_TIME)")
	TOTAL_TIME=$(perl -e "printf('%.2f', $SIMPLE_TIME + $TOTAL_TIME)")
	echo -e "\n█ Primi perf took $SIMPLE_TIME s (${SCORE}x slower)";

done

AVG_TIME=`perl -e "printf('%.2f', $TOTAL_TIME / $ITERS);"`
AVG_SCORE=`perl -e "printf('%.2f', $AVG_TIME / $PERF_STD_TIME);"`

header "Perf benchmark result"
printf \
"  - Total: $TOTAL_TIME s\n"\
"  - Avg  : $AVG_TIME s\n"

TODAY=`date +"%d.%m.%Y %H:%M"`
echo "$TODAY, ${AVG_TIME//,/.}, perf ${AVG_SCORE//,/.}x slower vs ${PERF_STD_TIME//,/.}" >> "bench_progress.csv"

exit

header "Running other benchmarks (Primi code) ..."
for i in $(seq $ITERS)
do
	[[ "$i" == "1" ]] && STATE='parsing' || STATE='cached'

	[[ "$STATE" == "parsing" ]] && DESC="With AST parsing" || DESC="With cached AST"
	info "Other benchmarks $DESC ($i / $ITERS)"

	for F in $OTHER_BENCH_FILES
	do
		echo -en "${CLR_YELLOW}${F}${CLR_RESET} ... "
		SIMPLE_TIME=$(timeit_primi $F)
		echo -e "\n█ Took $SIMPLE_TIME s";
	done
done
