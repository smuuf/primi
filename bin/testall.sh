#!/bin/bash

files=`find res/ -iname "*.primi"`

for i in $files
do
    echo -e "█ $i"
    # Print only errors
    php primi.php $i
done
