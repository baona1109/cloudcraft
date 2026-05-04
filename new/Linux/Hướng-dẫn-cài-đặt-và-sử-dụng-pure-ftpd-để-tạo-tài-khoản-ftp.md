---
title: "Hướng dẫn cài đặt và sử dụng pure-ftpd để tạo tài khoản ftp"
date: 2017-11-30 15:13:55
categories: [Linux]
---

Bài viết này nhằm mục đích hướng dẫn việc cài đặt và sử dụng pure-ftpd để tạo và quản lý dịch vụ ftp trên máy chủ. Đầu tiên chúng ta thực hiện cài pure-ftpd từ epel với lệnh sau
    
    
    yum install epel-release
    yum --enablerepo=epel -y install pure-ftpd

![](https://cloudcraft.info/wp-content/uploads/2017/11/huong-dan-cai-da…ao-tai-khoan-ftp-1.png) Sau khi cài đặt xong chúng ta cần phải chỉnh một vài thông số trong file config của pure-ftpd với lệnh
    
    
    vi /etc/pure-ftpd/pure-ftpd.conf

**Chỉnh các thông số như sau:** Cho phép các user ftp sử dụng toàn quyền trong thư mục gốc mà user đó được phân quyền, hạn chế không cho những tài khoản này hoạt động bên ngoài thư mục gốc được phân quyền
    
    
    ChrootEveryone yes

![](https://cloudcraft.info/wp-content/uploads/2017/11/huong-dan-cai-da…ao-tai-khoan-ftp-2.png) Chỉ cho phép truy cập bằng user đã được cấp quyền
    
    
    NoAnonymous yes

![](https://cloudcraft.info/wp-content/uploads/2017/11/huong-dan-cai-da…ao-tai-khoan-ftp-3.png) Chỉ định đường dẫn nơi chứa file dữ liệu của các tài khoản ftp
    
    
    # PureDB user database (see README.Virtual-Users)
    PureDB /etc/pure-ftpd/pureftpd.pdb

![](https://cloudcraft.info/wp-content/uploads/2017/11/huong-dan-cai-da…ao-tai-khoan-ftp-4.png) Tự động tạo thư mục home cho user nếu như chưa có thư mục này
    
    
    # Automatically create home directories if they are missing
    CreateHomeDir yes

![](https://cloudcraft.info/wp-content/uploads/2017/11/huong-dan-cai-da…ao-tai-khoan-ftp-5.png) Sau khi cấu hình file config xong thực hiện cập nhật database vào file *.pdb mà ta đã quy định ở file config
    
    
    pure-pw mkdb

![](https://cloudcraft.info/wp-content/uploads/2017/11/huong-dan-cai-da…ao-tai-khoan-ftp-6.png) Thực hiện tạo user ftp với lệnh sau:
    
    
    groupadd testftp
    useradd testftp -g testftp -d/home/testftp -m
    pure-pw useradd test -u testftp -g testftp -d /home/testftp/

Với các thông số:

  * -u testftp: Đây là user được tạo trên hệ thống linux
  * -g testftp: Là group của user trên
  * test: Đây là user ftp được tạo thuộc sở hữu của user testftp
  * -d: Là tham số cho đường dẫn tới thư mục home dành cho user test

![](https://cloudcraft.info/wp-content/uploads/2017/11/huong-dan-cai-da…ao-tai-khoan-ftp-7.png) Kiểm tra thông số user FTP vừa tạo bằng lệnh: 
    
    
    pure-pw show test
