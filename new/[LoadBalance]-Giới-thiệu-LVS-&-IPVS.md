---
title: "[LoadBalance] Giới thiệu LVS & IPVS"
date: 2019-09-26 10:00:59
categories: [Linux, High Availability, Load Balancing, TCP/IP]
---

# **Giới thiệu LVS & IPVS**

## Giới thiệu

LVS là cụm viết tắt thường thấy của Linux Virtual Server. Virtual Server ở đây không phải ám chỉ một công nghệ ảo hóa mà là một cluster gồm các Load Balancer (LB) và các Real Server. Cả Virtual Server sẽ có một Virtual IP duy nhất cho phép Client tạo request tới. Tùy vào cách cấu hình mà các LB sẽ phân phối request tới Real Server thích hợp. ![](https://cloudcraft.info/wp-content/uploads/2018/09/VirtualServer.png) LVS được chia làm ba loại: 

  * **NAT** : NAT 1:n. N Real Server với n Private IP sẽ được NAT 1:n thông qua IP private. LB server khi này đóng vai trò như là Gateway của hệ thống cân tải, các Real Server sẽ trỏ gateway của nó tới LB. Mọi gói tin in/out đều sẽ phải thông qua các LB. Dễ thấy vấn đề bottleneck có thể xảy ra đối với các LB này. [Here](http://www.linuxvirtualserver.org/VS-NAT.html)
  * **Direct Routing (DSR)** : Giải pháp này nhằm giải quyết vấn đề bottleneck khi dùng mô hình NAT như trên. Với mô hình này, các Real Server khi phản hồi client sẽ tiến hành phản hồi trực tiếp, không thông qua LB server. Cả mô hình sẽ sử dụng chung 1 VIP, khi phản hồi thì Real Server sẽ phản hồi lại thông qua VIP đã được gán. Điều này chỉ áp dụng khi các server thuộc cùng 1 Vlan. [Here](http://www.linuxvirtualserver.org/VS-DRouting.html)
  * **IP Tunneling** : Cơ chế cơ bản giống như Direct Routing, chỉ khác là nó sẽ đóng gói gói tin và truyền qua IP tunnel. Mô hình này được dùng cho khi các server nằm ở nhiều vlan hoặc DC khác nhau. [Here](http://www.linuxvirtualserver.org/VS-IPTunneling.html)

Thông thường, khi nhắc đến LVS, ta thường nhắc đến IPVS (đã được tích hợp sẵn trong Linux Kernel). IPVS là phần mềm cấu hình LVS Load balancing ở layer 4. 

## Cài đặt và cấu hình LVS với IPVS****

Ở bài viết này, mình sẽ hướng dẫn các bạn cấu hình LVS bằng công cụ IPVS đơn giản. Dưới đây sẽ là cấu hình LVS theo mô hình Direct Routing (DSR) Cài đặt gói dịch vụ của redhat 
    
    
    sudo yum install -y epel-release

Cài đặt **ipvsadm** để quản lý cấu hình cho LVS 
    
    
    sudo yum install -y ipvsadm

Bật tính năng forward giữa các interfaces trên linux 
    
    
    sudo echo "net.ipv4.ip_forward = 1" >> /etc/sysctl.conf
    sudo sysctl -p

Tạo file lưu giá trị config cho ipvsadm rồi start ipvsadm lên 
    
    
    sudo touch /etc/sysconfig/ipvsadm
    sudo systemctl start ipvsadm
    sudo systemctl enable ipvsadm

Chỉnh cấu hình trong file cấu hình chính ipvsadm-config tại các vị trí này bật tính năng lên: 
    
    
    IPVS_SAVE_ON_STOP="yes"
    IPVS_SAVE_ON_RESTART="yes"

**IPVS_SAVE_ON_STOP** cho phép lưu cấu hình đã cài đặt cho việc load balancer mỗi khi mình stop dịch vụ ipvsadm. Tương tự **IPVS_SAVE_ON_RESTART** thì dành cho restart dịch vụ. Tiến hành cấu hình cho ipvsadm load balancer, ở đây sử dụng ví dụ cấu hình cho LVS DSR gồm: Server load balancer (LB) với IP: 

  * Card mạng ens33: 192.168.100.2
  * VIP trên ens33: 192.168.100.100 và port 80

2 Server real chạy dịch vụ web: 
  * Web01: 192.168.100.4:80
  * Web02: 192.168.100.5:80

Mô hình ![](https://cloudcraft.info/wp-content/uploads/2019/09/loadbalance-gioi-thieu-lvs-ipvs-2-e1569400340700.jpg) Tiến hành cấu hình LVS trên server LB 
    
    
    ipvsadm -A -t 192.168.100.100:80 -s rr
    ipvsadm -a -t 192.168.100.100:80 -r 192.168.100.4:80 -g
    ipvsadm -a -t 192.168.100.100:80 -r 192.168.100.5:80 -g

Cấu hình trên có nghĩa đối với những request vào VIP trên port 80 sẽ được load balance xuống 2 con web server Ta có thể kiểm tra cấu hình bằng lệnh 
    
    
    ipvsadm –L -v

Do cấu hình mô hình DSR cho nên cả LB server và các Real Server đều giữ VIP, vì vậy để Gateway/Router của đường mạng có thể gửi gói tin từ client đến đúng server LB thì ta cần phải tắt ARP cho interface giữ VIP của các Real Server. 
    
    
    ip link set dev eth0 arp off

Thay "eth0" thành tên card mạng chứa VIP mà server bạn đang dùng **Phụ thêm** Nếu sử dụng cấu hình theo NAT, ta thay cờ “-g” thành cờ “-m” để sử dụng masquerading dành cho NAT và ta cấu hình iptables trên server real để cho phép nhận gói tin từ LB. VD: 
    
    
    iptables -t nat -A PREROUTING -p tcp -d 192.168.100.100 --dport 80 -j REDIRECT --to-ports 80

Ngoài ra ở trên real server gateway nên trỏ về địa chỉ IP private của LB (địa chỉ dùng để liên lạc giữa LB và đường mạng nội bộ bên trong) VD: Đường mạng trong private là 172.16.0.0/24 và địa chỉ private của LB là 172.16.0.2 thì khi đó ở các real server có gateway trỏ về LB là 172.16.0.2 ![](https://cloudcraft.info/wp-content/uploads/2019/09/loadbalance-gioi-thieu-lvs-ipvs-3.jpg) Để quản lý bằng giao diện GUI thân thiện dễ sử dụng ta cài thêm dịch vụ piranha (Centos 7 không còn hỗ trợ thay vào đó để load balancing với uptime cao người ta dùng haproxy kết hợp keepalive, việc cài đặt dưới đây được tiến hành trên Centos 6) 
    
    
    sudo yum install -y piranha

# Tham khảo

<http://www.linuxvirtualserver.org/VS-DRouting.html> <http://www.linuxvirtualserver.org/VS-NAT.html> [https://www.server-world.info/en/note?os=CentOS_7&p=lvs&f=1](https://www.server-world.info/en/note?os=CentOS_7&p=lvs&f=1) <http://www.hvaonline.net/hvaonline/posts/list/5/5417.hva> <http://www.austintek.com/LVS/LVS-HOWTO/HOWTO/LVS-HOWTO.ipvsadm.html> <https://sysadmins.co.za/setup-piranha-loadbalancer-on-centos/>
