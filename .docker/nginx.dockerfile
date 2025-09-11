FROM nginx:alpine

RUN echo ' \
upstream app { \
    server app:9000; \
} \
server { \
    listen 80; \
    server_name _; \
    client_max_body_size 10M; \
    \
    root /var/www/html/public; \
    index index.php index.html; \
    error_log  /var/log/nginx/error.log warn; \
    \
    location / { \
        try_files $uri /index.php?$query_string; \
    } \
    \
    location ~ \.php$ { \
        include fastcgi_params; \
        fastcgi_pass app; \
        fastcgi_index index.php; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
    } \
}' > /etc/nginx/conf.d/default.conf

CMD ["nginx", "-g", "daemon off;"]