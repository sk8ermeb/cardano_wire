#!/bin/bash

#   --socket-path /home/user/relay/node.socket \
cardano-node run \
   --topology /var/ada/mainnet-topology.json \
   --database-path /var/ada/relay/db \
   --socket-path /var/ada/node.socket \
   --host-addr 0.0.0.0 \
   --port 3001 \
   --config /var/ada/mainnet-config.json \
#   > /home/user/nodelog.txt 2>&1 &
