---
title: "Hướng dẫn bật tính năng log trên haproxy"
date: 2017-12-12 08:51:49
categories: [Linux, Load Balancing]
---

Mặc định, haproxy sẽ không ghi nhận log của các truy cập kết nối tới server hay một gói tin sẽ tới backend nào. Để bật tính năng ghi log, ta mở file cấu hình của dịch vụ này tại **/etc/haproxy/haproxy.cfg** và thêm dòng sau (nếu chưa có) vào trường **global** : 
    
    
    log 127.0.0.1 local2

Dòng trên có ý nghĩa là haproxy sẽ gửi message tới rsyslog tại địa chỉ 127.0.0.1 nhưng mặc định rsyslog sẽ không lắng nghe trên bất kì địa chỉ nào cả, ta mở file **/etc/rsyslog.conf** và comment out các dòng sau: ![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-bat-tinh-nang-log-tren-haproxy-1.png) Sau đó ta tạo file **/etc/rsyslog.d/haproxy.conf** và thêm 2 dòng sau: ![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-bat-tinh-nang-log-tren-haproxy-2.png) Chức năng lần lượt của 2 dòng trên là để khai báo file log chứa _ thông tin địa chỉ IP gửi request tới và request sẽ tới backend nào _ thông tin trạng thái của dịch vụ và trạng thái các backend Cuối cùng, ta reload hoặc restart lại haproxy và rsyslog: 
    
    
    systemctl reload haproxy
    systemctl restart rsyslog

 
