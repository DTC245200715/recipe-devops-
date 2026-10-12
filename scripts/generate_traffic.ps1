Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "  Dang tao luu luong truy cap dong (Traffic Generator)... " -ForegroundColor Cyan
Write-Host "  Tao request toi Web, Nginx, Database va Log he thong... " -ForegroundColor Cyan
Write-Host "==========================================================" -ForegroundColor Cyan

$keywords = @('pho', 'bun', 'chua', 'cuon', 'che', 'tofu', 'tra', 'com', 'bi', 'banh', 'xoai', 'gan')

for ($i = 1; $i -le 60; $i++) {
    $id = ($i % 12) + 1
    $kw = $keywords[$i % $keywords.Count]

    # 1. Truy cap trang chu & tim kiem
    curl.exe -k -s "https://localhost/" | Out-Null
    curl.exe -k -s "https://localhost/index.php?search=$kw" | Out-Null

    # 2. Truy cap chi tiet cong thuc (kich hoat MySQL Query)
    curl.exe -k -s "https://localhost/recipe.php?id=$id" | Out-Null

    # 3. Kiem tra health check
    curl.exe -k -s "https://localhost/health.php" | Out-Null

    # 4. Kich hoat HTTP -> HTTPS 301 redirect qua cong 8000
    if ($i % 3 -eq 0) {
        curl.exe -s "http://localhost:8000/" | Out-Null
    }

    # 5. Kich hoat 404 Not Found de sinh log loi
    if ($i % 10 -eq 0) {
        curl.exe -k -s "https://localhost/not-found-$i" | Out-Null
    }

    Write-Host "[Batch $i/60] Da gui cac request: Trang chu, Mon an #$id, Tim kiem '$kw'..." -ForegroundColor Green
    Start-Sleep -Milliseconds 150
}

Write-Host "==========================================================" -ForegroundColor Yellow
Write-Host "  Hoan tat! So lieu dong da duoc bom vao Grafana & Loki!  " -ForegroundColor Yellow
Write-Host "==========================================================" -ForegroundColor Yellow
