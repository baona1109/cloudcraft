---
title: "Hướng dẫn reset password admin Grafana"
date: 2019-11-27 20:51:07
categories: [Linux, Monitoring, Grafana]
---

# Hướng dẫn reset password admin của Grafana

Chao xìn mọi người, trong quá trình quản lý Grafana, sẽ có những lúc bạn quên password admin của Grafana, cần phải reset lại pass. Trong bài viết này, mình sẽ hướng dẫn các bạn cách reset password admin của Grafana một cách nhanh chóng. ![grafana-reset-password-2](https://cloudcraft.info/wp-content/uploads/2019/11/grafana-reset-password-2-1024x568.png)

## Reset password bằng CLI

Để có thể reset password admin của Grafana, trước hết ta cần có quyền sudo trên server cài đặt Grafana, và sử dụng grafana-cli để reset password, với cú pháp lệnh như sau: 
    
    
    sudo grafana-cli admin reset-admin-password --homepath "/usr/share/grafana/" <new_password>

Trong đó _**\--homepath**_ là đường dẫn tới folder home của grafana và _**< new_password>**_ là password admin mới của bạn Ta truy cập vào server và gõ những lệnh sau: 
    
    
    sudo ln -s /var/lib/grafana /usr/share/grafana/data
    sudo ln -s /var/log/grafana /usr/share/grafana/data/logs
    sudo grafana-cli admin reset-admin-password --homepath "/usr/share/grafana/" mypassword123
    
    INFO[11-27|21:26:44] Connecting to DB                         logger=sqlstore dbtype=sqlite3
    INFO[11-27|21:26:44] Starting DB migration                    logger=migrator
    
    Admin password changed successfully ✔

Cần lưu ý là tính năng cập nhật password bằng CLI này chỉ có từ phiên bản Grafana 4.1 về sau, nếu các bạn xài phiên bản cũ hơn thì sẽ không được hỗ trợ. Sau đó, các bạn thử đăng nhập lại Grafana để kiểm tra xem Grafana đã nhận được password mới hay chưa nhé. 

## Thay đổi password trực tiếp trên sqlite3

Nếu thực hiện theo cách trên vẫn không hiệu quả, ta có thể thay đổi password trực tiếp trên sqlite3. Cách thức reset password trực tiếp trên sqlite3 như sau: 
    
    
    sudo sqlite3 /var/lib/grafana/grafana.db
    
    # Reset the admin password to “admin”
    
    sqlite> update user set password = '59acf18b94d7eb0694c61e60ce44c110c7a683ac6a8f09580d626f90f4a242000746579358d77dd9e570e83fa24faa88a8a6', salt = 'F3FAxVm33R' where login = 'admin';
    sqlite> .exit

Các bạn đăng nhập lại giao diện web Grafana để tận hưởng thành quả nhé ;). Hy vọng bài viết này giúp ích được nhiều cho các bạn ^^. 

## Đọc thêm

Các bạn có thể đọc thêm một số bài viết khác về monitor dịch vụ, services bằng Prometheus hoặc TICK stack tại đây 

  * [Hướng dẫn cài đặt Prometheus và Grafana để monitor dịch vụ](https://cloudcraft.info/huong-dan-setup-prometheus-grafana-de-monitor-dich-vu/)
  * [Hướng dẫn cài đặt và cấu hình TICK stack để monitor dịch vụ](https://cloudcraft.info/cai-dat-va-cau-hinh-tick-stack/)


