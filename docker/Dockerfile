# 基于php:8.1-apache镜像
FROM php:8.1-apache

ENV PHP_OB_START=0

# 安装git
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git && \
    apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# 设置工作目录
WORKDIR /var/www/html

# 克隆项目
RUN git clone https://github.com/Andromedasm/employee_broswer.git && \
    mv employee_broswer/* . && \
    rm -rf employee_broswer

# 添加 Apache 配置文件
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# 开放端口
EXPOSE 80 443 5432

# 使 Apache 在后台运行
CMD ["apache2-foreground"]
