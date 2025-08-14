$ErrorActionPreference = 'Stop'

try {
    # Ensure TLS 1.2 for downloads
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
} catch {}

# Go to project root (one level up from scripts dir)
$projectRoot = Resolve-Path (Join-Path $PSScriptRoot '..')
Set-Location $projectRoot

$cdn = 'https://d1cj8q6w07zyiq.cloudfront.net/'
$assetsRoot = Join-Path $pwd 'assets'
$indexPath = Join-Path $pwd 'index.php'

if (!(Test-Path $indexPath)) {
    throw "index.php not found at $indexPath"
}

$content = Get-Content $indexPath -Raw
$pattern = 'https://d1cj8q6w07zyiq\.cloudfront\.net/[^\s""''<>]+'
$regex = [regex]$pattern
$urls = $regex.Matches($content) | ForEach-Object { $_.Value } | Sort-Object -Unique

Write-Host ("Found {0} asset URLs" -f $urls.Count)

function Save-Asset([string]$url) {
    try {
        $uri = [System.Uri]$url
        $relative = $uri.AbsolutePath.TrimStart('/')
        $dest = Join-Path $assetsRoot $relative
        $destDir = Split-Path $dest -Parent
        if (!(Test-Path $destDir)) { New-Item -ItemType Directory -Path $destDir -Force | Out-Null }
        $srcNoQuery = $uri.GetLeftPart([System.UriPartial]::Path)
        Invoke-WebRequest -Uri $srcNoQuery -OutFile $dest -UseBasicParsing
        Write-Host ("Saved: {0}" -f $relative)
    } catch {
        Write-Warning ("Failed: {0} - {1}" -f $url, $_.Exception.Message)
    }
}

if (!(Test-Path $assetsRoot)) { New-Item -ItemType Directory -Path $assetsRoot | Out-Null }

$urls | ForEach-Object { Save-Asset $_ }

# Backup current index.php
Copy-Item $indexPath (Join-Path $pwd 'index.remote.backup.php') -Force

# Replace CDN root with /assets/
($content -replace [regex]::Escape($cdn), '/assets/') | Set-Content $indexPath -Encoding UTF8

Write-Host 'Done'


