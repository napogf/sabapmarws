@echo off
move c:\ESPI\PROT*.zip C:\xamppNew\xampp\htdocs\mibac\dacaricare\
c:\xamppNew\xampp\php\php.exe -f C:\xamppNew\xampp\htdocs\mibac\batchUploadXML.php >> c:\xamppNew\uploads.log