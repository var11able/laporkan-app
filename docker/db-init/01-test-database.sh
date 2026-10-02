#!/bin/sh
mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS \`${MARIADB_DATABASE}_test\` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
