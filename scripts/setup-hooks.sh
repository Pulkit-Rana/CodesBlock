#!/usr/bin/env bash
# Configure git to use .githooks for automated pre-commit verification

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

chmod +x .githooks/pre-commit 2>/dev/null || true
git config core.hooksPath .githooks
echo "✅ Git hooks configured! Pre-commit verification is now active (.githooks)."
