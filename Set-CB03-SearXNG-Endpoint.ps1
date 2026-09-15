param(
    [Parameter(Mandatory = $true)]
    [string]$WorkflowPath,

    [Parameter(Mandatory = $true)]
    [ValidatePattern('^https://')]
    [string]$Endpoint
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$workflow = Get-Content -Raw -LiteralPath $WorkflowPath | ConvertFrom-Json
$node = $workflow.nodes | Where-Object { $_.name -eq 'Validate CB-03 Handoff and Configure' }
if (-not $node) {
    throw 'The CB-03 configuration node was not found.'
}

$code = [string]$node.parameters.jsCode
$pattern = "env\('CB03_SEARXNG_URL',\s*'[^']+'\)"
$matches = [regex]::Matches($code, $pattern)
if ($matches.Count -ne 1) {
    throw "Expected one editable SearXNG endpoint, found $($matches.Count)."
}

$escapedEndpoint = $Endpoint.Replace("'", "\'")
$node.parameters.jsCode = [regex]::Replace(
    $code,
    $pattern,
    "env('CB03_SEARXNG_URL', '$escapedEndpoint')",
    1
)

$json = $workflow | ConvertTo-Json -Depth 100
$utf8 = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText($WorkflowPath, $json + [Environment]::NewLine, $utf8)

Write-Output "WORKFLOW=$WorkflowPath"
Write-Output "SEARXNG_ENDPOINT=$Endpoint"
