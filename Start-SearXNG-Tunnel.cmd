@echo off
title CodesBlock SearXNG Cloudflare Tunnel
echo Starting the temporary Cloudflare tunnel for SearXNG...
echo Keep this window open while n8n uses the tunnel.
echo.
"C:\Program Files (x86)\cloudflared\cloudflared.exe" tunnel --url http://127.0.0.1:8888 --no-autoupdate --loglevel info --logfile "C:\Users\pulki\Local Sites\codesblock\cloudflared-searxng-visible.log"
echo.
echo The SearXNG tunnel has stopped. Restart this file to create a new temporary URL.
pause
