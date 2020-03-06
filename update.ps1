git pull

php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

$content = Get-Content -path .env
$hash = git rev-parse --short HEAD
$date = git log -1 --date=short --pretty=format:%cd
$content -replace 'VERSION=(.*)', "VERSION=$date-$hash" | Out-File ".env"
