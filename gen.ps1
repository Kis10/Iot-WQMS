Add-Type -AssemblyName System.Drawing
$img = [System.Drawing.Image]::FromFile("c:\xampp\htdocs\water-quality-system\public\img\logo\logo-wq.png")
$bmp192 = new-object System.Drawing.Bitmap 192, 192
$g192 = [System.Drawing.Graphics]::FromImage($bmp192)
$g192.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
$g192.DrawImage($img, 0, 0, 192, 192)
$bmp192.Save("c:\xampp\htdocs\water-quality-system\public\img\logo\logo-192.png", [System.Drawing.Imaging.ImageFormat]::Png)
$g192.Dispose()

$bmp512 = new-object System.Drawing.Bitmap 512, 512
$g512 = [System.Drawing.Graphics]::FromImage($bmp512)
$g512.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
$g512.DrawImage($img, 0, 0, 512, 512)
$bmp512.Save("c:\xampp\htdocs\water-quality-system\public\img\logo\logo-512.png", [System.Drawing.Imaging.ImageFormat]::Png)
$g512.Dispose()

# Create actual ICO file header since GDI+ save as Icon sometimes behaves weirdly with PNG streams
$bmp64 = new-object System.Drawing.Bitmap 64, 64
$g64 = [System.Drawing.Graphics]::FromImage($bmp64)
$g64.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
$g64.DrawImage($img, 0, 0, 64, 64)
$bmp64.Save("c:\xampp\htdocs\water-quality-system\public\img\logo\logo-64.png", [System.Drawing.Imaging.ImageFormat]::Png)
$g64.Dispose()
$bmp192.Dispose()
$bmp512.Dispose()
$bmp64.Dispose()
$img.Dispose()

$bytes = [System.IO.File]::ReadAllBytes("c:\xampp\htdocs\water-quality-system\public\img\logo\logo-64.png")
$icoFile = [System.IO.File]::Create("c:\xampp\htdocs\water-quality-system\public\favicon.ico")
$bw = new-object System.IO.BinaryWriter($icoFile)
$bw.Write([uint16]0)
$bw.Write([uint16]1)
$bw.Write([uint16]1)
$bw.Write([byte]64)
$bw.Write([byte]64)
$bw.Write([byte]0)
$bw.Write([byte]0)
$bw.Write([uint16]1)
$bw.Write([uint16]32)
$bw.Write([uint32]$bytes.Length)
$bw.Write([uint32]22)
$bw.Write($bytes, 0, $bytes.Length)
$bw.Close()
$icoFile.Close()
