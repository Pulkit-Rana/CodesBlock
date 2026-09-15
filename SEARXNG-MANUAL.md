# SearXNG manual controls

SearXNG is installed at `/home/pulkit/projects/searxng` and normally runs as
an enabled Ubuntu background service at `http://127.0.0.1:8888`.

## Normal background operation

```bash
sudo systemctl start searxng.service
sudo systemctl stop searxng.service
sudo systemctl restart searxng.service
sudo systemctl status searxng.service
```

View recent logs:

```bash
sudo journalctl -u searxng.service -n 100 --no-pager
```

## Run directly in the Ubuntu terminal

The background service and the terminal process cannot both use port 8888.

```bash
sudo systemctl stop searxng.service
/home/pulkit/projects/searxng/run-manually.sh
```

Press `Ctrl+C` to stop the terminal process. Restore normal background mode:

```bash
sudo systemctl start searxng.service
```

## Cloudflare Quick Tunnel for n8n Cloud

First make sure SearXNG is running. In Windows, launch
`Start-SearXNG-Tunnel.cmd` and keep that window open. A Quick Tunnel creates a
new `trycloudflare.com` URL whenever it is restarted, so update the n8n endpoint
after the URL changes.

## Update SearXNG

```bash
/home/pulkit/projects/searxng/update-searxng.sh
```

Some upstream engines may temporarily show as unavailable because of CAPTCHA,
rate limiting, geography, or network policy. SearXNG will continue with results
from the engines that answered.
