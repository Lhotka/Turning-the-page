#!/bin/bash

DATABASE="final"

# Get a list of tables in the database
TABLES=$(mysql -N -e "USE $DATABASE; SHOW TABLES;")

# Loop through each table and run REPAIR TABLE
for TABLE in $TABLES; do
    mysql -e "USE $DATABASE; REPAIR TABLE $TABLE;"
done


# to run use: chmod +x repair_tables.sh