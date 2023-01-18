# Simple MVC
A simple MVC in php

## Setup locally

* Import the `mvc.sql` file in MySql
* Change the `config/config.ini` database credentials to point to the new database
* Serve the app via PHP built-in server:
```
php -S localhost:8080 -t <PROJECT_DIR>/public
```
* Change the `url.web` value in `config/config.ini` to `http://localhost:8080/`
