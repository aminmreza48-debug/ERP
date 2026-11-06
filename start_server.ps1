# Script to ensure SIPERMA PHP server is running on port 8080
$port = 8080
$conn = Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue
if ($conn) {
    Write-Host "SIPERMA ERP PHP Server is already running on http://localhost:$port"
} else {
    Write-Host "Starting SIPERMA ERP PHP Server on http://localhost:$port..."
    Start-Process -FilePath "C:\xampp\php\php.exe" -ArgumentList "-S 0.0.0.0:8080 -t public" -WorkingDirectory "$PSScriptRoot" -WindowStyle Hidden
    Start-Sleep -Milliseconds 800
    Write-Host "SIPERMA ERP PHP Server started successfully."
}
