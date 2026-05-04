---
title: "Hướng dẫn cài đặt Teleport làm remote SSH"
date: 2018-01-01 14:05:25
categories: [Linux]
---

Xin chào tất cả các bạn. Hôm nay, Cloudcraft sẽ hướng dẫn các bạn cấu hình Teleport để làm Remote SSH thông qua HTTPS nhé.

# **Phần 1: Nói nhảm (Review)**

Teleport là một dạng remote SSH mới, màu mè và mang tính quản lý tập trung hơn so với SSH truyền thống cũng như mang nhiều khía cạnh khác về bảo mật, phân quyền và định danh:

  * Nếu đứng dưới khía cạnh của một nhà cung cấp phần mềm dịch vụ SaaS (Software as a Service) thì Teleport giúp phù hợp với yêu cầu bảo mật.
  * Cho phép quản lý nhân viên truy cập vào cơ sở hạ tầng qua nhiều khu vực và từ nhiều nhà quản lý khác nhau (có thể hiểu nhiều nhà cung cấp dịch vụ khác nhau).
  * Co giãn (mở rộng, thu hẹp) các dịch vụ quản lý liên quan đến IT qua cơ sở hạ tầng của khách hàng.



**Được thiết kế cho các Clusters** :

  * Quản lý người dùng truy cập loại ứng dụng nào đó trên nhiều server rải rác.
  * Định nghĩa, định danh và phân quyền ở cấp độ cluster.
  * Tất cả truy cập đến bất kỳ node nào phải luôn thông qua một proxy (aka, SSH bastion).

![](https://cloudcraft.info/wp-content/uploads/2018/01/teleport-0.png)

**Tính năng**

  * **Có thể đi qua firewall:** Truy cập từ xa đến các clusters kể cả nằm sau firewall thông qua các “outbound SSH tunnels” từ cluster.
  * **Kiểm soát hoàn toàn:** Mọi phiên hoạt động đều được ghi lại.
  * **Hoạt động với OpenSSH:** Có thể sử dụng cơ sở hạ tầng SSH có sẵn.
  * **Dán nhãn các máy Node:** Các máy có thể được gán nhãn với các biến tự động để dễ dàng định hướng trong cluster.
  * **WebUI và CLI:** WebUI cho phép dễ dàng phân tích. Command line để tự động hóa.
  * **Role based access controls:** Tùy chỉnh quyền truy cập dựa trên vai trò hoạt động (operation role).
  * **Tích hợp với các trình quản lý định danh có sẵn:** Như tích hợp với OIDC, OAuth hoặc SAML
  * **Cấu hình tự động:** Cập nhật cấu hình kể cả khi đang chạy.
  * **External audit logging:** Có thể lưu trữ các audit log ở hệ thống bên ngoài như Splunk, Alert Logic.



# **Phần 2: Cấu hình (Configuration)**

Gửi các bạn một số thông tin IP (IP này dùng local, với IP public cũng tương tự)

  * Master (Node, Proxy, Auth):



OS: CentOS 6

IP: 192.168.100.80

  * Node (Node, Proxy):



OS: CentOS 7

IP: 192.168.100.81

\- **Cài đặt Teleport ở cả master và node**. Mục tiêu của chúng ta là cài đặt teleport trên tất cả các máy thuộc cluster.

Link: https://github.com/gravitational/teleport/releases

Các bạn download phiên bản latest về, sử dụng bản build sẵn cho đơn giản, nếu bạn muốn custom thì có thể tải source, modify và rebuild bằng golang các bạn nhé. Ở đây mình sử dụng bản 2.3.5-bin.

Giờ nhào vô cài teleport trong 3 bước ở mọi node thôi:

wget https://github.com/gravitational/teleport/releases/download/v2.3.5/teleport-v2.3.5-linux-amd64-bin.tar.gz

tar xzf teleport-v2.3.5-linux-amd64-bin.tar.gz && cd teleport

./install

**\- Cài đặt Master**

Tạo tập tin cấu hình định nghĩa Master:

vim /etc/teleport.yaml

Thêm nội dung:
    
    
    teleport:
        nodename: master
        data_dir: /var/lib/teleport
        auth_token: "4f40a21ebfbe73ebdc0897aa1739b981"
        advertise_ip: 192.168.100.80
        auth_servers: 
            - 192.168.100.80:3025
        connection_limits:
            max_connections: 1000
            max_users: 250
        log:
            output: stderr
            severity: ERROR
        storage:
            type: bolt
        ciphers:
          - aes128-ctr
          - aes192-ctr
          - aes256-ctr
          - aes128-gcm@openssh.com
          - arcfour256
          - arcfour128
        kex_algos:
          - curve25519-sha256@libssh.org
          - ecdh-sha2-nistp256
          - ecdh-sha2-nistp384
          - ecdh-sha2-nistp521
          - diffie-hellman-group14-sha1
          - diffie-hellman-group1-sha1
        mac_algos:
          - hmac-sha2-256-etm@openssh.com
          - hmac-sha2-256
          - hmac-sha1
          - hmac-sha1-96
    
    auth_service:
        enabled: yes
        authentication:
            type: local
            second_factor: off
            u2f:
                app_id: https://localhost:3080
                facets:
                - https://localhost:3080
        listen_addr: 0.0.0.0:3025
        tokens:
            - "proxy,node:4f40a21ebfbe73ebdc0897aa1739b981"
            - "auth:4f40a21ebfbe73ebdc0897aa1739b981"
        cluster_name: "cluster-1"
    
    ssh_service:
        enabled: yes
        listen_addr: 0.0.0.0:3022
        labels:
            role: node,proxy,master
            type: postgres
        commands:
        - name: rpm             # this command will add a label like 'rpm=x86_64' to a node
          command: [uname, -p]
          period: 1h0m0s
        permit_user_env: false
    
    proxy_service:
        enabled: yes
        listen_addr: 0.0.0.0:3023
        tunnel_listen_addr: 0.0.0.0:3024
        web_listen_addr: 0.0.0.0:3080
        #https_key_file: /var/lib/teleport/webproxy_cert.pem
        #https_cert_file: /var/lib/teleport/webproxy_key.pem
    

Giải thích:

  * nodename: Thành domain bạn muốn, domain này cần trỏ về IP public của node đó, dùng cho việc truy cập mà không dùng IP
  * data_dir: Là thư mục mặc định lưu dữ liệu của teleport (mặc định đặt tại /var/lib/teleport)
  * advertise_ip và auth_servers: Sử dụng IP public hoặc IP mà các bạn muốn dùng để xác thực.



(Một số fields khác mình sẽ mô tả rõ hơn ở bài viết tới nếu có)

Khởi động teleport thôi:

teleport start -c /etc/teleport.yaml --insecure &

Giải thích:

teleport start là command để khởi động teleport, gồm tất cả những service đã cấu hình tại file teleport.yaml, mình dùng mode insecure vì key và cert được tạo ra chưa được sign bởi CA, dấu "&" dùng cho chạy ở chế độ background.

![](https://cloudcraft.info/wp-content/uploads/2018/01/teleport-1.png)

 

Thêm một node với vai trò proxy,node:

tctl nodes add --ttl=10m --roles=node,proxy

Lưu ý: dòng "**teleport start --roles=node,proxy --token=0652d8ef6fad10e8ecd5de87a6b9d310 --auth-server=192.168.100.80:3025** " được dùng như lệnh để chạy teleport ở máy node. ttl=10m có nghĩa là token tạo ra từ master có hiệu lực trong 10 minutes, sau thời gian đó thì token này không còn hợp lệ, bạn cần quay lại Master để tạo.

![](https://cloudcraft.info/wp-content/uploads/2018/01/teleport-2.png)

 

**\- Cài đặt Node**

Sau khi đã install Teleport, ta bắt đầu dùng lệnh lấy được từ việc dùng CLI ở Master bên trên để thêm Node hiện tại vào Cluster.

teleport start --roles=node,proxy --token=0652d8ef6fad10e8ecd5de87a6b9d310 --auth-server=192.168.100.80:3025 &

Lưu ý: dùng lệnh lấy được từ Master nhưng bổ sung thêm "&" để chạy background các bạn nhé, không thì cái session ssh là mất luôn process đó.

![](https://cloudcraft.info/wp-content/uploads/2018/01/teleport-3.png)

# 

# **Phần 3: Quản trị (Management)**

\- Tại Master:

Ta tạo một User để quản lý bằng giao diện WebUI nhé:

tctl users add test root

Giải thích:

  * tctl users add: là lệnh thêm user
  * test: chính là username
  * root: tức là user tương ứng với OS, có thể nhiều user: root,admin,username

![](https://cloudcraft.info/wp-content/uploads/2018/01/teleport-4.png)  

Chắc các bạn cũng thắc mắc, tại sao không thấy chỗ thiết lập Password cho User ?. Cũng tương tự như lệnh tạo token để thêm Node vào Cluster, ta cũng có một đường dẫn để làm việc này, truy cập vào đường dẫn "**https://master:3080/web/newuser/8bf81641af799dc227b218df8b970000** " sẽ cho phép ta thiết lập Password cho user mới tạo này. Lưu ý là dùng link này trên trình duyệt của máy có thể kết nối đến Master tại port 3080, "master" chính là cái Nodename ở teleport.yaml. Cho nên bạn nên để domain nào đó và trỏ về Nodename để dễ sử dụng. Do mình dở quá nên đặt master và giờ phải trỏ tạm file hosts (hoặc truy cập thẳng bằng IP của master các bạn nhé).

\- Tại máy bất kỳ có thể truy cập được cái link trên và đặt Password cho user mới tạo thôi, ở đây mình đã thay master=192.168.100.80

**https://192.168.100.80:3080/web/newuser/8bf81641af799dc227b218df8b970000**

![](https://cloudcraft.info/wp-content/uploads/2018/01/teleport-5.png)

\- Bạn cũng có thể thấy được đang có 2 nodes

![](https://cloudcraft.info/wp-content/uploads/2018/01/teleport-6.png)  

\- Nếu bạn chọn **Login as Root** thì sẽ có thể dùng SSH ở giao diện Web như thế này

![](https://cloudcraft.info/wp-content/uploads/2018/01/teleport-7.png)  

\- Ngoài ra bạn cũng có thể xem Realtime một session nào đó khi người ta gõ cái gì bằng việc **Join Session** hoặc xem lại session cũ đã thao tác những lệnh gì bằng nút **Play Session**

![teleport-8](https://cloudcraft.info/wp-content/uploads/2018/01/teleport-8.png)  

**"Ở giới hạn bài post, mình chỉ hướng dẫn sơ lược như vậy thôi. Nếu có loạt bài tiếp theo, mình sẽ giới thiệu sâu hơn và giải thích kỹ lưỡng hơn cách nó hoạt động và cấu hình. Hoặc hướng dẫn cài đặt và ứng dụng vào một số liên quan đến Ochestration Kubernetes, container."**

   
