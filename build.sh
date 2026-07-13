#!/usr/bin/env bash
# Exit on error
set -o errexit

# Create writable database file
touch fitcheck.db
chmod 666 fitcheck.db