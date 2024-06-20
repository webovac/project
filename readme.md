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
php vendor/webovac/core/src/install.php
```

Create `config/local.neon` from `config/dev.neon` and set local parameters.

```
php bin/install.php
```
