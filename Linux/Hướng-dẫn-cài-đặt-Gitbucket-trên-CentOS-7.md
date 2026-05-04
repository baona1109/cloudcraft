---
title: "Hướng dẫn cài đặt Gitbucket trên CentOS 7"
date: 2018-09-24 10:33:59
categories: [Linux]
---

Gitbucket là một web platform Git mã nguồn mở, được cung cấp bởi Scala, mang tới cho người dùng những tiện ích sau: _ Cài đặt dễ dàng _ Giao diện trực quan, sinh động, _ Khả năng mở rộng linh hoạt nhờ vào các plugins _ Tương thích API với Github Gitbucket cung cấp nhiều tính năng như: _ Tạo repository public và private (cho phép truy cập qua http/https và ssh) _ Hỗ trợ GitLFS (Git Large File Storage) _ Cho phép xem và chỉnh sửa repository online _ Issues, Pull request và wiki cho các repository _ Lịch sử hoạt động và tính năng thông báo qua email ... Ở bài viết này, tụi mình sẽ hướng dẫn các bạn cài đặt Gitbucket trên HĐH CentOS 7 bằng các bước cực kì đơn giản. Server/VPS được sử dụng trong bài sử dụng HĐH CentOS 7, đã thực hiện update và cài đặt một số công cụ cơ bản. Các bước thực hiện như sau: **1) Cài đặt java:**
    
    
    yum install java -y

Mặc định, CentOS 7 sẽ cài đặt java-1.8.0-openjdk **2) Tải file java của Gitbucket:**
    
    
    wget https://github.com/gitbucket/gitbucket/releases/download/4.28.0/gitbucket.war

Phiên bản mới nhất tại thời điểm viết bài này là 4.28, các bạn có thể tải những bản cũ hơn hoặc cập nhật lên bản cao hơn tại đường dẫn sau: <https://github.com/gitbucket/gitbucket/releases> Thao tác upate lên phiên bản cao hơn mình sẽ đề cập ở đoạn sau :D **3) Start gitbucket** Ơ, thế không cài đặt gì à? Thực chất đây là một ứng dụng java, và đã được compile, đóng gói thành file gitbucket.war tải ở bước trên. Công việc của ta chỉ là chạy một lệnh đơn giản mà thôi <3``
    
    
    java -jar gitbucket.war

``Mặc định, Gitbucket sẽ chạy ở port 8080, ta có thể truy cập giao diện web bằng địa chỉ: http://<địa chỉ IP>:8080 Tài khoản ban đầu là username: **root** / password: **root** (nhớ đổi lại ngay khi đăng nhập lần đầu nhé) ![](https://cloudcraft.info/wp-content/uploads/2018/09/huong-dan-cai-dat-gitbucket-tren-centos-7-1.png) Một số option đi kèm: `--port=[NUMBER]` `--prefix=[CONTEXTPATH]` `--host=[HOSTNAME]` `--gitbucket.home=[DATA_DIR]` `--temp_dir=[TEMP_DIR]` `--max_file_size=[MAX_FILE_SIZE]` Để chạy ứng dụng tự động mỗi khi hệ thống khởi động, hay chạy dưới dạng một dịch vụ, các bạn có thể thực hiện tạo cron, viết script rồi bỏ vào rc.local, hoặc rất nhiều cách khác, phần này các bạn có thể tự tùy biến nhé ;) **4) Sử dụng nginx làm reverse proxy** Về cơ bản, sau khi hoàn thành bước 3 ở trên là bạn đã có thể sử dụng Gitbucket được rồi, nhưng để tận dụng khả năng tùy biến HTTP cũng như tăng độ bảo mật bằng SSL, các bạn có thể cài đặt nginx để làm reverse proxy. 
    
    
    yum install nginx -y

Tạo file /etc/nginx/config.d/gitbucket.conf, thêm vào nội dung sau: 
    
    
    server {
        listen 80;
        server_name gitbucket.cloudcraft.info www.gitbucket.cloudcraft.info;
        access_log /var/log/nginx/gitbucket;
        error_log /var/log/nginx/gitbucket-error warn;
        location / {
            proxy_pass http://127.0.0.1:8080;
        }
    }

Sau đó start nginx và Gitbucket. Từ giờ, bạn có thể truy cập thông qua tên miền. ![](https://cloudcraft.info/wp-content/uploads/2018/09/huong-dan-cai-dat-gitbucket-tren-centos-7-2.png) **5) Update Gitbucket** Để update lên bản cao hơn, bạn chỉ cần làm một bước cực kì đơn giản đó là lên trang download và tải file gitbucket.war mới nhất về thay thế cho file cũ là xong :D Bài hướng dẫn tới đây là kết thúc, chúc các bạn cài đặt thành công. Nếu có bất kỳ vấn đề nào cần được giải đáp, đừng ngại ngùng mà để lại một comment hoặc liên hệ với tụi mình để được giúp đỡ nhé <3
