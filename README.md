# CS2102 Stuff Sharing

## Installation instructions (Windows)

**1. Download the files**

Place them in `{BITNAMI_INSTALL_DIR}\apps\cs2102`. A typical Bitnami installation directory might look like `C:\Bitnami\wappstack-5.6.30-0\apps\cs2102`.

**2. Install dependencies**

The application uses Composer to manage dependencies. You can download Composer and view its documentation at https://getcomposer.org/.

In cmd, change your working directory to the location of your PHP installation. Make sure `composer.phar` is in the same directory.

Run the following command:
```
php composer.phar install -d {BITNAMI_INSTALL_DIR}\apps\cs2102
```

The `-d` option installs the dependencies into the project folder.

**3. Configure app**

Update the directories in `httpd-app.conf`, `httpd-prefix.conf`, `httpd-vhosts.conf` in the `conf` folder.

For example, if your project is located at `C:\Bitnami\wappstack-5.6.30-0\apps\cs2102`, modify line 2 in `conf\httpd-app.conf`
to the following:
```ApacheConf
<Directory "C:\Bitnami\wappstack-5.6.30-0/apps/cs2102/htdocs">
```

**4. Configure server**

Edit `bitnami-apps-prefix.conf` in `{BITNAMI_INSTALL_DIR}\apache2\conf\bitnami` and add this line:
```
Include "{BITNAMI_INSTALL_DIR}\apps\cs2102/conf/httpd-prefix.conf"
```

**5. Configure database connection**

Edit `my_db_settings.ini` in `{BITNAMI_INSTALL_DIR}\apps\cs2102\htdocs` and change the settings to suit your personal configuration.  

The `driver` setting indicates the database management system that is being used.  
The `host` setting refers to the host on which the database resides.  
The `port` setting refers to the port number to use for the database connection.  
The default `username` is postgres.  
Use the same `password` that you specified when you first installed WAPP.  

To start a database connection, you can use
```php
Flight::register('db', 'MyPDO');
$db = Flight::db();
```

From there, you can use the methods specified in http://php.net/manual/en/class.pdo.php to interact with the database server. 

**6. Running database scripts**

- Run `{BITNAMI_INSTALL_DIR}\use_wappstack.bat`. In the cmd window, type the following command to run PostGreSQL:
```
psql -d postgres -U postgres
```
`-d` specifies the name of the database, `-U` specifies your username.

- Type in your password when prompted.

- Change your directory using the command
```
\cd {BITNAMI_INSTALL_DIR}/apps/cs2102/sql
```

- Run the SQL scripts in the following order to initialise the database
```
\i clean.sql
\i schema.sql
\i users.sql
\i categories.sql
\i listings.sql
```

## Documentation

We're using Flight, a lightweight framework for PHP, to simplify MVC creation. Documentation for Flight can be found at http://flightphp.com/learn/.

Flight has an issue tracker on Github, located at https://github.com/mikecao/flight/issues.

Some issues of interest:
* [Configuring database with PDO](https://github.com/mikecao/flight/issues/34)
