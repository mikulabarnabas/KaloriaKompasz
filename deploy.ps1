# 1. Frontend Build
Write-Host "--- Vite Build inditasa ---"
npm run build

# 2. Tömörítés
Write-Host "--- Build.zip keszitese ---"
if (Test-Path "build.zip") { Remove-Item "build.zip" }
# Fontos: a public/build mappat csomagoljuk be
Compress-Archive -Path public/build -DestinationPath build.zip
scp build.zip c78847@65658.wh14.rhweb.hu:/web/kaloriakompasz.hu/

Write-Host "--- KESZ! "