server {

	if ($you_shall_not_pass = yes) {
	  return 444;
	}
	
	server_name admin.patriot-sport.ru;	

	listen 443 ssl;
	root /var/www/patriot-sport.ru;
	index index.php index.html;
	
	proxy_cookie_path / "/; HTTPOnly; Secure";

	location / {
		try_files $uri $uri/ =404;
	}
	
	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
		fastcgi_read_timeout 10m;
	}

	location ~ /\.ht {
		deny all;
	}
    ssl_certificate /etc/letsencrypt/live/patriot-sport.ru/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/patriot-sport.ru/privkey.pem; # managed by Certbot
}
