---
title: "Hướng dẫn cấu hình chạy mô hình LAMP stack trên Directadmin"
date: 2017-12-28 06:00:29
categories: [Linux, Panel Hosting]
---

Trên Directadmin (tạm gọi tắt là **DA**) có cung cấp 3 mô hình chạy webserver thường gặp đó là: \+ Apache, mysql, php (LAMP stack) \+ Nginx, mysql, php (LEMP stack) \+ Apache + Nginx làm reverse proxy, mysql, php Bài này sẽ hướng dẫn cách build mô hình LAMP stack để chạy webserver trên DA. Trên DA, các file chứa thông số config chính nằm ở đường dẫn "**/usr/local/directadmin/custombuild** ". Bài này chỉ tập trung xoay quanh việc chỉnh sửa cấu hình trong file **options.conf** Đầu tiên các bạn vào thư mục **custombuild** của DA bằng lệnh: 
    
    
    cd /usr/local/directadmin/custombuild

Tại đây các bạn set giá trị lần lượt cho script build như sau 
    
    
    ./build set webserver apache
    ./build set mod_ruid2 yes

Ngoài ra, thay vì chỉnh gián tiếp bằng các lệnh trên chúng ta cũng có thể chỉnh sửa trực tiếp vào file **options.conf** như sau: \+ Mục **webserver** chỉnh thành **apache**
    
    
    #WEB Server Settings
    webserver=apache

\+ Mục **mod_ruid2** chỉnh thành **yes**
    
    
    mod_ruid2=yes

Sau khi tinh chỉnh xong, chúng ta chạy các lệnh sau để thực hiện build lại config 
    
    
    ./build update
    ./build apache
    ./build mod_ruid2
    ./build rewrite_confs
