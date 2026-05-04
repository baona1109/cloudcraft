---
title: "Hướng dẫn cài đặt PHP từ source"
date: 2018-10-03 16:40:42
categories: [Linux]
---

PHP (viết tắt của Personal Home Page) là một ngôn ngữ lập trình kịch bản (scripting language) mã nguồn mở được dùng phổ biến để tạo các ứng dụng web chạy trên máy chủ. Nó được sử dụng để quản lý nội dung động, Database, Session tracking,... PHP hỗ trợ nhiều giao thức lớn như POP3, IMAP, LDAP,... cú pháp của nó khá giống C do được phát triển từ ngôn ngữ lập trình C. **Hướng dẫn cài đặt** Trước tiên hết cần xác định xem đã cài đặt các công cụ hỗ trợ biên dịch mã nguồn chưa, nếu chưa thì chạy lệnh sau để tiến hành cài đặt 
    
    
    sudo yum install epel-release -y
    sudo yum groupinstall "Development Tools" -y

Để cài đặt được php bằng source tải từ trang chủ php về ta cần cài đặt một số gói thư viện cần thiết đi kèm với php bằng lệnh 
    
    
    sudo yum install -y c-client-devel openssl-devel libxml2-devel bzip2-devel curl-devel libjpeg-devel libpng-devel freetype-devel libc-client-devel.i686 libc-client-devel libmcrypt-devel

Cần update link tham số về các file cấu hình cũng như các thư viện kèm theo 
    
    
    sudo ldconfig

Bây giờ ta tiến hành tải source và cài đặt php 
    
    
    mkdir /home/dangtgh/php
    cd /home/dangtgh/php
    wget -O php-5.6.33.tar.gz http://sg2.php.net/distributions/php-5.6.33.tar.gz
    tar -xzf php-5.6.33.tar.gz
    cd php-5.6.33

Tiến hành cấu hình trước khi cài đặt như sau 
    
    
    sudo ./configure \
    --enable-bcmath \
    --with-bz2 \
    --enable-calendar \
    --with-curl \
    --enable-exif \
    --enable-ftp \
    --with-gd \
    --with-freetype-dir \
    --with-libdir=lib64 \
    --enable-gd-native-ttf \
    --with-imap \
    --with-imap-ssl \
    --with-kerberos \
    --with-mysql \
    --with-mysqli \
    --with-openssl \
    --with-pcre-regex \
    --with-pdo-mysql \
    --with-zlib-dir \
    --with-regex \
    --enable-soap \
    --enable-sockets \
    --with-xmlrpc \
    --enable-zip \
    --with-zlib \
    --enable-opcache \
    --enable-fpm \
    --prefix=/usr/local/php

**Với:** **\--prefix=/usr/local/php:** Đường dẫn nơi cài đặt cho php. Nếu muốn sử dụng đường dẫn khác thì có thể thay đổi ở đây. **\--with-gd:** Hỗ trợ database **\--with-bz2:** Cho php sử dụng giải nén đối với các file nén có đuôi bz2 **\--with-kerberos:** Tích hợp thêm thuật toán xác thực kerberos cho php **\--with-libdir=lib64:** Sử dụng loại thư viện cho php (có lib64 và lib) **\--enable-zip và --with-zlib:** Bật tính năng nén trên php và hỗ trợ nén zlib **\--with-mysql và --with-mysqli:** Thông báo php sử dụng đường dẫn dynamic linker cache (cần phải cài mysql trước đó) không cần phải chỉ định rõ đường dẫn (Dùng cho khi không biết rõ đường dẫn của mysql khi cài bằng repo) **\--enable-fpm:** Nên bật fpm kèm theo để sử dụng php-fpm. (Đây là tham số quan trọng khi cần php cần phải enable cùng trừ lý do riêng) **\--enable-opcache:** Bật tính năng opcache cho php, giúp tăng hiệu năng xử lý. Ngoài ra còn một số tính năng khác, có thể tìm hiểu thêm bằng việc dùng **./configure –help** để xem. Ở bên trên là config tham khảo, tùy người sử dụng có thể rút gọn đi hoặc thêm vào các module khác. Vì vậy trong quá trình chạy configure sẽ có thể gặp một số lỗi do thiếu thư viện, ta chỉ cần cài đặt thư viện đó bằng yum cho nhanh hoặc tìm source download về và biên dịch. Cài đặt bằng yum ta chỉ cần thêm –devel vào cuối tên thư viện 
    
    
    VD: yum install –y openssl-devel

Tiến hành cài đặt 
    
    
    sudo make -j2 && sudo make install

Do thực hiện cài đặt bằng source nên không có sẵn file script **php-fpm** nằm trong **/etc/init.d**. Ta cần cp file init.d.php-fpm vào và đổi tên lại thành **php-fpm**
    
    
    cd /etc/init.d
    sudo cp ~/php/php-5.6.6/sapi/fpm/init.d.php-fpm php-fpm
    sudo chmod +x php-fpm
    sudo vi php-fpm

Mở file script lên và thay đổi dòng (Nếu không thay đổi đường dẫn mặc định trong file **php-fpm.conf**) 
    
    
    php_fpm_PID=/var/run/php-fpm.pid

**Cấu hình** **Về việc cấu hình cho php-fpm (PHP – FastCGI Process Manager)** Thực hiện copy file php-fpm.conf.default trong thư mục nơi cài đặt php thành file php-fpm.conf 
    
    
    cd /usr/local/php/etc
    sudo mkdir fpm.d
    sudo cp php-fpm.conf.default php-fpm.conf

Tiến hành chỉnh sửa file php-fpm.conf thêm vào hoặc bỏ comment những dòng sau 
    
    
    sudo vi php-fpm.conf

Chỉnh sửa hoặc bỏ comment, ở đây có dòng pid nếu thay đổi ở đây thì phải chỉnh sửa lại đường dẫn cho file script phía trên. 
    
    
    include=etc/fpm.d/*.conf
    pid = /var/run/php-fpm.pid
    error_log = log/php-fpm.log

Theo như trên thì cấu hình nơi chứa log lỗi tại**/usr/local/php/var/log/php-fpm.log**. Khi sử dụng đường dẫn tương đối cho error_log trong **php-fpm.conf** thì đường dẫn mặc định trước đó là **/usr/local/php/var/**. Ngoài ra tiến hành include các file cấu hình fpm, trên hệ thống ta có thể chạy nhiều website với virtual host vì vậy mỗi virtual host thì tương ứng sẽ có file cấu hình fpm tương ứng riêng biệt. Ở đây cấu hình các file này nằm trong thư mục fpm.d Bên dưới cuối file **php-fpm.conf** bắt đầu từ dòng 
    
    
    #;;;;;;;;;;;;;;;;;;;;
    
    #; Pool Definitions ;
    
    #;;;;;;;;;;;;;;;;;;;;

Những dòng sau dòng trên là cấu hình dành cho các file config fpm dành cho virtual host riêng biệt. Giờ tạo thử một file config dangtgh_ga.conf dành cho virtual host có tên miền là dangtgh.ga và copy toàn bộ nội dung phía dưới dòng “**Pool Definitions** ” vào file config này rồi tiến hành chỉnh sửa một số tham số sau: 
    
    
    [dangtgh_ga] (đổi [www] thành tên khác theo tên miền mà server chạy)
    user=nginx
    group=nginx
    listen=var/run/dangtgh_ga-fpm.sock
    #listen=127.0.0.1:9002
    catch_workers_output = yes
    slowlog = var/logs/dangtgh_ga/php-fpm.slow.log
    request_slowlog_timeout = 30s
    php_flag[display_errors] = off
    php_admin_value[error_log] = /usr/local/php/var/logs/dangtgh_ga/php-fpm.error.log
    php_admin_flag[log_errors] = on
    php_admin_value[memory_limit] = 64M

  Dựa vào cấu hình trên ta sẽ thấy một vài tham số được bao lại bởi **php_admin_value** và **php_admin_flag**. Đây là 2 tham dùng để thay đổi giá trị các biến global trong**php.ini** (Với **php_admin_value** dùng cho biến chứa giá trị, **php_admin_flag** dùng cho biến bật tắt module). Khi đó nếu các request thuộc về domain dangtgh.ga của virtual host này sẽ được xử lý php với tham số được cấu hình tại đây mà không phải là tham số mặc định của php được cấu hình trong **php.ini.** Do đây là cấu hình dựa trên việc kết hợp với nginx nên phần **user** và **group** nên dùng user và group của nginx (tương tự nếu là lamp stack với apache thì đổi lại) Biến **listen** là tham số yêu cầu fpm này nghe trên socket được đặt tại **/usr/local/php/var/run/dangtgh_ga-fpm.sock**. Ta cũng có thể thay socket thành địa chỉ IP và port nhưng sẽ **chậm hơn là sử dụng socket** vì socket nhanh hơn qua port TCP nếu thực hiện xử lý fpm ngay tại trên server vì thực hiện qua socket sẽ không cần qua các bước đóng gói của tcp, chỉ nên dùng port khi việc xử lý fpm diễn ra trên một server khác. **catch_workers_output** cần được bật lên để các thông tin stdin và stderr được xuất vào file log lỗi chính đã cấu hình. Nếu tắt đi thì các thông báo sẽ được đẩy ra file /dev/null Trong file **php-fpm.conf** có tham số **process.max** giá trị mặc định là 128 là số lượng tối đa process mà FPM có thể tạo. Ta cần copy file php.ini-development trong bộ mã nguồn vào thư mục **/usr/local/php/lib**
    
    
    cd /usr/local/php/lib
    sudo cp ~/sources/php-5.6.6/php.ini-development ./php.ini

**Tham khảo** <http://php.net/manual/en/ini.core.php#ini.short-open-tag> <http://vietjack.com/php/> <https://blacksaildivision.com/php-install-from-source> <https://stackoverflow.com/questions/20533436/php-fpm-error-on-ec2-the-process-manager-is-missing-static-dynamic-or-ondema?rq=1> <https://www.digitalocean.com/community/tutorials/how-to-host-multiple-websites-securely-with-nginx-and-php-fpm-on-ubuntu-14-04> <https://unix.stackexchange.com/questions/91774/performance-of-unix-sockets-vs-tcp-ports>
