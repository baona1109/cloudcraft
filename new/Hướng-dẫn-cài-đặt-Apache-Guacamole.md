---
title: "Hướng dẫn cài đặt Apache Guacamole"
date: 2018-04-28 10:14:25
categories: [Linux]
---

Apache Guacamole là một chương trình remote desktop gateway được viết bằng ngôn ngữ Java và hỗ trợ bởi Tomcat. Chương trình này hỗ trợ ứng dụng web HTML5 để tạo môi trường desktop hiển thị kết nối đến các máy chủ từ xa sử dụng giao diện đồ họa (VNC, RDP) hay giao diện terminal (SSH Shell) ngay trên trình duyệt web cho phép bạn thao tác như các trình remote desktop client. Ở bài viết này, mình sẽ hướng dẫn các bạn cài đặt Apache Guacamole bằng script cực kỳ đơn giản. _ Clone script từ github của mình về bằng lệnh sau: 
    
    
    git clone https://github.com/cloudcraftteam/apache-guacamole.git

_ Di chuyển tới thư mục chứa script: 
    
    
    cd apache-guacamole/

_ Thêm quyền thực thi cho script: 
    
    
    chmod +x guacamole-install-script.sh

_ Chạy script: 
    
    
    ./guacamole-install-script.sh

_ Trong quá trình cài đặt, màn hình sẽ hiện ra một số tùy chọn như: database user / password, cài đặt nginx làm reverse proxy, cài đặt Let's Encrypt...(nếu để trống, chương trình sẽ được cài đặt theo thông tin mặc định được quy định trong script) ![](https://cloudcraft.info/wp-content/uploads/2018/04/huong-dan-cai-dat-apache-guacamole-1.png) _ Các bạn có thể tùy chỉnh các thông tin version trong script để cài đặt ứng dụng có phiên bản cao hơn Sau khi cài đặt hoàn tất, các bạn có thể truy cập thông qua trình duyệt bằng các địa chỉ như: 

**http:// <IP>/guacamole** hoặc **https:// <IP>/guacamole**

**http:// <domain>/guacamole** hoặc **https:// <domain>/guacamole** ![](https://cloudcraft.info/wp-content/uploads/2018/04/huong-dan-cai-dat-apache-guacamole-2.png) Một số hình ảnh giao diện sử dụng: ![](https://cloudcraft.info/wp-content/uploads/2018/04/huong-dan-cai-dat-apache-guacamole-3.png) ![](https://cloudcraft.info/wp-content/uploads/2018/04/huong-dan-cai-dat-apache-guacamole-4.png)
