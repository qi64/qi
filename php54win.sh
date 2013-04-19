#!/bin/bash

# http://stackoverflow.com/questions/5034076/what-does-dp0-mean-and-how-does-it-work

cd /tmp # diretorio temporario para download e gerar a instalacao
src=src # pasta onde sera extraido o php
dst=php54 # pasta destino onde sera gerado a instalacao
v=php-5.4.14 # versao do php para baixar
zip=$v-Win32-VC9-x86.zip
url=http://windows.php.net/downloads/releases/$zip

# download e unzip o php para a pasta $src
download() {
	echo "downloading $v"
	wget -c $url
	echo "unzipping $v"
	unzip -o -q $zip -d $src
}

# instalacao minima funcional do php, sem nenhuma extensao
minimum() {
	echo "basic PHP"
	rm -rf $dst
	mkdir -p $dst
	cp $src/php.exe $dst/
	cp $src/php5ts.dll $dst/php5ts.dll
	cat <<'S' > $dst/start.bat
start http://localhost:8081/phpinfo.php
"%~d0%~p0php" -S 0.0.0.0:8081 -t "%~d0%~p0www"
S
	mkdir -p $dst/www
	echo "<?php phpinfo();" > $dst/www/phpinfo.php
}

# configuracao minima do php.ini
phpini() {
	echo "php.ini"
cat <<'S' > $dst/php.ini
short_open_tag = On
display_errors = On
display_startup_errors = On
html_errors = Off
log_errors = On
safe_mode = Off

date.timezone = America/Sao_Paulo
magic_quotes_gpc = Off
allow_url_fopen = On
phar.readonly = Off

memory_limit = 128M
mysql.connect_timeout = 20
upload_max_filesize = 8M

max_input_time = 20
max_execution_time = 20
default_socket_timeout = 20

extension_dir=ext
S
}

# adiciona uma extensao a pasta e ao php.ini
add_dll() {
	dll=php_$1.dll
	cp $src/ext/$dll $dst/ext/
	echo "extension=$dll" >> $dst/php.ini
}

# instala extensoes basicas, deixando de fora as maiores
install_ext() {
	echo "ext"
	mkdir -p $dst/ext
	for dll in mysql mysqli pdo_mysql pdo_sqlite sqlite3 bz2 pdo_odbc exif sockets
	do
		add_dll $dll
	done
}

# extensoes extras maiores
install_extra_ext() {
	echo "extra ext"
	mkdir -p $dst/ext
	for dll in gd2 curl
	do
		add_dll $dll
	done
}

# baixa o composer e cria o composer.bat
install_composer() {
	echo "composer"
	rm -f composer.phar
	curl -sS https://getcomposer.org/installer | php -d detect_unicode=Off
	chmod +x composer.phar
	mv composer.phar $dst/
	# expand drive:\path\to\dir\name
	echo '"%~d0%~p0php" "%~d0%~p0%~n0.phar" %*' > $dst/composer.bat
}

# baixa o phpunit e cria o phpunit.bat
install_phpunit() {
	echo "phpunit"
	rm -f phpunit.phar
	wget -c -q http://pear.phpunit.de/get/phpunit.phar
	chmod +x phpunit.phar
	mv phpunit.phar $dst/
	echo '"%~d0%~p0php" "%~d0%~p0%~n0.phar" %*' > $dst/phpunit.bat
}

# instalar suporte a ssl
install_ssl() {
	echo "ssl"
	cp $src/libeay32.dll $dst/
	cp $src/ssleay32.dll $dst/
	add_dll openssl
}

# gerar instalador zip
compress() {
	echo "zip"
	date=$(date +%Y-%m-%d_%H.%M | tr -d '\n')
	zip -r -9 -q $v-$date.zip $dst/ 
}

download
minimum
phpini
install_ext
install_composer
install_phpunit
install_ssl
compress
