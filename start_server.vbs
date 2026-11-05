Set WshShell = CreateObject("WScript.Shell")
WshShell.CurrentDirectory = "d:\semseter 5\ERP"
WshShell.Run "cmd /c ""C:\xampp\php\php.exe -S 0.0.0.0:8080 -t public > d:\semseter 5\ERP\server.log 2>&1""", 0, False
