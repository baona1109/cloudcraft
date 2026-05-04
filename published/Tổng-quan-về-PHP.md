---
title: "Tổng quan về PHP"
date: 2018-11-16 14:23:48
categories: [Linux]
---

# **Giới thiệu về PHP**

Bài viết này mình sẽ giới thiệu tổng quan về PHP cũng như các tham số cấu hình thường dùng trong PHP

PHP (tiền thân là Personal Home Page sau được viết tắt dành cho _PHP: Hypertext Preprocessor_) là một ngôn ngữ lập trình kịch bản (scripting language) mã nguồn mở được dùng phổ biến để tạo các ứng dụng web chạy trên máy chủ. Nó được sử dụng để quản lý nội dung động, Database, Session tracking,... PHP hỗ trợ nhiều giao thức lớn như POP3, IMAP, LDAP,... cú pháp của nó khá giống C do được phát triển từ ngôn ngữ lập trình C.

Một số công dụng của PHP

  * PHP có thể xử lý các dữ liệu từ form, có thể thu thập dữ liệu người dùng thao tác và gửi về server để xử lý.
  * PHP tích hợp các hàm thư viện giúp người dùng thao tác lên database của họ thông qua PHP.
  * Truy cập được các biến cookie và thiết lập cookie.
  * Có thể tùy chỉnh phân quyền truy cập các trang bên trong website.
  * Nó có thể mã hóa dữ liệu.



PHP có những đặc trưng sau giúp nó trở thành một ngôn ngữ tiện dụng: Đơn giản hóa, hiệu quả, bảo mật cao, linh động, thân thiện. Hiện nay nhiều framework ra đời được phát triển dựa trên PHP để đơn giản hóa hơn trong việc lập trình như: Zend, CodeIgniter, Laravel... đa phần theo mô hình MVC thuận tiện cho việc phát triển phần mềm

Các bạn có thể tham khảo cách cài đặt PHP thông qua việc compile từ source: <https://cloudcraft.info/huong-dan-cai-dat-php-tu-source/>

## **Cấu hình file php.ini**

File php.ini là file cấu hình của php, mọi cấu hình cho sự hoạt động của php đều được chỉnh trong file này. Có khá là nhiều thông số trong file này nhưng dưới đây là một số thông số thường hay gặp khi cấu hình.

**Tham số** | **Ý nghĩa**  
---|---  
_**short_open_tag**_ | Nếu bật cờ này thì php sẽ sử dụng form ngắn **<? ?>**. Tuy nhiên nếu muốn sử dụng xml kết hợp php thì nên tắt tính năng này để có thể dùng các tag của xml như **<?xml ?>**. Nếu tắt chế độ này thì nên dùng tag **<?php ?>**  
_**disable_functions**_ | Vô hiệu hóa các hàm php để tăng tính bảo mật cho hệ thống, chẳng hạn chặn người dùng không cho truy cập hàm phpinfo() để khai thác thông tin của server hosting  
_**max_execution_time**_ | Thời gian tối đa mà một script được thực thi khi có yêu cầu. Khi script chạy quá thời gian này sẽ báo lỗi “Fatal error: Maximum execution time of {giây} seconds” và bị timeout. Nếu script ta viết cần nhiều thời gian để thực thi thì có thể tăng giá trị này lên. Tính bằng giây  
_**memory_limit**_ | Giới hạn bộ nhớ của hệ thống được dùng để xử lý các script trong php. Tuy nhiên với các mã nguồn lớn, phục vụ nhu cầu cao thì nên tăng giá trị này lên. Giá trị mặc định 128MB  
_**upload_max_filesize**_ | Giới hạn dung lượng tối đa mỗi tập tin khi upload. Tham số này thường đi kèm với post_max_size  
_**file_upload**_ | Bật tắt tính năng upload file lên hệ thống bằng php.  
_**display_errors**_ | Tùy chọn hiển thị lỗi ra ngoài website. Thường dùng cho việc debug.  
 

Ngoài các tham số trên thì còn rất nhiều tham số khác trong **php.ini**.

Một số tham số hỗ trợ cho module **OpCache** như: _opcache.enable, opcache.memory_consumption, opcache.interned_strings_buffer...._ Hoặc các tham số liên quan **MySQL** như: _mysql.default_host, mysql.default_user, mysql.default_password..._

## **Cấu hình nginx sử dụng php-fpm**

Để đối ứng với các file config trong thư mục fpm.d đã nói bên trên phần cấu hình php-fpm. Ta cần phải cấu hình các virtual host trên nginx tương ứng để có thể sử dụng. Ta thêm các dòng sau vào trong file **nginx.conf** (nếu tách riêng ra file vhost thì nên include vào file này lại)
    
    
    server {
                listen       80;
                server_name  dangtgh.ga;
    
                # note that these lines are originally from the "location /" block
                root   /var/www/dangtgh_ga/public_html/;
                index index.php index.html index.htm;
    
                location / {
                            try_files $uri $uri/ =404;
                }
                error_page 404 /404.html;
                error_page 500 502 503 504 /50x.html;
    
                location ~ \.php$ {
                            try_files $uri =404;
                            fastcgi_pass unix:/usr/local/php/var/run/dangtgh_ga-fpm.sock;
                            fastcgi_index index.php;
                            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                            include fastcgi_params;
                }
    }

Ngoài ra cần phân quyền group sở hữu cho thư mục cài đặt của php thành nginx với lệnh để nginx có thể chạy lấy các file config như socket của fpm, file log,... 
    
    
    chgrp –R nginx /usr/local/php

  **Tham khảo** <http://php.net/manual/en/ini.core.php#ini.short-open-tag> <http://vietjack.com/php/>
