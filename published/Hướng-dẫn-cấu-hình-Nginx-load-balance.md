---
title: "Hướng dẫn cấu hình Nginx load balance"
date: 2018-10-04 07:37:05
categories: [Linux]
---

Load balancing là giải pháp phân phối tải cho các ứng dụng bằng cách tối ưu hóa tài nguyên hệ thống, tăng tối đa thông lượng mạng, giảm độ trễ và điều chỉnh một số cấu hình chịu lỗi cho hệ thống. Trong bài viết này, mình sẽ giới thiệu đến các bạn cách dùng Nginx để cân bằng tải cho các truy cập HTTP nhằm nâng cao hiệu năng, khả năng mở rộng và độ ổn định của các ứng dụng. 

## **Một số thuật toán cân bằng tải**

Trước tiên, ta cần phải tìm hiểu về các thuật toán cân bằng tải mà Nginx sử dụng: 

  * **_Round-robin_** : các request được gởi tới sẽ được chia đều cho các server.
  * **_Least-connected_** : request kế tiếp sẽ được gởi tới cho server có số lượng kết nối ít nhất (thường cũng là server nhẹ tải nhất)
  * **_IP-hash_** (chỉ dùng cho HTTP): thuật toán hash sẽ dựa trên 3 octer đầu tiên trên IP của client để quyết định xem client này sẽ được map cố định với server nào.Thích hợp với những ứng dụng cần giữ lại session của người dùng. 
    * Khi ta thêm hoặc bớt một server trong pool thì các giá trị hash này sẽ được phân bố lại (mất session). Ta có thể dùng thêm một tham số bổ trợ là consistent để giảm thiểu ảnh hưởng của khi phân phối lại các giá trị hash.

  ![](https://cloudcraft.info/wp-content/uploads/2018/07/huong-dan-cau-hinh-nginx-load-balance-1.jpg)

_Load balancing_

Ngoài 3 thuật toán cơ bản trên, ta còn thể dùng thêm trọng số (weight) cho các server để nginx ưu tiên lựa chọn những server này hơn. Ví dụ như ta có 3 server chạy round-robin với trọng số lần lượt là 3 – 1 – 1 thì server đầu tiên sẽ được nhận 60% trên tổng số request mà cả 3 server này cùng nhận. Một số chỉ thị về load balance trong file config của nginx:  **Module** | **Công dụng**  
---|---  
_ip_hash_ | Lệnh này sẽ điều khiển nginx phân tán tải cho các upstream server bằng cách hash 3 octet đầu tiên của IP client.  
_least_conn_ | Ưu tiên phân tải cho upstream server nào có ít kết nối đang hoạt động nhất.  
_server_ | Mỗi lệnh này tương ứng với 1 upstream server/socket. Một số thông số đi kèm gồm: 

  * **_weight_** : trọng số ưu tiên của server này.
  * **_max_fails_** : Số lần tối đa mà load balancer không liên lạc được với server này (trong khoảng **_fail_timeout_**) trước khi server này bị coi là down.
  * **_fail_timeout_** : khoảng thời gian mà một server phải trả lời load balancer, nếu không trả lời thì server này sẽ bị coi là down. Đây cũng là thời gian downtime của server này.
  * **_backup_** : những server nào có thông số này sẽ chỉ nhận request từ load balancer một khi tất cả các server khác đều bị **_down_**.
  * **_down_** : chỉ thị này cho biết server này hiện không thể xử lý các request được gởi tới. Load balancer vẫn lưu server này trong danh sách nhưng sẽ không phân tải cho server này cho đến khi chỉ thị này được gỡ bỏ.

  
  
## Cấu hình Nginx load balance

Để cấu hình hoàn chỉnh load balance cho nginx ta cần phải cấu hình block upstream tương ứng với những server backend cần trỏ tới. Sau đó, ta phải set proxy_pass của đường dẫn tới block upstream tương ứng. Ví dụ về một **block upstream** trong nginx sử dụng thuật toán **round robin** (nginx sẽ mặc định sử dụng thuật toán này nếu như không chọn một thuật toán) 
    
    
    upstream backend_api_1 {
     server 10.10.10.11:8080 max_fails=2 fail_timeout=10s; 
     server 10.10.10.12:8080 max_fails=2 fail_timeout=10s; 
     server 10.10.10.13:8080 max_fails=2 fail_timeout=10s;
    }

Ở đây nginx sẽ phân tải đến 3 server ở phía sau. Mỗi server sẽ có _fail _timeout_ là 10s, nếu trong 10s này mà nginx không thể liên lạc được với server này từ 3 lần trở đi thì server đó sẽ bị coi là down. Một ví dụ khác về **block upsteam** sử dụng thuật toán**least_conn** , chọn lựa upstream server nào hiện đang có ít kết nối nhất 
    
    
    upstream backend_api_2 {
      least_conn;
      server 10.10.10.11:80 max_fails=2 fail_timeout=10s;
      server 10.10.10.12:80 max_fails=2 fail_timeout=10s;
      server 10.10.10.13:80 max_fails=2 fail_timeout=10s;
    }
    

  Vậy là ta đã xong phần tạo upsteam block, giờ để người dùng có thể truy cập tới các upstream block này, ta cần phải set **proxy_pass** trong **block http** trỏ tới block upstream cần dùng 
    
    
    http
    {
    ....   
       location /api1 {
            proxy_set_header X-Real-IP  $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header Host $host;
            proxy_pass http://backend_api_1;
       }
    
       location /api2 {
            proxy_set_header X-Real-IP  $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header Host $host;
            proxy_pass http://backend_api_2;
       }
    }

Theo cấu hình này, khi người dùng truy cập vào đường dẫn abc.com/api1 thì sẽ được trỏ tới block upsteam là backend-api-1. Tương tự như vậy, khi người dùng vào đường dẫn /api2 thì sẽ được trỏ tới upstream backend-api-2. 

# Tham khảo

<http://dgtool.treitos.com/2013/02/nginx-as-sticky-balancer-for-ha-using.html> <https://www.nginx.com/resources/admin-guide/load-balancer/> <http://nginx.org/en/docs/http/ngx_http_upstream_module.html> <https://www.digitalocean.com/community/tutorials/how-to-set-up-nginx-load-balancing> <https://www.digitalocean.com/community/tutorials/understanding-nginx-http-proxying-load-balancing-buffering-and-caching> <http://nginx.org/en/docs/http/ngx_http_proxy_module.html> <https://www.nginx.com/blog/tuning-nginx/>
