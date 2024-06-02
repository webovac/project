Webovac Project
=================

Installation
------------
[comment]: # (Still another comment)
```
composer create-project nette/web-project path/to/install
mkdir temp log sessions
chmod -R a+rw temp log sessions
chmod +x bin/clear-cache
cp config/dev.neon config/local.neon 
```
Setup local parameters in `config/local.neon`
```
php bin/install.php
```


