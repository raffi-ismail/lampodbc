FROM    ubuntu:18.04
LABEL   maintainer="ChubbyCat" \
        git="https://github.com/raffi-ismail/lampodbc" \
        dockerhub="https://hub.docker.com/r/chubbycat/lampodbc"

RUN echo '#!/bin/sh' > /usr/sbin/policy-rc.d  && \
    echo 'exit 101' >> /usr/sbin/policy-rc.d  && \
    chmod +x /usr/sbin/policy-rc.d            && \
    dpkg-divert --local --rename --add /sbin/initctl  && \
    cp -a /usr/sbin/policy-rc.d /sbin/initctl         && \
    sed -i 's/^exit.*/exit 0/' /sbin/initctl          && \
    echo 'force-unsafe-io' > /etc/dpkg/dpkg.cfg.d/docker-apt-speedup   && \
    echo 'DPkg::Post-Invoke { "rm -f /var/cache/apt/archives/*.deb /var/cache/apt/archives/partial/*.deb /var/cache/apt/*.bin || true"; };' > /etc/apt/apt.conf.d/docker-clean  && \
    echo 'APT::Update::Post-Invoke { "rm -f /var/cache/apt/archives/*.deb /var/cache/apt/archives/partial/*.deb /var/cache/apt/*.bin || true"; };' >> /etc/apt/apt.conf.d/docker-clean && \
    echo 'Dir::Cache::pkgcache ""; Dir::Cache::srcpkgcache "";' >> /etc/apt/apt.conf.d/docker-clean   && \
    echo 'Acquire::Languages "none";' > /etc/apt/apt.conf.d/docker-no-languages   && \
    echo 'Acquire::GzipIndexes "true"; Acquire::CompressionTypes::Order:: "gz";' > /etc/apt/apt.conf.d/docker-gzip-indexes   && \
    echo 'Apt::AutoRemove::SuggestsImportant "false";' > /etc/apt/apt.conf.d/docker-autoremove-suggests

RUN rm -rf /var/lib/apt/lists/* && \
    mkdir -p /run/systemd && echo 'docker' > /run/systemd/container && \
    echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections

RUN apt-get update -qq && apt-get upgrade -qqy && \
    apt-get install -qq -y apt-utils curl git \
            software-properties-common gcc make autoconf libc-dev pkg-config

RUN add-apt-repository ppa:ondrej/php && \
    apt-get install -qqy nano apt-transport-https bash zip unzip jq apache2 \
            php7.0 php7.0-fpm php-xml php7.0-xml php-pear php7.0-dev php7.0-zip php7.0-curl php7.0-gd \
            php7.0-zip \
            php7.0-mysql php7.0-mcrypt php7.0-mbstring && \
    apt-get update -qqy 

RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/ubuntu/18.04/prod.list > /etc/apt/sources.list.d/mssql-release.list && \
    curl https://packages.microsoft.com/config/ubuntu/18.04/packages-microsoft-prod.deb -o /tmp/packages-microsoft-prod.deb && \
    dpkg -i /tmp/packages-microsoft-prod.deb

RUN apt-get update -qqy && ACCEPT_EULA=Y apt-get install -qqy msodbcsql17 mssql-tools unixodbc-dev powershell
RUN echo extension=sqlsrv.so > /etc/php/7.0/mods-available/sqlsrv.ini && \
    echo extension=pdo_sqlsrv.so > /etc/php/7.3/mods-available/pdo_sqlsrv.ini && \
    ln -s /etc/php/7.0/mods-available/sqlsrv.ini /etc/php/7.0/fpm/conf.d/30-sqlsrv.ini && \
    ln -s /etc/php/7.0/mods-available/pdo_sqlsrv.ini /etc/php/7.0/fpm/conf.d/30-pdo_sqlsrv.ini && \
    ln -s /etc/apache2/conf-available/php7.0-fpm.conf /etc/apache2/conf-enabled/php7.0-fpm.conf && \
    curl -sLo /tmp/tmp.deb http://mirrors.kernel.org/ubuntu/pool/multiverse/liba/libapache-mod-fastcgi/libapache2-mod-fastcgi_2.4.7~0910052141-1.2_amd64.deb && \
    dpkg -i /tmp/tmp.deb; apt-get install -f && \
    a2enmod actions fastcgi alias proxy_fcgi && \
    pecl install sqlsrv pdo_sqlsrv 

RUN apt-get update && apt-get install -y --no-install-recommends openssh-server  && echo "root:Docker!" | chpasswd

COPY etc/sshd_config /etc/ssh/

#COPY code-server1.1156-vsc1.33.1-linux-x64.tar.gz /tmp/code-server.tar.gz
#RUN mkdir /var/www/code-server && tar --strip-components 1 -zxf /tmp/code-server.tar.gz -C /var/www/code-server && chmod +x /var/www/code-server/code-server

COPY etc/apache2.conf /etc/apache2/
COPY etc/000-default.conf /etc/apache2/sites-available
COPY etc/php-fpm.conf /etc/php/7.0/fpm/
COPY etc/php.ini /etc/php/7.0/fpm/
COPY etc/www.conf /etc/php/7.0/fpm/pool.d/

WORKDIR /var/www
COPY etc/composer.json /var/www/
COPY sh/setup-composer.sh /tmp/
RUN chmod +x /tmp/setup-composer.sh && cd /var/www/ && /tmp/setup-composer.sh && ./composer.phar install

RUN mv /var/www/html/index.html /var/www/html/index.old.html
ADD html /var/www/html/
COPY startup.sh /var/

WORKDIR /var/www/html/fiddle
RUN mkdir -p sandbox && chmod 777 sandbox
RUN chmod +x /var/startup.sh
EXPOSE 2222 443 80 


ENTRYPOINT ["/var/startup.sh"] 