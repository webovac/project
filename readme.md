Webovac Project
===============

Installation
------------
```
composer create-project webovac/project path
mkdir temp log sessions
chmod -R a+rw temp log sessions
chmod +x bin/clear-cache
cp config/dev.neon config/local.neon 
```
Setup local parameters in `config/local.neon`
```
php bin/install.php
```


