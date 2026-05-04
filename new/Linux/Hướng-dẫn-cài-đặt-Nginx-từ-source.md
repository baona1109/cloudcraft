---
title: "Hướng dẫn cài đặt Nginx từ source"
date: 2018-03-23 16:35:27
categories: [Linux]
---

# **Giới thiệu**

Nginx là 1 ứng dụng để chạy máy chủ với vai trò là webserver hoặc cũng có thể đóng vai trò reverse proxy cho các giao thức HTTP, HTTPS, SMTP, POP3, IMAP, có thể đóng vai trò cân bằng tải (load balancer). Không giống như các loại ứng dụng chạy webserver truyền thống khác chẳng hạn như apache,... Nginx không dựa trên thread để xử lý yêu cầu mà thay vào đó nó sử dụng một kiến trúc theo hướng sự kiện (event-driven) bất đồng bộ. Nginx có hiệu suất cao và yêu cầu bộ nhớ thấp hơn so với Apache. Ngoài ra, bởi vì Nginx xử lý các yêu cầu theo hướng sự kiện bất đồng bộ không sử dụng thread như các webserver truyền thống khác nên nó cũng giải quyết được vấn đề C10K. C10K hiểu đơn giản thì do các máy chủ web truyền thống xử lý các yêu cầu dựa trên thread, mỗi khi có một yêu cầu mới thì máy chủ sẽ tạo ra 1 thread để xử lý cho yêu cầu đó. Số lượng yêu cầu càng nhiều thì số lượng thread xử lý càng tăng, điều này dẫn đến việc thiếu hụt tài nguyên để cấp cho các thread để xử lý.

## **Những tính năng của máy chủ HTTP Nginx**

Phục vụ cho tập tin tĩnh và lập chỉ mục cho tập tin Cân bằng tải đơn giản và khả năng chịu lỗi Cấu hình linh hoạt, lưu lại nhật ký truy vấn Hỗ trợ tăng tốc với bộ nhớ đệm của Fastcgi, scgi và các máy chủ memcached Hỗ trợ mã hóa TLS và SSL

# **Cài đặt nginx bằng biên dịch mã nguồn**

Trước tiên để thực hiện build một phần mềm hay ứng dụng nào trên centos thì cần phải kiểm tra và thực hiện cài đặt các công cụ hỗ trợ cho việc biên dịch mã nguồn.
    
    
    yum install epel-release -y
    yum groupinstall "Development Tools" -y

Thực hiện tải mã nguồn của Nginx tại <http://nginx.org/en/download.html> Phiên bản được sử dụng tại thời điểm hiện tại là 1.10.3, nếu muốn cài đặt các phiên bản khác hoặc mới hơn thì ta có thể vào link trên để lựa chọn.
    
    
    mkdir /home/dangtgh/nginx
    cd /home/dangtgh/nginx
    wget http://nginx.org/download/nginx-1.10.3.tar.gz
    tar -xzf nginx-1.10.3.tar.gz

Tải các bộ thư viện liên quan, thực hiện giải nén để tiến hành build cùng lúc với souce của Nginx
    
    
    wget https://ftp.pcre.org/pub/pcre/pcre-8.41.tar.gz
    wget https://www.openssl.org/source/old/1.0.2/openssl-1.0.2m.tar.gz
    tar -xzf pcre-8.41.tar.gz
    tar xvf openssl-1.0.2m.tar.gz

Tiến hành cài đặt Nginx
    
    
    cd nginx-1.10.3

Một vài tham số cơ bản khi cấu hình **\--user:** chỉ định user sẽ được dùng để chạy nginx, có thể chỉnh sửa trong file config của nginx **\--prefix:** Chỉ định đường dẫn tới nơi cài đặt của nginx **\--error-log-path:** Chỉ định đường dẫn mặc định cho error log **\--http-log-path** : Chỉ định đường dẫn mặc định cho nơi chứa file ghi log truy cập http Thực hiện cấu hình trước khi cài đặt cho Nginx. Dưới đây là một số tham số kham khảo, ngoài ra có thể tùy chỉnh các tham số khác có thể tìm thấy bằng việc dùng lệnh **./configure --help**
    
    
    sudo ./configure \
    --user=nginx \
    --prefix=/etc/nginx \
    --error-log-path=/etc/nginx/log/error.log \
    --http-log-path=/etc/nginx/log/access.log \
    --with-http_gzip_static_module \
    --with-http_ssl_module \
    --with-pcre=/home/dangtgh/nginx/pcre-8.41 \
    --with-openssl=/home/dangtgh/nginx/openssl-1.0.2m \
    --with-file-aio \
    --with-http_realip_module \
    --without-http_scgi_module \
    --without-http_uwsgi_module

Khi thực hiện config nếu có báo lỗi thư viện đang thiếu thì có thể cài đặt thêm bằng lệnh yum như sau
    
    
    yum install <namelib>-devel -y
    Ex: yum install zlib-devel -y

Tiến hành biên dịch và cài đặt
    
    
    sudo make && sudo make install

Như ở cấu hình trên ta dùng user nginx để chạy Nginx. Vì vậy ta cần tạo user và group nginx để có thể sử dụng
    
    
    sudo useradd --system --home /var/cache/nginx --shell /sbin/nologin --comment "nginx user" --user-group nginx

Do khi ta biên dịch bằng source thì chưa có file script dành cho việc start, restart, stop,... cho Nginx nằm trong **/etc/init.d/** nên ta cần tạo file script nginx tại **/etc/init.d/** và thêm đoạn script sau
    
    
    #!/bin/sh
    #
    # nginx - this script starts and stops the nginx daemin
    #
    # chkconfig:   - 85 15
    # description:  Nginx is an HTTP(S) server, HTTP(S) reverse \
    #               proxy and IMAP/POP3 proxy server
    # processname: nginx
    # config:      /etc/nginx/conf/nginx.conf
    # pidfile:     /etc/nginx/logs/nginx.pid
    # user:        johndoe
    
    # Source function library.
    . /etc/rc.d/init.d/functions
    
    # Source networking configuration.
    . /etc/sysconfig/network
    
    # Check that networking is up.
    [ "$NETWORKING" = "no" ] && exit 0
    
    nginx="/etc/nginx/sbin/nginx"
    prog=$(basename $nginx)
    
    NGINX_CONF_FILE="/etc/nginx/conf/nginx.conf"
    
    lockfile=/var/run/nginx.lock
    
    start() {
        [ -x $nginx ] || exit 5
        [ -f $NGINX_CONF_FILE ] || exit 6
        echo -n $"Starting $prog: "
        daemon $nginx -c $NGINX_CONF_FILE
        retval=$?
        echo
        [ $retval -eq 0 ] && touch $lockfile
        return $retval
    }
    
    stop() {
        echo -n $"Stopping $prog: "
        killproc $prog -QUIT
        retval=$?
        echo
        [ $retval -eq 0 ] && rm -f $lockfile
        return $retval
    }
    
    restart() {
        configtest || return $?
        stop
        start
    }
    
    reload() {
        configtest || return $?
        echo -n $"Reloading $prog: "
        killproc $nginx -HUP
        RETVAL=$?
        echo
    }
    
    force_reload() {
        restart
    }
    
    configtest() {
      $nginx -t -c $NGINX_CONF_FILE
    }
    
    rh_status() {
        status $prog
    }
    
    rh_status_q() {
        rh_status >/dev/null 2>&1
    }
    
    case "$1" in
        start)
            rh_status_q && exit 0
            $1
            ;;
        stop)
            rh_status_q || exit 0
            $1
            ;;
        restart|configtest)
            $1
            ;;
        reload)
            rh_status_q || exit 7
            $1
            ;;
        force-reload)
            force_reload
            ;;
        status)
            rh_status
            ;;
        condrestart|try-restart)
            rh_status_q || exit 0
                ;;
        *)
            echo $"Usage: $0 {start|stop|status|restart|condrestart|try-restart|reload|force-reload|configtest}"
            exit 2
    esac
    
    
    chmod +x /et c/init.d/nginx

Bật cho nginx có thể khởi động cùng lúc khi server bị reboot
    
    
    sudo chkconfig --add nginx
    sudo chkconfig --level 345 nginx on

Sau đó chạy thử nginx để kiểm tra
    
    
    service nginx restart

**Tham khảo:** <https://lcdung.top/nginx-va-apache/> <http://nginx.org/en/docs/http/ngx_http_core_module.html> <https://viblo.asia/p/tim-hieu-va-huong-dan-setup-web-server-nginx-OREGwBwlvlN> <https://www.vultr.com/docs/how-to-compile-nginx-from-source-on-centos-7> <https://viblo.asia/p/tim-hieu-va-huong-dan-setup-web-server-nginx-OREGwBwlvlN>
