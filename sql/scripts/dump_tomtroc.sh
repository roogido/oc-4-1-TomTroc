#!/usr/bin/env bash

# ----- CONFIGURATION -----
MYSQLDUMP="/c/xampp/mysql/bin/mysqldump.exe"
OUT_DIR="/c/xampp/htdocs/oc/p4l1/sql/dumps"
DB_NAME="tomtroc"
DB_USER="root"
DB_PASS=""   # mettre -pMotDePasse si besoin

# ----- Générer timestamp : yyyymmdd_hhmm -----
TS=$(date +"%Y%m%d_%H%M")

# ----- Nom du fichier -----
FILE="$OUT_DIR/tomtroc.$TS.sql"

# ----- Dump -----
echo "Dump de la base $DB_NAME..."
$MYSQLDUMP -u "$DB_USER" $DB_PASS "$DB_NAME" > "$FILE"

echo "Dump terminé : $FILE"
