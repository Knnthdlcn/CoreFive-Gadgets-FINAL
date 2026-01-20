# Starts Laravel dev server with increased upload limits (no php.ini edits required).
# Usage:
#   powershell -ExecutionPolicy Bypass -File .\scripts\serve_5mb.ps1

$ErrorActionPreference = 'Stop'

Set-Location -Path (Split-Path -Parent $PSScriptRoot)

# 5MB upload + 8MB post body (must be >= upload_max_filesize)
php -d upload_max_filesize=5M -d post_max_size=8M artisan serve
