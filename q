[0;1;31m●[0m apache2.service - The Apache HTTP Server
     Loaded: loaded (/lib/systemd/system/apache2.service; enabled; vendor preset: enabled)
     Active: [0;1;31mfailed[0m (Result: exit-code) since Wed 2023-11-29 13:52:02 CET; 5min ago
       Docs: https://httpd.apache.org/docs/2.4/
    Process: 37086 ExecStart=/usr/sbin/apachectl start [0;1;31m(code=exited, status=139)[0m

nov. 29 13:52:02 matteo-ThinkPad-E14-Gen-3 systemd[1]: Starting The Apache HTTP Server...
nov. 29 13:52:02 matteo-ThinkPad-E14-Gen-3 apachectl[37089]: [Wed Nov 29 13:52:02.118809 2023] [so:warn] [pid 37089] AH01574: module php_module is already loaded, skipping
nov. 29 13:52:02 matteo-ThinkPad-E14-Gen-3 apachectl[37089]: AH00558: apache2: Could not reliably determine the server's fully qualified domain name, using 127.0.1.1. Set the 'ServerName' directive globally to suppress this message
nov. 29 13:52:02 matteo-ThinkPad-E14-Gen-3 apachectl[37086]: Segmentation fault (core dumped)
nov. 29 13:52:02 matteo-ThinkPad-E14-Gen-3 apachectl[37086]: Action 'start' failed.
nov. 29 13:52:02 matteo-ThinkPad-E14-Gen-3 apachectl[37086]: The Apache error log may have more information.
nov. 29 13:52:02 matteo-ThinkPad-E14-Gen-3 systemd[1]: [0;1;39m[0;1;31m[0;1;39mapache2.service: Control process exited, code=exited, status=139/n/a[0m
nov. 29 13:52:02 matteo-ThinkPad-E14-Gen-3 systemd[1]: [0;1;38;5;185m[0;1;39m[0;1;38;5;185mapache2.service: Failed with result 'exit-code'.[0m
nov. 29 13:52:02 matteo-ThinkPad-E14-Gen-3 systemd[1]: [0;1;31m[0;1;39m[0;1;31mFailed to start The Apache HTTP Server.[0m
