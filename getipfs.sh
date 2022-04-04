#!/bin/bash

#curl --output /var/www/html/nftfiles/.$fullfile -X POST \"http://127.0.0.1:5001/api/v0/cat?arg=$hash&offset=0&length=10000000000000\""
#file=$1
#hash=$2
if [ "$#" -eq 2 ]; then

curl --output /var/www/html/nftfiles/.$1.tmp -X POST "http://127.0.0.1:5001/api/v0/cat?arg=$2&offset=0&length=10000000000000"
fi


