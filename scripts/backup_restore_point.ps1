param(
    [string]$OutputDir = "backups",
    [string]$Label = "restorepoint"
)

$ErrorActionPreference = 'Stop'

function Get-EnvValue {
    param(
        [hashtable]$EnvMap,
        [string]$Key,
        [string]$Default = ""
    )

    if ($EnvMap.ContainsKey($Key) -and $EnvMap[$Key] -ne $null -and $EnvMap[$Key] -ne "") {
        return $EnvMap[$Key]
    }

    return $Default
}

function Read-DotEnv {
    param([string]$DotEnvPath)

    if (!(Test-Path $DotEnvPath)) {
        throw "Missing .env at: $DotEnvPath"
    }

    $map = @{}
    Get-Content $DotEnvPath | ForEach-Object {
        $line = $_.Trim()
        if ($line -eq "" -or $line.StartsWith('#')) { return }
        $idx = $line.IndexOf('=')
        if ($idx -lt 1) { return }
        $k = $line.Substring(0, $idx).Trim()
        $v = $line.Substring($idx + 1).Trim()

        # Strip surrounding quotes
        if (($v.StartsWith('"') -and $v.EndsWith('"')) -or ($v.StartsWith("'") -and $v.EndsWith("'"))) {
            $v = $v.Substring(1, $v.Length - 2)
        }

        $map[$k] = $v
    }

    return $map
}

$projectRoot = (Get-Location).Path
if (!(Test-Path (Join-Path $projectRoot 'artisan'))) {
    throw "Run this script from the Laravel project root (folder containing artisan). Current: $projectRoot"
}

$dotenv = Read-DotEnv (Join-Path $projectRoot '.env')
$dbHost = Get-EnvValue $dotenv 'DB_HOST' '127.0.0.1'
$dbPort = Get-EnvValue $dotenv 'DB_PORT' '3306'
$dbName = Get-EnvValue $dotenv 'DB_DATABASE' ''
$dbUser = Get-EnvValue $dotenv 'DB_USERNAME' 'root'
$dbPass = Get-EnvValue $dotenv 'DB_PASSWORD' ''

if ([string]::IsNullOrWhiteSpace($dbName)) {
    throw "DB_DATABASE is empty in .env"
}

$stamp = Get-Date -Format "yyyyMMdd-HHmmss"
$backupRoot = Join-Path $projectRoot $OutputDir
$backupDir = Join-Path $backupRoot ("{0}-{1}-{2}" -f $Label, $stamp, $dbName)
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

$mysqldump = 'C:\xampp\mysql\bin\mysqldump.exe'
if (!(Test-Path $mysqldump)) {
    $found = (Get-Command mysqldump -ErrorAction SilentlyContinue | Select-Object -ExpandProperty Source -First 1)
    if (![string]::IsNullOrWhiteSpace($found)) {
        $mysqldump = $found
    } else {
        throw "mysqldump not found. Expected C:\xampp\mysql\bin\mysqldump.exe or on PATH."
    }
}

$zipPath = Join-Path $backupDir ("project-{0}.zip" -f $stamp)
$sqlPath = Join-Path $backupDir ("database-{0}.sql" -f $stamp)
$envPath = Join-Path $backupDir (".env.backup")
$metaPath = Join-Path $backupDir ("BACKUP_INFO.txt")

# Metadata (avoid printing secrets)
$gitHash = ''
try { $gitHash = (git rev-parse HEAD 2>$null).Trim() } catch { $gitHash = '' }
$gitBranch = ''
try { $gitBranch = (git rev-parse --abbrev-ref HEAD 2>$null).Trim() } catch { $gitBranch = '' }

@(
    "Created: $stamp",
    "Project: $projectRoot",
    "DB: $dbName@${dbHost}:$dbPort (user: $dbUser)",
    ("Git: {0} ({1})" -f ($(if ($gitHash -ne '') { $gitHash } else { 'n/a' }), $(if ($gitBranch -ne '') { $gitBranch } else { 'n/a' }))),
    "Files: $zipPath",
    "SQL: $sqlPath"
) | Set-Content -Encoding UTF8 $metaPath

Copy-Item (Join-Path $projectRoot '.env') $envPath -Force

Write-Host "[1/2] Exporting database '$dbName'..." -ForegroundColor Cyan
$oldMysqlPwd = $env:MYSQL_PWD
$env:MYSQL_PWD = $dbPass

try {
    # Use cmd redirection to avoid UTF-8 BOM and keep output streaming to disk.
    $dumpArgs = @(
        "--host=$dbHost",
        "--port=$dbPort",
        "--user=$dbUser",
        "--single-transaction",
        "--routines",
        "--triggers",
        "--events",
        "--default-character-set=utf8mb4",
        $dbName
    )

    $dumpCmd = '"{0}" {1} > "{2}"' -f $mysqldump, ($dumpArgs -join ' '), $sqlPath
    & cmd.exe /c $dumpCmd | Out-Null

    if ($LASTEXITCODE -ne 0) {
        throw "mysqldump failed with exit code $LASTEXITCODE"
    }

    if (!(Test-Path $sqlPath) -or (Get-Item $sqlPath).Length -le 0) {
        throw "Database dump was created but is empty: $sqlPath"
    }
} finally {
    $env:MYSQL_PWD = $oldMysqlPwd
}

Write-Host "[2/2] Creating project archive..." -ForegroundColor Cyan

# Use bsdtar to create a zip with exclusions
$excludes = @(
    '--exclude=.git',
    '--exclude=backups',
    '--exclude=node_modules',
    '--exclude=storage/logs',
    '--exclude=storage/framework/sessions',
    '--exclude=storage/framework/views',
    '--exclude=storage/framework/cache',
    '--exclude=bootstrap/cache'
)

$tarArgs = @('-a', '-c', '-f', $zipPath) + $excludes + @('-C', $projectRoot, '.')
& tar @tarArgs | Out-Null

$readme = Join-Path $backupDir 'RESTORE_README.txt'
@(
    "Restore Point created at: $stamp",
    "",
    "This folder contains:",
    "- project-*.zip      (project files)",
    "- database-*.sql     (MySQL dump of '$dbName')",
    "- .env.backup        (your local env; keep private)",
    "",
    "Quick restore (manual):",
    "1) Extract project zip to a folder",
    "2) Create database '$dbName' in phpMyAdmin (or mysql)",
    "3) Import database-*.sql into '$dbName'",
    "4) Put .env.backup back as .env",
    "5) Run: php artisan key:generate (only if needed)",
    "   and: php artisan storage:link",
    "",
    "You can also run scripts/restore_restore_point.ps1 to automate restore."
) | Set-Content -Encoding UTF8 $readme

Write-Host "Restore point created:" -ForegroundColor Green
Write-Host "- $backupDir"
