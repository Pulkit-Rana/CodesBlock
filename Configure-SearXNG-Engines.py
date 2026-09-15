#!/usr/bin/env python3
"""Apply CodesBlock's broad, no-credential SearXNG engine profile."""

from pathlib import Path
import shutil
import sys
from datetime import datetime

import yaml


SETTINGS = Path("/home/pulkit/projects/searxng/config/settings.yml")
DEFAULT_SETTINGS = Path("/home/pulkit/projects/searxng/source/searx/settings.yml")

# General web/search sources that do not require a private API key.
ENGINE_NAMES = [
    "google",
    "google cse",
    "bing",
    "brave",
    "duckduckgo",
    "duckduckgo web",
    "qwant",
    "startpage",
    "yahoo",
    "yep",
    "mojeek",
    "mwmbl",
    "marginalia",
    "wiby",
    "yandex",
    "wikipedia",
    "github",
    "github code",
]


def main() -> int:
    with DEFAULT_SETTINGS.open("r", encoding="utf-8") as handle:
        defaults = yaml.safe_load(handle)

    available = {
        engine.get("name")
        for engine in defaults.get("engines", [])
        if isinstance(engine, dict)
    }
    missing = [name for name in ENGINE_NAMES if name not in available]
    if missing:
        print("SearXNG no longer recognizes: " + ", ".join(missing), file=sys.stderr)
        return 1

    with SETTINGS.open("r", encoding="utf-8") as handle:
        settings = yaml.safe_load(handle) or {}

    stamp = datetime.now().strftime("%Y%m%d-%H%M%S")
    backup = SETTINGS.with_name(f"settings.yml.before-broad-engines-{stamp}")
    shutil.copy2(SETTINGS, backup)

    settings["engines"] = [
        {"name": name, "disabled": False} for name in ENGINE_NAMES
    ]
    settings.setdefault("outgoing", {})["request_timeout"] = 10.0
    settings["outgoing"]["max_request_timeout"] = 20.0

    with SETTINGS.open("w", encoding="utf-8") as handle:
        yaml.safe_dump(settings, handle, sort_keys=False, allow_unicode=True)

    print(f"Enabled {len(ENGINE_NAMES)} broad, no-credential engines.")
    print(f"Backup: {backup}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
