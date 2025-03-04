FROM php:5.6-apache

# 更新为 Debian Stretch 归档源
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i 's/security.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i '/stretch-updates/d' /etc/apt/sources.list

# 安装必要的依赖
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    msmtp \
    msmtp-mta \
    ca-certificates \
    && docker-php-ext-install mysql mysqli \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && a2enmod rewrite

# 创建 PHP 配置目录
RUN mkdir -p /usr/local/etc/php/conf.d

# 复制 PHP 配置文件
COPY php.ini /usr/local/etc/php/php.ini

# 设置工作目录
WORKDIR /var/www/html

# 复制网站文件
COPY ./htdocs/ /var/www/html/

# 创建 sendmail 配置
RUN mkdir -p /var/www/html/freespc/includes/sendmail \
    && echo -e "[sendmail]\nsmtp_server=localhost\nsmtp_port=25\ndefault_domain=localhost" > /var/www/html/freespc/includes/sendmail/sendmail_example.ini

# 设置权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 配置 Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 暴露端口
EXPOSE 80

# 启动 Apache
CMD ["apache2-foreground"]