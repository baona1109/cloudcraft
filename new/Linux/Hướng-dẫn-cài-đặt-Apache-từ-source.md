---
title: "Hướng dẫn cài đặt Apache từ source"
date: 2018-03-23 16:00:32
categories: [Linux]
---

# **Giới thiệu**

Apache HTTP server (Apache), là một phần mềm webserver được sử dụng rộng rãi và phổ biến trên thế giới. Apache được phát triển và duy trì bởi cộng đồng mã nguồn mở dưới sự bảo trợ của Apache Software Foundation. Apache là một phần mềm có nhiều tính năng mạnh và linh hoạt để làm webserver như:

  * Hỗ trợ đầy đủ các giao thức HTTP cũ như HTTP/1.1
  * Có hỗ trợ chứng thực người dùng, virtual hosting, hỗ trợ nhiều module khác như CGI, FCGI, HTTPS, Ipv6,..
  * Có thể chạy trên nhiều nền tảng như Windows, linux,...



# **Cài đặt apache bằng cách biên dịch mã nguồn**

Phần này sẽ hướng dẫn các bước cần thiết để có thể tiến hành build source của apache để tiến hành cài phiên bản theo nhu cầu của từng người. Bài viết này hướng dẫn cách cài đặt apache phiên bản 2.4.29. Các phiên bản khác cài đặt tương tự. Trước khi tiến hành build source thì cần phải chắc chắn rằng server đã được cài đặt các gói thư viên, công cụ hỗ trợ cho việc build source như gcc, c++, apr-devel,... Chạy các lệnh sau cài đặt repo cho Red Hat và các thư viện cho việc build source:
    
    
    yum install epel-release -y
    yum install gcc-c++ autoconf gcc* wget lynx libtool apr-devel apr-util-devel apr apr-util pcre pcre-devel openssl openssl-devel zlib zlib-devel –y

Các gói pcre, apr, openssl nếu không muốn sử dụng phiên bản đang tích hợp tại yum để cài tự động như lệnh trên thì có thể tiến hành tải từng phiên bản build tay để dễ dàng tích hợp cấu hình khi compile cho apache. Tải các gói thư viện cần thiết và bộ source của apache
    
    
    mkdir /home/dangtgh/apache
    cd /home/dangtgh/apache
    wget http://mirrors.viethosting.com/apache//apr/apr-1.6.3.tar.gz
    wget http://mirrors.viethosting.com/apache//apr/apr-util-1.6.1.tar.gz
    wget http://mirrors.viethosting.com/apache//apr/apr-iconv-1.2.2.tar.gz
    wget https://ftp.pcre.org/pub/pcre/pcre-8.41.tar.gz
    wget https://www.openssl.org/source/old/1.0.2/openssl-1.0.2m.tar.gz
    wget http://mirrors.viethosting.com/apache//httpd/httpd-2.4.29.tar.gz

Tiến hành giải nén các file đã tải bên trên
    
    
    tar xvf apr-1.6.3.tar.gz
    tar xvf apr-util-1.6.1.tar.gz
    tar xvf apr-iconv-1.2.2.tar.gz
    tar xvf pcre-8.41.tar.gz
    tar xvf openssl-1.0.2m.tar.gz
    tar xvf httpd-2.4.29.tar.gz

Thư mục httpd-2.4.29 sau khi giải nén, đây là thư mục chứa source của apache. Trước khi tiến hành cài đặt ta cần làm một số thao tác sau. Copy toàn bộ thư mục apr-x, apr-util-x, apr-iconv-x vào folder **srclib** nằm trong folder httpd-2.4.29 và đổi tên thành apr, apr-util, apr-iconv để khi tiến hành config file cấu hình của apache có thể nhận diện được. Với x là số phiên bản của các gói, ở đây là apr-1.6.3, apr-util-1.6.1, apr-iconv-1.2.2.
    
    
    cp -rp apr-1.6.3 httpd-2.4.29/srclib/apr
    cp -rp apr-util-1.6.1 httpd-2.4.29/srclib/apr-util
    cp -rp apr-iconv-1.2.2 httpd-2.4.29/srclib/apr-iconv

Cài đặt thư viện lib với đường dẫn prefix = /usr/lib
    
    
    cd pcre-8.41
    sudo ./configure --prefix=/usr/lib/
    sudo make && sudo make install

Di chuyển thư mục chứa mã nguồn openssl đã giải nén bên trên vào /**usr/local/src/** tùy mỗi người muốn đặt ở đâu thì đặt nhưng phải nhớ để đưa vào cấu hình khi cài đặt apache. Ở hướng dẫn này thì được đặt tại **/usr/local/src/**
    
    
    cp –rp openssl-1.0.2m usr/local/src/openssl-1.0.2m

Tiến hành cấu hình và compile apache, nên chú ý các đường dẫn cho chính xác đặc biệt là đường dẫn openssl với cờ là "**\--with-ssl=** ”.
    
    
    cd ../httpd-2.4.29
    
    
    sudo ./configure \
    "--with-mpm=prefork" \
    "--prefix=/etc/httpd/" \
    "--exec-prefix=/etc/httpd" \
    "--sysconfdir=/etc/httpd/conf" \
    "--sbindir=/usr/sbin" \
    "--bindir=/usr/bin" \
    "--datadir=/var/www" \
    "--mandir=/etc/httpd/man" \
    "--libdir=/etc/httpd/lib" \
    "--libexecdir=/etc/httpd/modules" \
    "--includedir=/etc/httpd/include" \
    "--enable-auth" \
    "--enable-cgid" \
    "--enable-cgi" \
    "--enable-dav" \
    "--enable-dav-fs" \
    "--enable-dav-lock" \
    "--enable-deflate" \
    "--enable-dir" \
    "--enable-expires" \
    "--enable-headers" \
    "--enable-log-config" \
    "--enable-mime" \
    "--enable-mime-magic" \
    "--enable-mods-shared=most" \
    "--enable-reqtimeout" \
    "--enable-rewrite" \
    "--enable-so" \
    "--enable-ssl" \
    "--enable-suexec" \
    "--enable-status" \
    "--enable-static-support" \
    "--enable-unique-id" \
    "--enable-static-htpasswd" \
    "--enable-static-rotatelogs" \
    "--disable-asis" \
    "--disable-auth-anon" \
    "--disable-auth-dbm" \
    "--disable-auth-digest" \
    "--disable-autoindex" \
    "--disable-cache" \
    "--disable-case-filter" \
    "--disable-case-filter-in" \
    "--disable-cern-meta" \
    "--disable-disk-cache" \
    "--disable-ext-filter" \
    "--disable-file-cache" \
    "--disable-filter" \
    "--disable-imap" \
    "--disable-info" \
    "--disable-mem-cache" \
    "--disable-proxy" \
    "--disable-proxy-connect" \
    "--disable-proxy-ftp" \
    "--disable-proxy-http" \
    "--disable-speling" \
    "--disable-usertrack" \
    "--disable-version" \
    "--with-included-apr" \
    "--with-pcre=/usr/lib/bin/pcre-config" \
    "--with-ssl=/usr/local/src/openssl-1.0.2m" \
    "--enable-ssl-staticlib-deps" \
    "--enable-mods-static=ssl"
    sudo make && sudo make install

Cấu hình trên là dành cho ai muốn cài đặt apache trên CentOs giống với khi cài đặt **yum**. Còn nếu muốn sự dụng đường dẫn mặc định khi cài trên ubuntu hoặc debian bằng **apt-get** thì chỉnh lại các thông số sau.
    
    
    ./configure \
    "--with-mpm=prefork" \
    "--prefix=/usr/local/apache/" \
    "--exec-prefix=/usr/local/apache/" \
    "--sysconfdir=/usr/local/apache/conf" \
    "--sbindir=/usr/local/apache/bin" \
    "--bindir=/usr/local/apache/bin" \
    "--datadir=/var/www" \
    "--mandir=/usr/local/apache/man" \
    "--libdir=/usr/local/apache/lib" \
    "--libexecdir=/usr/local/apache/modules" \
    "--includedir=/usr/local/apache/include" \
    ....

Sau khi cài đặt hoàn tất thì ta kiểm tra bằng lệnh sau để kiểm tra việc hoạt động của apache
    
    
    sudo /usr/sbin/apachectl -k start

Bởi vì do build bằng source nên không có các file script đặt trong **/etc/init.d/** ta cần tạo file script. Với **Centos 7** có hỗ trợ systemd thì có thể tạo 1 file **httpd.service** với nội dung sau:
    
    
    sudo vi /etc/systemd/system/httpd.service

Thêm nội dung sau vào:
    
    
    [Unit]
    Description=The Apache HTTP Server
    After=network.target
    [Service]
    Type=forking
    ExecStart=/usr/sbin/apachectl -k start
    ExecReload=/usr/sbin/apachectl -k graceful
    ExecStop=/usr/sbin/apachectl -k graceful-stop
    PIDFile=/etc/httpd/logs/httpd.pid
    PrivateTmp=true
    [Install]
    WantedBy=multi-user.target

Sau đó chạy lệnh sau khởi động lại daemon systemctl để nạp lại cấu hình có hiệu lực
    
    
    sudo systemctl daemon-reload
    sudo systemctl start httpd

Lưu ý: Đối với CentOs 6 ta có thể cp file apachectl vào /etc/init.d/ và đổi tên lại thành httpd
    
    
    sudo cp /usr/sbin/apachectl /etc/init.d/httpd

Sau đó chạy khởi động dịch vụ để kiểm tra
    
    
    sudo service httpd start

  **Tham khảo:** <https://en.wikipedia.org/wiki/List_of_Apache_modules> <https://bugs.debian.org/cgi-bin/bugreport.cgi?bug=504132#25> <https://httpd.apache.org/docs/2.4/en/> <http://www.gocit.vn/bai-viet/lab-linux-cau-hinh-webserver-apache/> <https://nguyenngoclyna.wordpress.com/2014/09/27/tim-hieu-ve-apache-http-server/> <https://blacksaildivision.com/how-to-install-apache-httpd-on-centos> <https://crosp.net/blog/administration/install-latest-apache-server-centos-7/> <http://httpd.apache.org/docs/2.2/en/vhosts/examples.html>
