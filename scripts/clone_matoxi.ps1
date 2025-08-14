$ErrorActionPreference = 'Stop'
try { [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12 } catch {}

$URL = 'https://codervent.com/matoxi/demo/vertical-menu/index-semi-dark.html'
$OutRoot = Join-Path $PSScriptRoot '..' | Resolve-Path | ForEach-Object { Join-Path $_ 'admin/matoxi' }
if (-not (Test-Path $OutRoot)) { New-Item -ItemType Directory -Path $OutRoot -Force | Out-Null }

function Save-Url([string]$AbsUrl) {
  try {
    $u = [System.Uri]$AbsUrl
    $rel = ($u.AbsoluteUri -replace '^https?://[^/]+/','')
    $dest = Join-Path $OutRoot $rel
    $destDir = Split-Path $dest -Parent
    if (-not (Test-Path $destDir)) { New-Item -ItemType Directory -Path $destDir -Force | Out-Null }
    Invoke-WebRequest -UseBasicParsing -Uri $u.AbsoluteUri -OutFile $dest
    return $dest
  } catch {
    Write-Host "Skip $AbsUrl : $_" -ForegroundColor Yellow
    return $null
  }
}

# 1) Download HTML
$htmlPath = Join-Path $OutRoot 'index-semi-dark.html'
$html = (Invoke-WebRequest -UseBasicParsing -Uri $URL).Content
[System.IO.File]::WriteAllText($htmlPath, $html)

# 2) Extract resources from HTML (css/js/images/fonts)
$rx = [regex]'(?is)(?:href|src)=["\']([^"\']+\.(?:css|js|png|jpg|jpeg|gif|svg|webp|woff2?|ttf|eot))(?:\?[^"\']*)?["\']'
$m = $rx.Matches($html)
$seen = @{}
$downloaded = @()
foreach ($mm in $m) {
  $path = $mm.Groups[1].Value
  if ($seen.ContainsKey($path)) { continue }
  $seen[$path] = $true
  if ($path -match '^https?://') { $abs = $path }
  else {
    $base = [System.Uri]$URL
    $abs = ([System.Uri]::new($base, $path)).AbsoluteUri
  }
  $saved = Save-Url $abs
  if ($saved) { $downloaded += ,@($abs,$saved) }
}

# 3) Parse downloaded CSS files for url(...) dependencies and download them
$cssDeps = @()
foreach ($row in $downloaded) {
  $absUrl = $row[0]; $savedPath = $row[1]
  if ($savedPath -and $savedPath.ToLower().EndsWith('.css')) {
    $css = Get-Content $savedPath -Raw
    $rxCss = [regex]'url\(([^)]+)\)'
    $ms = $rxCss.Matches($css)
    foreach ($m2 in $ms) {
      $ref = $m2.Groups[1].Value.Trim("'\" ")
      if ($ref.StartsWith('data:')) { continue }
      if (-not ($ref -match '\.(png|jpg|jpeg|gif|svg|webp|woff2?|ttf|eot)(\?.*)?$')) { continue }
      # Resolve relative to CSS file URL
      $base = [System.Uri]$absUrl
      $abs2 = if ($ref -match '^https?://') { $ref } else { ([System.Uri]::new($base,$ref)).AbsoluteUri }
      if (-not $seen.ContainsKey($abs2)) {
        $seen[$abs2] = $true
        $saved2 = Save-Url $abs2
        if ($saved2) { $cssDeps += ,@($abs2,$saved2) }
      }
    }
  }
}

Write-Host ("Downloaded HTML + {0} assets (+{1} css deps) into {2}" -f $downloaded.Count, $cssDeps.Count, $OutRoot)

