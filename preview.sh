#!/bin/sh
cd $(dirname "$0")
rm -rf public
./hugo --logLevel info -w server
