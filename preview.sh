#!/bin/sh
cd $(dirname "$0")
rm -rf public
./hugo -v -w server
