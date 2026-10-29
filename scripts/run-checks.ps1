<#
.SYNOPSIS
    Runs all local checks, security audits, and unit tests for CodesBlock.
.DESCRIPTION
    Replicates the GitHub Actions CI pipeline locally.
    Runs the four local theme hygiene, syntax, security, and unit-test checks.
#>

$ErrorActionPreference = "Stop"

Write-Host "====================================================" -ForegroundColor Cyan
Write-Host "   CodesBlock Local CI & Verification Runner" -ForegroundColor Cyan
Write-Host "====================================================" -ForegroundColor Cyan

# Locate PHP
$phpCmd = "php"
if (-not (Get-Command "php" -ErrorAction SilentlyContinue)) {
    $localPhp = Get-ChildItem -Path "$env:APPDATA\Local\lightning-services\php-*\bin\win64\php.exe" -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($localPhp) {
        $phpCmd = $localPhp.FullName
        Write-Host "Using LocalWP PHP: $phpCmd" -ForegroundColor DarkGray
    } else {
        Write-Error "PHP executable not found on PATH or LocalWP directory."
        exit 1
    }
}

$repoRoot = Resolve-Path "$PSScriptRoot\.."
Set-Location $repoRoot

$failed = $false

# 1. Essential Hygiene Check
Write-Host "`n[1/4] Checking Repository Hygiene..." -ForegroundColor Yellow
$conflictMarkers = git grep -I -n -E '^(<{7}\s|={7}$|>{7}\s)' -- '*.php' 'template-parts/*' 'assets/*' 'tests/*' '.github/*' '*.js' '*.css' '*.json' '*.yml' '*.md' 2>$null
if ($conflictMarkers) {
    Write-Host "[-] Merge conflict markers detected:" -ForegroundColor Red
    Write-Host $conflictMarkers -ForegroundColor Red
    $failed = $true
} else {
    Write-Host "[+] Zero merge conflict markers found." -ForegroundColor Green
}

$forbiddenFiles = git ls-files | Where-Object { $_ -notmatch 'secondary-packages|vendor' } | Select-String -Pattern '(wp-config\.php$|\.sql$|\.key$|id_rsa$|\.env$|\.env\..*)$'
if ($forbiddenFiles) {
    Write-Host "[-] Forbidden / secret files tracked in git:" -ForegroundColor Red
    Write-Host ($forbiddenFiles -join "`n") -ForegroundColor Red
    $failed = $true
} else {
    Write-Host "[+] Zero forbidden or sensitive files tracked." -ForegroundColor Green
}

# 2. PHP Syntax Check
Write-Host "`n[2/4] Linting Theme PHP Syntax (php -l)..." -ForegroundColor Yellow
$themePhpFiles = @(
    Get-ChildItem -Path . -Filter "*.php" -File
    Get-ChildItem -Path "template-parts" -Filter "*.php" -File -ErrorAction SilentlyContinue
    Get-ChildItem -Path "tests" -Filter "*.php" -Recurse -File -ErrorAction SilentlyContinue
)

$syntaxErrors = 0
foreach ($file in $themePhpFiles) {
    $output = & $phpCmd -l $file.FullName 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host "[-] Syntax error in $($file.FullName): $output" -ForegroundColor Red
        $syntaxErrors++
    }
}

if ($syntaxErrors -eq 0) {
    Write-Host "[+] All $($themePhpFiles.Count) theme and test PHP files passed syntax check." -ForegroundColor Green
} else {
    Write-Host "[-] $syntaxErrors PHP syntax errors detected." -ForegroundColor Red
    $failed = $true
}

# 3. Security SAST Scan
Write-Host "`n[3/4] Running Security Anti-Pattern Checks..." -ForegroundColor Yellow
& $phpCmd scripts/sast-scan.php
if ($LASTEXITCODE -eq 0) {
    Write-Host "[+] Security SAST scan clean: No OWASP anti-patterns found in theme templates." -ForegroundColor Green
} else {
    Write-Host "[-] Security SAST scan found anti-patterns." -ForegroundColor Red
    $failed = $true
}

# 4. Unit Tests Runner
Write-Host "`n[4/4] Executing CodesBlock Test Suite..." -ForegroundColor Yellow
& $phpCmd tests/run-tests.php
if ($LASTEXITCODE -ne 0) {
    $failed = $true
}

Write-Host "`n====================================================" -ForegroundColor Cyan
if ($failed) {
    Write-Host "   CHECK RESULT: FAILED - Please resolve the issues above." -ForegroundColor Red
    exit 1
} else {
    Write-Host "   CHECK RESULT: ALL CHECKS PASSED CLEANLY!" -ForegroundColor Green
}
Write-Host "====================================================" -ForegroundColor Cyan
