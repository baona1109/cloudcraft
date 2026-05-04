---
title: "Hướng dẫn liệt kê danh sách domain của nginx"
date: 2017-12-26 08:23:15
categories: [Linux]
---

Chào các bạn, ở bài viết này, Cloudcraft sẽ hướng dẫn các bạn tận dụng lệnh **find** có sẵn của Linux để liệt kê danh sách toàn bộ các domain đang được khai báo trong nginx. Để thực hiện điều này, các bạn cần phải đảm bảo các file cấu hình virtual host đều được đặt trong cùng một đường dẫn và có cùng định dạng. Khi đó, các bạn thực hiện lệnh sau: 
    
    
    find /etc/nginx/vhosts -type f -name "*.conf" -print0 | xargs -0 egrep '^(\s|\t)*server_name' | sed -r 's/(.*server_name\s*|;)//g'

Trong đó: **/etc/nginx/vhosts** : là đường dẫn tới thư mục chưa các file cấu hình của vhost ***.conf** : định dạng chung của các file cấu hình vhost Kết quả: ![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-liet-ke-danh-sach-domain-cua-nginx-1-1.png)  
