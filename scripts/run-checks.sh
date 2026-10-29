#!/usr/bin/env bash
# CodesBlock Local CI & Verification Runner (Bash / Linux / macOS)
# Replicates the GitHub Actions CI pipeline locally.
# Runs the four local theme hygiene, syntax, security, and unit-test checks.

set -e

echo "===================================================="
echo "   CodesBlock Local CI & Verification Runner"
echo "===================================================="

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

FAILED=0

# 1. Essential Hygiene Check
echo -e "\n[1/4] Checking Repository Hygiene..."
CONFLICT_FILES=$(git grep -I -n -E '^(<{7}\s|={7}$|>{7}\s)' -- '*.php' 'template-parts/*' 'assets/*' 'tests/*' '.github/*' '*.js' '*.css' '*.json' '*.yml' '*.md' || true)
if [ -n "$CONFLICT_FILES" ]; then
  echo "[-] Merge conflict markers detected:"
  echo "$CONFLICT_FILES"
  FAILED=1
else
  echo "[+] Zero merge conflict markers found."
fi

FORBIDDEN_TRACKED=$(git ls-files | grep -v -E '(secondary-packages|vendor)' | grep -E '(wp-config\.php$|\.sql$|\.key$|id_rsa$|\.env$|\.env\..*)$' || true)
if [ -n "$FORBIDDEN_TRACKED" ]; then
  echo "[-] Forbidden or private file patterns tracked in git:"
  echo "$FORBIDDEN_TRACKED"
  FAILED=1
else
  echo "[+] Zero forbidden or sensitive files tracked."
fi

# 2. PHP Syntax Check
echo -e "\n[2/4] Linting Theme PHP Syntax (php -l)..."
PHP_ERRORS=0
for file in *.php template-parts/*.php tests/*.php tests/**/*.php; do
  if [ -f "$file" ]; then
    php -l "$file" > /dev/null 2>&1 || {
      echo "[-] Syntax error in $file"
      PHP_ERRORS=$((PHP_ERRORS + 1))
    }
  fi
done

if [ "$PHP_ERRORS" -eq 0 ]; then
  echo "[+] All theme and test PHP files passed syntax check."
else
  echo "[-] $PHP_ERRORS PHP syntax errors detected."
  FAILED=1
fi

# 3. Security SAST Scan
echo -e "\n[3/4] Running Security Anti-Pattern Checks..."
php scripts/sast-scan.php || FAILED=1

if [ "$FAILED" -eq 0 ]; then
  echo "[+] Security SAST scan clean: No OWASP anti-patterns found in theme templates."
fi

# 4. Unit Tests Runner
echo -e "\n[4/4] Executing CodesBlock Test Suite..."
php tests/run-tests.php || FAILED=1

echo -e "\n===================================================="
if [ "$FAILED" -ne 0 ]; then
  echo "   CHECK RESULT: FAILED - Please resolve the issues above."
  exit 1
else
  echo "   CHECK RESULT: ALL CHECKS PASSED CLEANLY!"
fi
echo "===================================================="
