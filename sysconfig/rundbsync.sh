#!/bin/bash

#   --socket-path /home/user/relay/node.socket \
PGPASSFILE=/home/user/cardano-db-sync/config/pgpass-mainnet \
  /home/user/cardano-db-sync/db-sync-node/bin/cardano-db-sync \
      --config /home/user/cardano-db-sync/config/mainnet-config.yaml \
      --socket-path /var/ada/node.socket \
      --state-dir /home/user/cardano-db-sync/ledger-state/mainnet \
      --schema-dir /home/user/cardano-db-sync/schema/ \
#      > /home/user/db-sync-log.txt 2>&1 &
#cardano-node run \
#   --topology /var/ada/mainnet-topology.json \
#   --database-path /var/ada/relay/db \
#   --socket-path /var/ada/node.socket \
#   --host-addr 0.0.0.0 \
#   --port 3001 \
#   --config /var/ada/mainnet-config.json \
#   > /home/user/nodelog.txt 2>&1 &
