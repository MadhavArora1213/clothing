# ═══════════════════════════════════════════════════════════════
# watch-uploads.ps1 — Urban Outfit Collection
# Monitors uploads/ folder. When admin uploads a new image via
# the admin panel, this script auto git add + commit + pushes
# that file to GitHub immediately.
#
# HOW TO RUN (once, before starting admin work):
#   Right-click → "Run with PowerShell"
#   OR in terminal: powershell -ExecutionPolicy Bypass -File watch-uploads.ps1
# ═══════════════════════════════════════════════════════════════

$repoRoot    = "c:\xampp\htdocs\urban_outfit\clothing"
$uploadsPath = "$repoRoot\uploads"
$branch      = "main"
$logFile     = "$repoRoot\upload-push.log"

# ── Colours ──
function Write-OK   { param($m) Write-Host "[✓] $m" -ForegroundColor Green  }
function Write-INFO { param($m) Write-Host "[i] $m" -ForegroundColor Cyan   }
function Write-ERR  { param($m) Write-Host "[✗] $m" -ForegroundColor Red    }
function Write-WAIT { param($m) Write-Host "[~] $m" -ForegroundColor Yellow }

function Log { param($msg) $ts = Get-Date -Format "yyyy-MM-dd HH:mm:ss"; Add-Content $logFile "[$ts] $msg" }

# ── Ensure uploads folder exists ──
if (!(Test-Path $uploadsPath)) {
    New-Item -ItemType Directory -Path $uploadsPath -Force | Out-Null
}

Write-Host ""
Write-Host "  ╔══════════════════════════════════════════════╗" -ForegroundColor Magenta
Write-Host "  ║   Urban Outfit — Upload Watcher Started      ║" -ForegroundColor Magenta
Write-Host "  ║   Watching: $uploadsPath" -ForegroundColor Magenta
Write-Host "  ║   Push to : GitHub / $branch" -ForegroundColor Magenta
Write-Host "  ╚══════════════════════════════════════════════╝" -ForegroundColor Magenta
Write-Host ""
Write-INFO "Press Ctrl+C to stop watcher."
Write-Host ""

# ── FileSystemWatcher setup ──
$watcher                     = New-Object System.IO.FileSystemWatcher
$watcher.Path                = $uploadsPath
$watcher.Filter              = "*.*"
$watcher.IncludeSubdirectories = $true
$watcher.NotifyFilter        = [System.IO.NotifyFilters]::FileName -bor [System.IO.NotifyFilters]::LastWrite
$watcher.EnableRaisingEvents = $true

# ── Debounce: avoid double-firing on same file ──
$lastPushed = @{}
$debounceMs = 3000  # 3 seconds

# ── Push function ──
function Push-Upload {
    param([string]$fullPath)

    $ext = [System.IO.Path]::GetExtension($fullPath).ToLower()
    $imageExts = @('.jpg', '.jpeg', '.png', '.webp', '.gif', '.avif')

    # Only push image files
    if ($imageExts -notcontains $ext) { return }

    # Debounce check
    $now = [datetime]::Now
    if ($lastPushed.ContainsKey($fullPath)) {
        $diff = ($now - $lastPushed[$fullPath]).TotalMilliseconds
        if ($diff -lt $debounceMs) { return }
    }
    $lastPushed[$fullPath] = $now

    # Wait for file to finish writing (PHP move_uploaded_file can be async)
    Start-Sleep -Milliseconds 800

    if (!(Test-Path $fullPath)) { return }  # file might have been temp

    $relPath    = $fullPath.Replace("$repoRoot\", "").Replace("\", "/")
    $fileName   = [System.IO.Path]::GetFileName($fullPath)
    $commitMsg  = "upload: $fileName [$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')]"

    Write-Host ""
    Write-WAIT "New image detected: $relPath"
    Log "New image detected: $relPath"

    Set-Location $repoRoot

    # git add the specific file
    $addResult = & git add $relPath 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-ERR "git add failed: $addResult"
        Log "ERROR git add: $addResult"
        return
    }

    # Check if there's actually something staged
    $staged = & git diff --cached --name-only 2>&1
    if ([string]::IsNullOrWhiteSpace($staged)) {
        Write-INFO "No changes staged (file already in git). Skipping push."
        return
    }

    # git commit
    $commitResult = & git commit -m $commitMsg 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-ERR "git commit failed: $commitResult"
        Log "ERROR git commit: $commitResult"
        return
    }

    Write-OK "Committed: $commitMsg"
    Log "Committed: $commitMsg"

    # git push with retry (up to 3 attempts)
    $pushed = $false
    for ($i = 1; $i -le 3; $i++) {
        Write-WAIT "Pushing to GitHub (attempt $i/3)..."
        $pushResult = & git push origin $branch 2>&1
        if ($LASTEXITCODE -eq 0) {
            $pushed = $true
            break
        }
        Write-ERR "Push attempt $i failed: $pushResult"
        Start-Sleep -Seconds 2
    }

    if ($pushed) {
        Write-OK "Pushed to GitHub: $fileName"
        Log "SUCCESS pushed: $fileName"
        Write-Host ""
        Write-Host "  → https://github.com/MadhavArora1213/clothing/blob/main/$relPath" -ForegroundColor DarkCyan
    } else {
        Write-ERR "All push attempts failed for: $fileName"
        Log "FAILED all push attempts: $fileName"
    }

    Write-Host ""
    Write-WAIT "Watching for next upload..."
}

# ── Register event handlers ──
$createdAction = {
    param($source, $e)
    Push-Upload -fullPath $e.FullPath
}

$renamedAction = {
    param($source, $e)
    # Covers cases where PHP writes to a temp file then renames
    Push-Upload -fullPath $e.FullPath
}

Register-ObjectEvent $watcher "Created" -Action $createdAction | Out-Null
Register-ObjectEvent $watcher "Renamed" -Action $renamedAction | Out-Null

Write-WAIT "Watching $uploadsPath ..."
Write-Host ""

# ── Keep script alive ──
try {
    while ($true) {
        Start-Sleep -Seconds 1
    }
} finally {
    $watcher.EnableRaisingEvents = $false
    $watcher.Dispose()
    Write-Host ""
    Write-INFO "Watcher stopped."
}
