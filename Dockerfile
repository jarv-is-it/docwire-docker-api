# A Docker image that exposes DocWire through a simple REST API
# Copyright (C) 2024 Filippo Toso - https://toso.dev/
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

FROM ubuntu:noble
LABEL Author="Filippo Toso" Description="A docker image that exposes a DocWire through a simple REST API"

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    apt-get -y install \
        supervisor \
        cron \
        apache2 \
        libapache2-mod-php \
        libapache2-mod-auth-openidc \
        php-bcmath \
        php-cli \
        php-curl \
        php-gd \
        php-intl \
        php-json \
        php-ldap \
        php-mbstring \
        php-memcached \
        php-mysql \
        php-pgsql \
        php-sqlite3 \
        php-soap \
        php-tidy \
        php-uploadprogress \
        php-xml \
        php-xmlrpc \
        php-yaml \
        php-zip \
        wget \
        tar \
        bzip2 \ 
        libgomp1 \
        imagemagick \
        libcap2-bin && \
    setcap 'cap_net_bind_service=+ep' /usr/sbin/apache2 && \
    dpkg --purge libcap2-bin && \
    apt-get -y autoremove && \
    apt-get clean && \
    a2disconf other-vhosts-access-log && \
    chown -Rh www-data:www-data /var/run/apache2 && \
    rm -rf /var/lib/apt/lists/* && \
    a2enmod rewrite headers expires ext_filter

# Install docwire
RUN mkdir /opt/temp && \
    wget -c "https://github.com/docwire/docwire/releases/download/2024.07.31/docwire-2024.07.31-x64-linux-dynamic-gcc-13.2.0.tar.bz2" -P /opt/temp && \
    tar -xvf "/opt/temp/docwire-2024.07.31-x64-linux-dynamic-gcc-13.2.0.tar.bz2" -C /opt/temp && \
    mv /opt/temp/docwire-2024.07.31 /usr/local/docwire && \
    rm -rf /opt/temp/ && \
    rm -f /usr/local/docwire/.vcpkg-root && \
    rm -f /usr/local/docwire/setup_env.sh && \
    rm -f /usr/local/docwire/vcpkg && \
    rm -rf /usr/local/docwire/scripts && \
    rm -rf /usr/local/docwire/installed/vcpkg && \
    rm -rf /usr/local/docwire/installed/x64-linux \
    rm -rf /usr/local/docwire/installed/x64-linux-dynamic/debug && \
    rm -rf /usr/local/docwire/installed/x64-linux-dynamic/include && \
    rm -rf /usr/local/docwire/installed/x64-linux-dynamic/share && \
    rm -rf /usr/local/docwire/installed/x64-linux-dynamic/src 
    
ENV PATH="/usr/local/docwire/installed/x64-linux-dynamic/tools:$PATH"
ENV LD_LIBRARY_PATH="/usr/local/docwire/installed/x64-linux-dynamic/lib:/usr/local/docwire/installed/x64-linux-dynamic/lib/docwire_system_libraries:$LD_LIBRARY_PATH"
ENV CPLUS_INCLUDE_PATH="/usr/local/docwire/installed/x64-linux-dynamic/include:$CPLUS_INCLUDE_PATH"
ENV LIBRARY_PATH="/usr/local/docwire/installed/x64-linux-dynamic/lib:$LIBRARY_PATH"
ENV OPENSSL_MODULES="/usr/local/docwire/installed/x64-linux-dynamic/lib/ossl-modules"

# Install composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG CACHEBUST_CONFIG=3

# Override default apache and php config

COPY config/000-default.conf /etc/apache2/sites-available
COPY config/mpm_prefork.conf /etc/apache2/mods-available
COPY config/99-local.ini     /etc/php/8.1/apache2/conf.d
COPY config/supervisor.conf     /etc/supervisor/conf.d/supervisor.conf

RUN rm -f /var/www/html/index.html

# Setup application

ARG CACHEBUST_COMPOSER=2

COPY ./src/composer.json /var/www/html
COPY ./src/composer.lock /var/www/html

WORKDIR /var/www/html

RUN composer install --no-scripts

ARG CACHEBUST_APP=4

COPY src /var/www/html

COPY config/.env /var/www/html

RUN php artisan key:generate
RUN php artisan app:setup-platform
RUN php artisan view:cache
RUN php artisan route:cache
RUN php artisan event:cache
RUN php artisan config:cache

RUN chown -R www-data:www-data /var/log/apache2
RUN chown -R www-data:www-data /var/www/html

RUN chmod -R 755 /var/www/html/storage
RUN chmod -R 755 /var/www/html/bootstrap/cache

COPY config/laravel /etc/cron.d/laravel
RUN chmod 0644 /etc/cron.d/laravel
RUN crontab -u www-data /etc/cron.d/laravel

RUN mkdir /opt/tools
COPY scripts/entrypoint.sh /opt/tools/entrypoint.sh
RUN chmod +x /opt/tools/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/opt/tools/entrypoint.sh"]