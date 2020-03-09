git pull

php artisan cache:clear

$content = Get-Content -encoding UTF8 -path .env
$hash = git rev-parse --short HEAD
$date = git log -1 --date=short --pretty=format:%cd

$newContent = $content -replace 'VERSION=(.*)', "VERSION=$date-$hash"
$path = (Get-Location).Path + "\.env"

[System.IO.File]::WriteAllLines($path, $newContent)

php artisan config:cache
php artisan route:cache
php artisan view:cache
