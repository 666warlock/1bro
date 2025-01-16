<?php 
// Fungsi untuk menghitung jumlah baris dalam sebuah file 
function getFileRowCount($filename) 
{ 
    if (!file_exists($filename)) { 
        return 0; 
    } 
    return count(file($filename, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES)); 
} 
 
// Paksa protokol menjadi HTTPS 
$protocol = 'https'; // Dipaksa menggunakan HTTPS 
$host = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL); 
$scriptName = dirname(filter_input(INPUT_SERVER, 'SCRIPT_NAME', FILTER_SANITIZE_URL)); 
$urlBase = rtrim($protocol . "://" . $host . $scriptName, '/') . '/'; 
 
// Membuat file robots.txt 
$robotsTxt = "User-agent: *" . PHP_EOL; 
$robotsTxt .= "Allow: /" . PHP_EOL; 
$robotsTxt .= "Sitemap: " . $urlBase . "sitemap.xml" . PHP_EOL; 
file_put_contents('robots.txt', $robotsTxt); 
 
// Nama file input 
$judulFile = "list.txt"; 
 
// Memastikan file input ada 
if (!file_exists($judulFile)) { 
    header("HTTP/1.1 404 Not Found"); 
    echo "File $judulFile tidak ditemukan."; 
    exit; 
} 
 
// Mengatur zona waktu 
date_default_timezone_set('Asia/Bangkok'); 
 
// Membuat file sitemap.xml 
$sitemapFile = fopen("sitemap.xml", "w"); 
fwrite($sitemapFile, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL); 
fwrite($sitemapFile, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL); 
 
// Membaca isi file dan menulis ke sitemap.xml 
$fileLines = file($judulFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); 
foreach ($fileLines as $judul) { 
    $sitemapLink = htmlspecialchars($urlBase . '?go=' . urlencode($judul), ENT_QUOTES, 'UTF-8'); 
    fwrite($sitemapFile, '  <url>' . PHP_EOL); 
    fwrite($sitemapFile, '    <loc>' . $sitemapLink . '</loc>' . PHP_EOL); 
    $currentTime = date('Y-m-d\TH:i:sP'); 
    fwrite($sitemapFile, '    <lastmod>' . $currentTime . '</lastmod>' . PHP_EOL); 
    fwrite($sitemapFile, '    <changefreq>daily</changefreq>' . PHP_EOL); 
    fwrite($sitemapFile, '  </url>' . PHP_EOL); 
} 
fwrite($sitemapFile, '</urlset>' . PHP_EOL); 
fclose($sitemapFile); 
 
// Menampilkan pesan 403 Forbidden hanya jika akses terlarang 
http_response_code(403); 
echo "<title>403 Forbidden</title>"; 
echo "<h1>Forbidden</h1>"; 
echo "<p>You don't have permission to access this resource.</p>"; 
?>
