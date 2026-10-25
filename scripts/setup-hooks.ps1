<#
.SYNOPSIS
    Configures git to use .githooks for automated pre-commit verification.
#>
$repoRoot = Resolve-Path "$PSScriptRoot\.."
Set-Location $repoRoot

git config core.hooksPath .githooks
Write-Host "✅ Git hooks configured! Pre-commit verification is now active (.githooks)." -ForegroundColor Green
