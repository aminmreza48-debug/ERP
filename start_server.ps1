# Script to ensure SIPERMA PHP server is running on port 8080
$port = 8080
$projectDir = "d:\semseter 5\ERP"
$publicDir = "$projectDir\public"
$phpExe = "C:\xampp\php\php.exe"

$conn = Get-NetTCPConnection -LocalPort $port -State Listen -ErrorAction SilentlyContinue
if ($conn) {
    Write-Host "SIPERMA ERP PHP Server is already running on http://localhost:$port"
} else {
    Write-Host "Starting SIPERMA ERP PHP Server on http://localhost:$port..."
    Start-Process -FilePath $phpExe -ArgumentList "-S 0.0.0.0:$port -t `"$publicDir`"" -WorkingDirectory $projectDir -WindowStyle Hidden
    Start-Sleep -Milliseconds 1000
    Write-Host "SIPERMA ERP PHP Server started successfully on http://localhost:$port."
}
