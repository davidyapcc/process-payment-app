#!/bin/sh
set -e

# Check if the application is ready for use
if [ ! -f .env ]; then
    cp .env.dist .env
fi

exec "$@"
