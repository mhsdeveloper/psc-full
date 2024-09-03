#!/bin/bash

echo "This will setup the initial database for the historical names manager and the document manager."

read -p "Pick a name for your mysql user: " user

read -

mysql -uroot -e "create user $user@localhost"

mysql -uroot -e "alter $user@localhost identified by $password"