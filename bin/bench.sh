#!/bin/bash

TIMEFORMAT=%R

comment=$1
simplefiles=$(find ./res -iname "*simple*.primi")
complexfiles=$(find ./res -iname "*complex*.primi")
simpletime=0
complextime=0
iterations=100

function timeit {
    echo `{ time php primi.php $1 > /dev/null; } 2>&1`
}

for i in $(seq $iterations)
do
    for f in $simplefiles
    do
        tmp=$(timeit $f)
        simpletime=$(php -r "echo $simpletime + $tmp;");
    done
    for f in $complexfiles
    do
        tmp=$(timeit $f)
        complextime=$(php -r "echo $complextime + $tmp;");
    done
done

printf \
"Iterations: $iterations""\n"\
"Simple""\n"\
"- Total: $simpletime"" s \n"\
"- AVG  : "`php -r "echo $simpletime / $iterations;";`" s \n"\
"Complex""\n"\
"- Total: $complextime"" s \n"\
"- AVG  : "`php -r "echo $complextime / $iterations;";`" s \n"\

nowdate=`date +"%d.%m.%Y %H:%M"`
echo "$nowdate,$iterations,$simpletime,"`php -r "echo $simpletime / $iterations;"`",$complextime,"`php -r "echo $complextime / $iterations;"`",$comment" >> "bench_progress.csv"
