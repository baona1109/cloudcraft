---
title: "Hướng dẫn tạo chứng thực cơ bản nginx - http basic authentication"
date: 2019-02-27 13:50:34
categories: [Linux, Security]
---

Bạn đang chạy website wordpress nhưng muốn bảo vệ trang admin, đường dẫn URI bất kỳ hay đang trong quá trình cài đặt mà không muốn ai can thiệp. Thì "http basic authentication" là một trong các giải pháp cơ bản để giới hạn quyền truy cập.

**\--- Chuẩn bị ---**

Web server: **nginx** và **htpasswd**

Tham khảo cài đặt Nginx trên

  * Ubuntu: https://www.digitalocean.com/community/tutorials/how-to-install-nginx-on-ubuntu-16-04
  * CentOS: https://www.digitalocean.com/community/tutorials/how-to-install-nginx-on-centos-7



Để cài đặt htpasswd: Tiến hành cài gói apache2-utils (Debian, Ubuntu) hoặc httpd-tools (CentOS)

Nếu gặp khó khăn khi cài đặt đặt httpd-tools trên CentOS 6 thì thực hiện như sau:
    
    
    yum install apr apr-util
    rpm -Uvh https://rpmfind.net/linux/centos/6.10/os/x86_64/Packages/httpd-tools-2.2.15-69.el6.centos.x86_64.rpm

**\--- Cài đặt chứng thực ---**

  1. Tạo thư mục lưu chứng thực, ở đây tôi dùng thư mục /etc/nginx
  2. Thêm một thông tin chứng thực và sử dụng **flag: -c** nếu bạn chưa có tập tin /etc/nginx/.htpasswd


    
    
    htpasswd -c /etc/nginx/.htpasswd admin

Sẽ có prompt chờ bạn nhập thông tin password cho user: admin

3\. Nếu đã tạo tập tin /etc/nginx/.htpasswd, KHÔNG sử dụng flag: -c
    
    
    htpasswd /etc/nginx/.htpasswd admin

Nếu user đã tồn tại, thì lệnh trên tương ứng với cập nhật password.

4\. Tôi đang sử dụng httpd-tools trên CentOS 6, nên mặc định là "CRYPT encryption"

Mẫu password sau khi mã hóa lưu trong /etc/nginx/.htpasswd như sau:
    
    
    [root@host nginx]# cat /etc/nginx/.htpasswd
    admin1:LocBtasAIP0qY
    admin2:{SHA}cRDtpNCeBiql5KOQsKVyrA0sAiA=
    admin3:$apr1$Gncic8gQ$IWiLO2oZAnc9wZ46nt3yb1

  * admin1: CRYPT encryption : htpasswd -d /etc/nginx/.htpasswd admin1
  * admin2: SHA encryption : htpasswd -s /etc/nginx/.htpasswd admin2
  * admin3: MD5 encryption : htpasswd -m /etc/nginx/.htpasswd admin3



6\. Cấu hình location muốn chứng thực trên nginx

  * Ví dụ tôi muốn cấu hình chứng thực mọi URI bắt đầu với wp-admin/*


    
    
    location /wp-admin/ {
    auth_basic "Administrator’s Area";
    auth_basic_user_file /etc/nginx/.htpasswd;
    }

Nhớ reload nginx: nginx -s reload

  * Nếu bạn muốn cấu hình cho toàn trang domain.com/* nhưng chỉ tắt ở một số URI nhất định, thì đặt auth_basic trong block: server ngang hàng với các location. Sau đó đặt auth_basic off ở vị trí mong muốn.


    
    
    server {
    ...
    auth_basic "Administrator’s Area";
    auth_basic_user_file /etc/nginx/.htpasswd;
    location /bluegag/ {auth_basic off;}
    }

Hình minh họa truy cập URI đã được bảo vệ như sau:

![](https://cloudcraft.info/wp-content/uploads/2019/02/http-basic-authen_1.png)

Sau khi nhập đúng thông tin, trình duyệt sẽ cache thông tin chứng thực này đến khi bạn đóng toàn bộ tab.

Nguồn: https://docs.nginx.com/nginx/admin-guide/security-controls/configuring-http-basic-authentication/
