---
title: "Hướng dẫn cài đặt và cấu hình Memcached trên CentOS"
date: 2018-07-13 13:52:38
categories: [Linux]
---

**Memcached là gì** Memcached là một hệ thống dùng để phân phối lưu trữ bản sao các đối tượng và dữ liệu được truy cập nhiều lần trên RAM để tăng tốc độ truy xuất. Nó là mã nguồn mở và miễn phí được dùng chủ yếu để làm bộ nhớ đệm và nhằm tăng tốc xử lý cho ứng dụng web. **Cách thức hoạt động** ![](https://cloudcraft.info/wp-content/uploads/2018/07/huong-dan-cai-dat-va-cau-hinh-memcached-tren-centos-1.jpg) Mô hình hoạt động cơ bản của memcached: Ở những yêu cầu đầu tiên được gửi tới server, server sẽ xử lý tính toán và truy vấn xuống database để lấy kết quả và trả về cho user kèm thêm một việc là trả dữ liệu để ghi vào server memcached. Những yêu cầu lặp lại lần sau, server sẽ không tự xử lý nữa mà chuyển sang server memcached để lấy kết quả trả về cho user. Điều này giúp giảm thiểu một lượng công việc lớn mà server phải xử lý. **Về ưu điểm** Memcached sử dụng RAM làm nơi lưu trữ dữ liệu nên có tốc độ truy xuất rất nhanh Memcached có thể làm nơi lưu trữ chia sẽ, thường lưu session rất tiện lợi cho mô hình load balancing, không cần phải lo về các vấn đề xoay quanh persistence session. Có thể dùng memcached để giảm thiểu truy vấn từ databases, dành cho đối với những dữ liệu ít thay đổi và cần tính toán, truy vấn phức tạp tốn tài nguyên Sử dụng cấu trúc lưu trữ đơn giản map giữa key => value nên dễ sử dụng và truy vấn. **Về nhược điểm** Memcached không có cơ chế thẩm định tính chính xác dữ liệu lưu trong nó, cấu trúc của memcached không có bất cứ sự liên hệ nào tới DB mà nó nằm độc lập. Vì vậy mà chưa có cơ chế đồng bộ với dữ liệu DB khi có sự thay đổi. Do sử dụng RAM để lưu trữ nên cần lượng lớn RAM cho server memcached, chi phí khá cao Nếu RAM server không đủ thì có thể gây tác dụng ngược về hiệu năng. Do chỉ lưu trữ trên RAM nên nếu dịch vụ memcached hoặc server có sự cố shutdown đột ngột hoặc restart để bảo trì ..., thì bộ nhớ giải phóng và dữ liệu không còn. **Cài đặt Memcached** Nếu dùng Centos 6 hoặc 7 thì chỉ cần add repo và các thư viện của redhat xong tiến hành yum install 
    
    
    sudo yum install -y epel-release
    sudo yum install -y memcached

Đối với Centos 5 thì làm như sau 
    
    
    sudo rpm -Uvh http://dl.fedoraproject.org/pub/epel/5/i386/epel-release-5-4.noarch.rpm
    sudo rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-5.rpm
    sudo yum --enablerepo=remi install memcached -y

Cấu hình cho dịch vụ chạy cùng khi khởi động server 
    
    
    sudo chkconfig memcached on

Khởi chạy dịch vụ memcached 
    
    
    sudo service memcached start

**Cài đặt memcached extension cho php** Đầu tiên để sử dụng memcached và php cần cài đặt gói thư viện libmemcached. Tải gói dịch vụ về, giải nén và tiến hành cài đặt 
    
    
    wget https://launchpad.net/libmemcached/1.0/1.0.18/+download/libmemcached-1.0.18.tar.gz
    tar -xvf libmemcached-1.0.18.tar.gz
    cd libmemcached-1.0.18
    sudo ./configure
    sudo make –j2 && sudo make install

Tải gói dịch vụ memcached dành cho php về giải nén và tiến hành cài đặt 
    
    
    wget https://pecl.php.net/get/memcached-2.2.0.tgz
    tar xf memcached-2.2.0.tgz
    cd memcached-2.2.0

Chuẩn bị môi trường cho việc build extionsion php 
    
    
    phpize

Tiến hành configure với đường dẫn tới file php-config và tắt đi memcached-sasl. Ngoài ra trong quá trình configure nếu có lỗi thì làm theo chỉ dẫn được xuất kèm thông báo lỗi 
    
    
    sudo ./configure \
    --with-php-config=/usr/local/php/bin/php-config \
    --disable-memcached-sasl
    
    sudo make –j2 && sudo make install

Sau khi cài đặt extension xong thêm dòng sau vào file php.ini 
    
    
    extension=memcached.so

Theo cấu hình mặc định thì memcached khi chạy sẽ lắng nghe trên port 11211 và có một số cấu hình khác mặc định. Tuy nhiên hiện nay có lỗ hỏng trên memcached nên bị hacker lợi dụng giao thức upd và cổng mặc định của memcached để khuếch đại cuộc tấn công DDOS, ngoài ra với cấu hình mặc định thì memcached không thể hoạt động đúng công suất vì vậy ta cần chỉnh sửa cấu hình trong file _**/etc/sysconfig/memcached**_ nếu ta muốn thay đổi cấu hình. 
    
    
    PORT="11211"
    USER="memcached"
    MAXCONN="10240"
    CACHESIZE="128"
    OPTIONS=""

Với tham số OPTIONS là đưa những tùy chọn khác như xác định xem memcached nghe trên IP nào, có dùng UDP hay không. Tham khảo thêm về cách cấu hình tại [link](https://www.jamf.com/jamf-nation/articles/428/memcached-installation-and-configuration-for-clustered-jamf-pro-environments) Tham khảo về tham số cấu hình dành cho mục OPTIONS [link](https://github.com/memcached/memcached/wiki/ConfiguringServer) Ngoài ra có thể sử dụng lệnh memcached –h để xem các tùy chọn hỗ trợ. **Tham khảo:** <https://www.jamf.com/jamf-nation/articles/428/memcached-installation-and-configuration-for-clustered-jamf-pro-environments> <https://github.com/memcached/memcached/wiki> <https://www.cloudways.com/blog/memcached-with-php/> <https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-memcached-on-ubuntu-16-04> <https://www.liquidweb.com/kb/how-to-install-memcached-on-centos-7/>
