---
title: "Hướng dẫn cấu hình virtual IP với keepalived"
date: 2017-12-27 16:03:47
categories: [Linux, High Availability]
---

Đối với những mô hình dịch vụ cần đảm bảo tính sẵn sàng cao (High Availability - HA), thì việc hệ thống bị down là không thể chấp nhận được. Hiện có rất nhiều phần mềm, giải pháp để đảm bảo tính HA cho các hệ thống nhưng mình sẽ giới thiệu phần mềm đơn giản nhất là keepalived với tính năng tự động switch Virtual IP (VIP) giữa các máy chủ theo mô hình Active/Passive. Bạn nào lười đọc lý thuyết thì có thể kéo thẳng xuống phần thực hành ở dưới hoặc làm theo cheatsheet này ;) [Link github](https://github.com/cloudcraftteam/System-Engineer-Cheat-Sheets/blob/master/High%20Availability/keepalived-nginx.txt)

# Giới thiệu về keepalived

Keepalived là một phần mềm định tuyến, được viết bằng ngôn ngữ C. Chương trình keepalived cho phép nhiều máy tính cùng chia sẻ một địa chỉ IP ảo với nhau theo mô hình Active – Passive (ta có thể cấu hình thêm một chút để chuyển thành mô hình Active – Active). Khi người dùng cần truy cập vào dịch vụ, người dùng chỉ cần truy cập vào địa chỉ IP ảo dùng chung này thay vì phải truy cập vào những địa chỉ IP thật của các thiết bị kia. Một số đặc điểm của phần mềm Keepalived:

  * Keepalived không đảm bảo tính ổn định của dịch vụ chạy trên máy chủ, nó chỉ đảm bảo rằng sẽ luôn có ít nhất một máy chủ chịu trách nhiệm cho IP dùng chung khi có sự cố xảy ra.
  * Keepalived thường được dùng để dựng các hệ thống HA (High Availability) dùng nhiều router/firewall/server để đảm bảo hệ thống được hoạt động liên tục.
  * Keepalived dùng giao thức _**VRRP (Virtual Router Redundancy Protocol)**_ để liên lạc giữa các thiết bị trong nhóm.



# Giới thiệu về giao thức VRRP

Virtual router đại diện cho một nhóm thiết bị sẽ có một virtual IP và một đỉa chỉ **MAC (Media Access Control)** đặc biệt là **_00-00-5E-00-01-XX_**. Trong đó, XX là số định danh của router ảo – **Virtual Router Identifier (VRID)** , mỗi virtual router trong một mạng sẽ có một giá trị VRID khác nhau. Vào mỗi thời điểm nhất định, chỉ có một router vật lý dùng địa chỉ MAC ảo này. Khi có ARP request gởi tới virtual IP thì router vật lý đó sẽ trả về địa chỉ MAC này. Các router vật lý sử dụng chung VIP phải liên lạc với nhau bằng địa chỉ multicast _**224.0.0.18**_ bằng giao thức VRRP. Các router vật lý sẽ có độ ưu tiên (priority) trong khoảng từ 1 – 254, và router có độ ưu tiên cao nhất sẽ thành Master, các router còn lại sẽ thành các Slave/Backup, hoạt động ở chế độ chờ.

# Cơ chế hoạt động

Như đã nói ở trên, các router/server vật lý dùng chung VIP sẽ có 2 trạng thái là **MASTER/ACTIVE** và **BACKUP/SLAVE**. Cơ chế failover được xử lý bởi giao thức VRRP, khi khởi động dịch vụ, toàn bộ các server dùng chung VIP sẽ gia nhập vào một nhóm multicast. Nhóm multicast này dùng để gởi/nhận các gói tin quảng bá VRRP. Các router sẽ quảng bá độ ưu tiên (priority) của mình, server với độ ưu tiên cao nhất sẽ được chọn làm MASTER. Một khi nhóm đã có 1 MASTER thì MASTER này sẽ chịu trách nhiệm gởi các gói tin quảng bá VRRP định kỳ cho nhóm multicast. Nếu vì một sự cố gì đó mà các server BACKUP không nhận được các gói tin quảng bá từ MASTER trong một khoảng thời gian nhất định thì cả nhóm sẽ bầu ra một MASTER mới. MASTER mới này sẽ tiếp quản địa chỉ VIP của nhóm và gởi các gói tin ARP báo là nó đang giữ địa chỉ VIP này. Khi MASTER cũ hoạt động bình thường trở lại thì router này có thể lại trở thành MASTER hoặc trở thành BACKUP tùy theo cấu hình độ ưu tiên của các router.

# Cấu hình

Trong mô hình này, ta sẽ có 2 load balancer chạy Nginx (bạn có thể đổi thành HAProxy tùy ý) phân tải cho 2 web server Nginx ở phía sau. 2 load balancer này sẽ được cấu hình dùng chung một VIP. Bình thường thì VIP này sẽ cho node Master phụ trách, node Backup sẽ ở trạng thái chờ. Khi có sự cố xảy ra với node Master thì node Backup sẽ nhận lấy VIP này và chịu trách nhiệm phân tải về cho các backend server nằm ở sau.

![](https://cloudcraft.info/wp-content/uploads/2017/12/Huong-dan-cau-hinh-virtual-ip-voi-keepalived-1.png)

_Mô hình thử nghiệm với keepalived và nginx_

![](https://cloudcraft.info/wp-content/uploads/2017/12/Huong-dan-cau-hinh-virtual-ip-voi-keepalived-2.png)

_VIP sẽ được chuyển cho 1 Master khác nếu dịch vụ trên Master chính gặp sự cố_

Để cài đặt dịch vụ keepalived trên yum, ta làm theo câu lệnh sau trên cả 2 node
    
    
    yum update
    yum install gcc kernel-headers kernel-devel
    yum install keepalived

Cấu hình cho phép gắn địa chỉ IP ảo lên card mạng
    
    
    echo "net.ipv4.ip_nonlocal_bind = 1" >> /etc/sysctl.conf
    sysctl -p

Để cấu hình dịch vụ keepalived, ta cần phải chỉnh sửa file ** _/etc/keepalived/keepalived.conf_**. Một số block đáng chú ý trong file này như sau:

  * **_global_defs_** : cấu hình global cho keepalived như gởi email thông báo tới đâu, tên của cluster đang cấu hình.
  * **_vrrp_script_** : chứa script hoặc đường dẫn tới script kiểm tra dịch vụ (Ví dụ: nếu dịch vụ này down thì keepalived sẽ chuyển VIP sang 1 server khác).
  * **_vrrp_instance_** : thông tin chi tiết về 1 server vật lý trong nhóm dùng chung VRRP. Gồm các thông tin như interface dùng để liên lạc của server này, độ ưu tiên để, virtual IP tương ứng với interface, cách thức chứng thực, script kiểm tra dịch vụ….

Ta sẽ cấu hình cho 2 node dùng chung VIP 192.168.31.10, keepalived sẽ kiểm tra trạng thái của process nginx trên node MASTER để quyết định trạng thái của node này. Cấu hình mẫu theo mô hình Active/Passive: **Node MASTER**
    
    
    global_defs {
    	vrrp_version 3
    }
    
    vrrp_script chk_nginx {
    	# Check status of nginx main process
    	script "pidof nginx"
    	# Time interval
    	interval 2
    }
    
    vrrp_instance VIP1 {
    	state MASTER
    	# Master priority (1-254)
    	priority 200
    	# VRRP sending interval
    	advert_int 1
    	
    	virtual_router_id 11
    	virtual_ipaddress
    	{
    		192.168.31.10/24 dev ens33 label ens33:vip_1
    	}
    	
    	authentication
    	{
    		# Use IP-Sec Authentication Header
    		# More secure than plain text password
    		auth_type AH
    		# The auth_pass will only use the first 8 characters entered.
    		auth_pass aabbccdd
    	}
    	
    	track_script 
    	{
    		chk_nginx
    	}
    }

**Node BACKUP**
    
    
    global_defs {
    	vrrp_version 3
    }
    
    vrrp_script chk_nginx {
    	# Check status of nginx main process
    	script "pidof nginx"
    	# Time interval
    	interval 2
    }
    
    vrrp_instance VIP1 {
    	state BACKUP
    	# Backup priority (1-254)
    	priority 100
    	# VRRP sending interval
    	advert_int 1
    	
    	virtual_router_id 11
    	virtual_ipaddress
    	{
    		192.168.31.10 dev ens33 label ens33:vip    
    	}
    	
    	authentication
    	{
    		# Use IP-Sec Authentication Header
    		# More secure than plain text password
    		auth_type AH
    		# The auth_pass will only use the first 8 characters entered.
    		auth_pass aabbccdd
    	}
    	
    	track_script 
    	{
    		chk_nginx
    	}
    }

**Khởi động lại dịch vụ và kiểm tra log**
    
    
    # Start keepalived service on both VM #
    systemctl start keepalived
    systemctl enabled keepalived
    systemctl status keepalived
    
    # Check if keepalived works #
    ip addr show
    tail -f /var/log/messages | grep vrrp
    tcpdump -vvv -n -i ens34 vrrp
    
    # Allow VRRP traffice through iptables
    iptables -I INPUT -p vrrp -j ACCEPT
    iptables -I OUTPUT -p vrrp -j ACCEPT

# Kết quả

Khởi động server Master
    
    
    centos-master Keepalived_vrrp[1084]: VRRP_Script(chk_nginx) succeeded
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) Transition to MASTER STATE
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) Entering MASTER STATE
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) setting protocol VIPs.
    centos-master Keepalived_vrrp[1084]: Sending gratuitous ARP on ens33 for 192.168.31.10
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) Sending/queueing gratuitous ARPs on ens33 for 192.168.31.10
    centos-master Keepalived_vrrp[1084]: Sending gratuitous ARP on ens33 for 192.168.31.10
    centos-master Keepalived_vrrp[1084]: Sending gratuitous ARP on ens33 for 192.168.31.10

Ta tắt thử dịch vụ nginx trên node MASTER thì thấy node này chuyển thành FAULT STATE, không còn giữ VIP nữa
    
    
    centos-master systemd: Stopping nginx - high performance web server...
    centos-master systemd: Stopped nginx - high performance web server.
    centos-master Keepalived_vrrp[1084]: /usr/sbin/pidof nginx exited with status 1
    centos-master Keepalived_vrrp[1084]: VRRP_Script(chk_nginx) failed
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP2) Now in FAULT state
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) Entering FAULT STATE
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) removing protocol VIPs.
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) Now in FAULT state
    centos-master ntpd[664]: Deleting interface #8 ens33:vip_1, 192.168.31.10#123, interface stats: received=0, sent=0, dropped=0, active_time=310 secs

BACKUP server lúc này sẽ tự động chuyển sang MASTER và nhận VIP 192.168.31.10. Node BACKUP sẽ gởi các gói tin quảng bá ARP cho những node khác biết là nó đang giữ VIP này.
    
    
    centos-minion1 Keepalived_vrrp[835]: VRRP_Instance(VIP1) Transition to MASTER STATE
    centos-minion1 Keepalived_vrrp[835]: VRRP_Instance(VIP1) Entering MASTER STATE
    centos-minion1 Keepalived_vrrp[835]: VRRP_Instance(VIP1) setting protocol VIPs.
    centos-minion1 Keepalived_vrrp[835]: Sending gratuitous ARP on ens33 for 192.168.31.10
    centos-minion1 Keepalived_vrrp[835]: VRRP_Instance(VIP1) Sending/queueing gratuitous ARPs on ens33 for 192.168.31.10
    centos-minion1 Keepalived_vrrp[835]: Sending gratuitous ARP on ens33 for 192.168.31.10
    centos-minion1 Keepalived_vrrp[835]: Sending gratuitous ARP on ens33 for 192.168.31.10

Khi dịch vụ nginx trên node MASTER cũ được khởi động lại thì node này quay về trạng thái MASTER
    
    
    centos-master systemd: Starting nginx - high performance web server...
    centos-master nginx: nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
    centos-master nginx: nginx: configuration file /etc/nginx/nginx.conf test is successful
    centos-master systemd: Failed to read PID from file /var/run/nginx.pid: Invalid argument
    centos-master systemd: Started nginx - high performance web server.
    centos-master Keepalived_vrrp[1084]: VRRP_Script(chk_nginx) succeeded
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) forcing a new MASTER election
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) Transition to MASTER STATE
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) Entering MASTER STATE
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) setting protocol VIPs.
    centos-master Keepalived_vrrp[1084]: Sending gratuitous ARP on ens33 for 192.168.31.10
    centos-master Keepalived_vrrp[1084]: VRRP_Instance(VIP1) Sending/queueing gratuitous ARPs on ens33 for 192.168.31.10
    centos-master Keepalived_vrrp[1084]: Sending gratuitous ARP on ens33 for 192.168.31.10
    centos-master Keepalived_vrrp[1084]: Sending gratuitous ARP on ens33 for 192.168.31.10

Như vậy là ta đã cấu hình thành công chương trình keepalived theo mô hình Active/Passive. **Active/Active setup** Cấu hình Active/Active setup cũng sẽ tương tự như mô hình Active/Passive. Tuy nhiên, bạn sẽ cần thêm 1 VIP (VRRP instance) nữa và setup DNS Round Robin tới 2x VIPs này Do bài viết đã dài, bạn nào quan tâm có thể tham khảo thêm [Ansible Role](https://github.com/nduytg/ansible_keepalived) này của mình để tìm hiểu cách setup
