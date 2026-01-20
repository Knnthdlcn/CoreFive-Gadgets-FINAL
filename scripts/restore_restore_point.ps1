param(
    [Parameter(Mandatory = $true)]
    [string]$RestorePointDir,

    [string]$DestinationDir = "",

    [switch]$SkipFiles,
    [switch]$SkipDatabase
)

$ErrorActionPreference = 'Stop'

if (!(Test-Path $RestorePointDir)) {
    throw "RestorePointDir not found: $RestorePointDir"
}

$rp = (Resolve-Path $RestorePointDir).Path

$zip = Get-ChildItem -Path $rp -Filter 'project-*.zip' | Sort-Object LastWriteTime -Descending | Select-Object -First 1
$sql = Get-ChildItem -Path $rp -Filter 'database-*.sql' | Sort-Object LastWriteTime -Descending | Select-Object -First 1
$envBackup = Join-Path $rp '.env.backup'

if (!$SkipFiles -and !$zip) { throw "Missing project zip (project-*.zip) in: $rp" }
if (!$SkipDatabase -and !$sql) { throw "Missing database sql (database-*.sql) in: $rp" }

if ([string]::IsNullOrWhiteSpace($DestinationDir)) {
    $DestinationDir = Join-Path (Get-Location).Path ("restored-" + (Get-Date -Format 'yyyyMMdd-HHmmss'))
}

$DestinationDir = (Resolve-Path (Split-Path $DestinationDir -Parent)).Path + "\" + (Split-Path $DestinationDir -Leaf)

if (!$SkipFiles) {
    New-Item -ItemType Directory -Force -Path $DestinationDir | Out-Null
    Write-Host "[1/2] Extracting project to: $DestinationDir" -ForegroundColor Cyan
    & tar -xf $zip.FullName -C $DestinationDir

    if (Test-Path $envBackup) {
        Copy-Item $envBackup (Join-Path $DestinationDir '.env') -Force
    }
}

if (!$SkipDatabase) {
    # Read .env from restored project if available, else from backup copy
    if (Test-Path (Join-Path $DestinationDir '.env')) {
        $envPath = Join-Path $DestinationDir '.env'
    } else {
        $envPath = $envBackup
    }
    if (!(Test-Path $envPath)) {
        throw "No .env available to determine DB settings. Expected: $envPath"
    }

    $dotenv = @{}
    Get-Content $envPath | ForEach-Object {
        $line = $_.Trim()
        if ($line -eq "" -or $line.StartsWith('#')) { return }
        $idx = $line.IndexOf('=')
        if ($idx -lt 1) { return }
        $k = $line.Substring(0, $idx).Trim()
        $v = $line.Substring($idx + 1).Trim()
        if (($v.StartsWith('"') -and $v.EndsWith('"')) -or ($v.StartsWith("'") -and $v.EndsWith("'"))) {
            $v = $v.Substring(1, $v.Length - 2)
        }
        $dotenv[$k] = $v
    }

    $dbHost = (if ($dotenv.ContainsKey('DB_HOST')) { $dotenv['DB_HOST'] } else { '127.0.0.1' })
    $dbPort = (if ($dotenv.ContainsKey('DB_PORT')) { $dotenv['DB_PORT'] } else { '3306' })
    $dbName = (if ($dotenv.ContainsKey('DB_DATABASE')) { $dotenv['DB_DATABASE'] } else { '' })
    $dbUser = (if ($dotenv.ContainsKey('DB_USERNAME')) { $dotenv['DB_USERNAME'] } else { 'root' })
    $dbPass = (if ($dotenv.ContainsKey('DB_PASSWORD')) { $dotenv['DB_PASSWORD'] } else { '' })

    if ([string]::IsNullOrWhiteSpace($dbName)) { throw "DB_DATABASE is empty in .env" }

    $mysql = 'C:\xampp\mysql\bin\mysql.exe'
    if (!(Test-Path $mysql)) {
        $found = (Get-Command mysql -ErrorAction SilentlyContinue | Select-Object -ExpandProperty Source -First 1)
        if (![string]::IsNullOrWhiteSpace($found)) {
            $mysql = $found
        } else {
            throw "mysql client not found. Expected C:\xampp\mysql\bin\mysql.exe or on PATH."
        }
    }

    Write-Host "[2/2] Restoring database '$dbName'..." -ForegroundColor Cyan

    $oldMysqlPwd = $env:MYSQL_PWD
    $env:MYSQL_PWD = $dbPass
    try {
        # Create DB if needed
        $createDbSql = "CREATE DATABASE IF NOT EXISTS ``$dbName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        & $mysql "--host=$dbHost" "--port=$dbPort" "--user=$dbUser" -e $createDbSql | Out-Null
        if ($LASTEXITCODE -ne 0) {
            throw "mysql failed while creating database (exit code $LASTEXITCODE)"
        }

        # Import dump via cmd redirection (streaming + avoids encoding surprises)
        $importCmd = '"{0}" --host={1} --port={2} --user={3} {4} < "{5}"' -f $mysql, $dbHost, $dbPort, $dbUser, $dbName, $sql.FullName
        & cmd.exe /c $importCmd | Out-Null
        if ($LASTEXITCODE -ne 0) {
            throw "mysql failed while importing dump (exit code $LASTEXITCODE)"
        }
    } finally {
        $env:MYSQL_PWD = $oldMysqlPwd
    }
}

Write-Host "Restore completed." -ForegroundColor Green
if (!$SkipFiles) { Write-Host "- Files: $DestinationDir" }
if (!$SkipDatabase) { Write-Host "- Database restored from: $($sql.FullName)" }
