#!/usr/bin/env bash
set -euo pipefail

SERVICE="searxng.service"
PROJECT="/home/pulkit/projects/searxng"

if systemctl is-active --quiet "$SERVICE"; then
  echo "SearXNG is already running as a background service."
  echo "To run it in this terminal instead, first use:"
  echo "  sudo systemctl stop $SERVICE"
  exit 1
fi

export SEARXNG_SETTINGS_PATH="$PROJECT/config/settings.yml"
cd "$PROJECT/source"

echo "Starting SearXNG at http://127.0.0.1:8888"
echo "Press Ctrl+C to stop it."
exec "$PROJECT/.venv/bin/python" -m searx.webapp
