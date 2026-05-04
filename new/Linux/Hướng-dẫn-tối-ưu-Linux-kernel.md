---
title: "Hướng dẫn tối ưu Linux kernel"
date: 2017-11-29 16:57:15
categories: [Linux]
---

Kernel của Linux có rất nhiều những thông số cho phép người dùng tùy chỉnh nhằm tối ưu hiệu năng của hệ thống và ứng dụng. Tùy vào nhu cầu thực tế mà người quản trị có thể tối ưu các tham số này khác nhau.

Để tương tác với các tham số của kernel, ta sử dụng chương trình **_sysctl_** (system control) để tương tác với kernel, cho phép thay đổi thông số của kernel trong lúc hệ thống đang chạy mà không bị gián đoạn.

Bạn nào lười đọc chữ thì có thể tham khảo cheat-sheet này^^: [GitHub](https://github.com/cloudcraftteam/System-Engineer-Cheat-Sheets/blob/master/Tunning%20kernel/Tuning%20Kernel.txt)

# Tunning Kernel

Để hiển thị toàn bộ các tham số hiện có của kernel, ta dùng lệnh:
    
    
    sysctl –a

_![](http://cloudcraft.info/wp-content/uploads/2017/11/sysctl-a.png)_

![](https://cloudcraft.info/wp-content/uploads/2017/11/huong-dan-toi-uu-linux-kernel-1-1.png)

_Có khoảng hơn…900 biến dùng để cấu hình cho kernel_

Nhưng trước khi đi sâu vào tùy chỉnh các biến này, ta cần phải nắm sơ lược về _**/proc**_ filesystem.

## proc filesystem

Một số filesystem như **_/proc_** được gọi là pseudo filesystem (file system giả) bởi vì filesystem này không tồn tại trên ổ cứng. Filesystem /proc chỉ chứa các file ảo (tồn tại trên bộ nhớ RAM), cho phép người dùng đọc được thông tin hệ thống theo thời gian thực (bộ nhớ RAM, thông tin CPU, các thiết bị được gắn lên, cấu hình phần cứng).

![](https://cloudcraft.info/wp-content/uploads/2017/11/huong-dan-toi-uu-linux-kernel-2-1.png)

_Danh sách các file, thư mục trong /proc_

Một số file quan trọng trên /proc là:

  * **_/proc/cpuinfo_**
  * **_/proc/sys_**
  * **_/proc/interrupts_**
  * **_/proc/meminfo_**
  * **_/proc/mounts_**
  * **_/proc/partitions_**
  * **_/proc/version_**
  * **_/proc/ <Process-ID>_**



Ở đây, ta sẽ tập trung vào cấu trúc của thư mục **_/proc/sys_** , đây là thư mục chứa hầu như toàn bộ thông tin cấu hình của các thiết bị được kết nối tới máy tính, driver các thiết bị, cấu hình file system… Trong thư mục này gồm có những thư mục con như sau:

  * **abi/** : application binary interface
  * **dev/** : chứa thông số cấu hình các thiết bị được kết nối tới máy tính
  * **fs/** : chứa thông số cấu hình filesystem như (như cấu hình disk quotas hoặc inodes…).
  * **kernel/** : chứa các cấu hình của kernel.
  * **net/** : chứa các thông số cấu hình mạng.
  * **vm/** : chứa các thông số cấu hình vm (virtual memory).
  * **user/** : chứa các thông số về giới hạn namespace của từng user.



## Thay đổi các thông số của kernel

Để thay đổi các thông số của kernel, ta có thể trực tiếp thay đổi thông số của các file trong** _/proc/sys_**

Ví dụ: muốn thay đổi thông số **_net.ipv4.ip_forward_** , ta sẽ thay đổi giá trị của file **_/proc/sys/net/ipv4/ip_forward_** như sau:_![](http://cloudcraft.info/wp-content/uploads/2017/11/change-value.png)_

![](https://cloudcraft.info/wp-content/uploads/2017/11/huong-dan-toi-uu-linux-kernel-3-1.png)

_Thay đổi trực tiếp giá trị của các file cấu hình_

Hoặc ta có thể thay đổi các thông số này qua lệnh **_sysctl_** với cờ **_-w_**

![](https://cloudcraft.info/wp-content/uploads/2017/11/huong-dan-toi-uu-linux-kernel-4-1.png)_Thay đổi thông số của kernel thông qua lệnh sysctl_

Tuy nhiên, khi dùng lệnh **_sysctl –w_** thì thay đổi chỉ có giá trị trong session đó, nếu hệ thống khởi động lại thì giá trị đó sẽ trở về mặc định, không được lưu lại. Để lưu lại giá trị cấu hình kernel, ta cần thay đổi file **_/etc/sysctl.conf_** và nạp lại cấu hình bằng lệnh **_sysctl –p_**

# Một số thông số kernel mẫu

Dưới đây là một số thông số kernel mẫu, các bạn có thể tham khảo, bản full thì coi tại đây: [GitHub](https://github.com/cloudcraftteam/System-Engineer-Cheat-Sheets/blob/master/Tunning%20kernel/Tuning%20Kernel.txt)

Chép đè các thông số này lên **/etc/sysctl.conf** => chạy lệnh**sysctl -p** để nạp các thông số này vào hệ thống
    
    
    ### Improving Network Performance ###
    ## Congestion control ##
    net.ipv4.tcp_congestion_control = htcp
    net.ipv4.tcp_timestamps = 1
    net.ipv4.tcp_window_scaling = 1
    net.ipv4.tcp_sack = 1
    
    ## Increase socket buffer ##
    # Increase Read Memory Buffer #
    # TCP Read Memory: Min - Default - Max #
    net.ipv4.tcp_rmem = 8192 87380 16777216
    net.ipv4.udp_rmem_min = 16384
    # Default read memory buffer of all receiving sockets (except TCP and UDP)
    net.core.rmem_default = 262144
    net.core.rmem_max = 16777216
    
    # Increase Write Memory Buffer #
    # TCP Write Memory: Min - Default - Max #
    net.ipv4.tcp_wmem = 8192 65536 16777216
    net.ipv4.udp_wmem_min = 16384
    # Default read memory buffer of all sending sockets (except TCP and UDP)
    net.core.wmem_default = 262144
    net.core.wmem_max = 16777216
    
    # Increase connection queue #
    net.core.somaxconn = 16384
    
    # Improve packet processing queue, speed #
    net.core.netdev_max_backlog = 16384
    net.core.dev_weight = 64
    
    ## Improve connection tracking ##
    # For high-loaded servers #
    net.nf_conntrack_max = 100000
    #or
    net.netfilter.nf_conntrack_max = 100000
    
    # Decrease connection timeout in netfilter table #
    net.netfilter.nf_conntrack_tcp_timeout_established = 600
    
    ## Improving Network Security ##
    # Prevent SYN Attack #
    net.ipv4.tcp_syncookies = 1
    net.ipv4.tcp_max_syn_backlog = 4096
    net.ipv4.tcp_syn_retries = 2
    net.ipv4.tcp_synack_retries = 2
    
    ## Prevent IP spoofing ##
    # Enable reverse path filter to verify IPs #
    net.ipv4.conf.all.rp_filter = 1
    net.ipv4.conf.default.rp_filter = 1
    net.ipv4.conf.all.log_martians = 1
    net.ipv4.conf.default.log_martians = 1
    
    ## Decrease TCP FIN timeout ##
    net.ipv4.tcp_fin_timeout = 7
    
    ## Decrease keep alive waiting time ##
    net.ipv4.tcp_keepalive_time = 300
    net.ipv4.tcp_keepalive_probes = 5
    net.ipv4.tcp_keepalive_intvl = 15
    
    # Disable Proxy ARP #
    net.ipv4.conf.all.proxy_arp = 0
    
    ### Filesystem Tuning ###
    ## Increase open file limit ##
    # For web/database/log servers which need a lot of open files #
    fs.file-max = 300000
    
    ### Memory Tuning ###
    ## Decrease swapping ##
    vm.swappiness = 10
    vm.dirty_background_ratio = 5
    vm.dirty_ratio = 10
    vm.overcommit_memory = 0
    vm.overcommit_ratio = 50

# Một số thông số của kernel cần lưu ý

Có rất nhiều thông số của kernel để tinh chỉnh, nhưng tựu chung lại gồm 1 số nhóm thông số chính sau trong file **_/etc/sysctl.conf_**.

**Lưu ý:** có những thông số cần phải có một module nào đó của kernel mới hoạt động được.

## Network – Improving Performance

Những thông số dưới đây nhằm tối ưu kết nối mạng trên các hệ điều hành Linux

**Tên thông số** | **Ý nghĩa**  
---|---  
_**Congestion Control**_  
_net.ipv4.tcp_congestion_control = htcp_ | Có thể kiểm tra trên kernel hiện đang bật module của thuật toán nào bằng tham số"_**net.ipv4.tcp_available_congestion_control** ". _Các phiên bản kernel hiện nay thì mặc định có 2 module thuật toán đã được bật là reno và cubic. Tham số bên biểu hiện đang dùng thuật toán H-TCP để kiểm soát tắt nghẽn với những kết nối mạng có bandwidth lớn, độ trễ thấp. Tuy nhiên do mặc định module htcp chưa được bật nên ta cần sử dụng lệnh “** _modprobe tcp_htcp_** ” trước để kích hoạt module này.  
_net.ipv4.tcp_timestamps = 1_ | Bật TCP timestamp, timestamp chuẩn giúp các thuật toán congestion control hoạt động tốt hơn đối với mạng có tốc độ cao (high speed network) Tuy nhiên, tính năng khởi tạo timestamp cũng làm tốn 1 phần tài nguyên hệ thống (timestamp tốn thêm 10 byte cho mỗi gói tin). Một số nguồn thông tin nói rằng không nên bật timestamps nếu đang sử dụng mạng có tốc độ thấp (low speed network)  
_net.ipv4.tcp_window_scaling = 1_ | Bật chế độ [windows scaling](https://en.wikipedia.org/wiki/TCP_window_scale_option) (mặc định là 1) Tính năng này cho phép việc co dãn được windows size trong kết nối tcp.  
_net.ipv4.tcp_sack = 1_ | Bật chế độ TCP SACK (Selective ACK). Cho phép client chỉ cần gởi lại những gói tin bị mất trên đường truyền, không cần gởi lại toàn bộ. Tuy nhiên, trong một số trường hợp TCP SACK có thể gây tiêu tốn nhiều tài nguyên CPU, RAM, giảm tốc độ truyền tải.  
_**Điều chỉnh buffer của các socket**_  
_net.ipv4.tcp_rmem = 8192 87380 16777216_ | Tinh chỉnh buffer nhận (min – default – max) của **_socket TCP_**. Tối ưu tốc độ chuyển file có dung lượng lớn qua các kết nối WAN. Hệ thống sẽ dựa vào 3 giá trị này để có thể tự động điều chỉnh kích thước buffer nhận của socket TCP phù hợp với nhu cầu của kết nối.  
_net.ipv4.udp_rmem_min = 16384_ | Kích thước buffer tối thiểu của các **_socket UDP_** nhận (tính theo bytes).  
_net.core.rmem_default = 262144_ | Kích thước buffer mặc định của toàn bộ các socket nhận (nếu ta có cấu hình **_tcp_rmem_** thì hệ thống sẽ ưu tiên giá trị này hơn).  
_net.core.rmem_max = 16777216_ | Kích thước buffer tối đa của toàn bộ các socket nhận  
_net.ipv4.tcp_wmem = 8192 65536 16777216_ | Tăng buffer gởi (min – default – max) của **_socket TCP_**. Tối ưu tốc độ chuyển file có dung lượng lớn qua các kết nối WAN.  
_net.ipv4.udp_wmem_min = 16384_ | Kích thước buffer tối thiểu của các **_socket UDP_** gởi (tính theo bytes).  
_net.core.wmem_default = 262144_ | Kích thước buffer mặc định của toàn bộ các socket gởi (nếu ta có cấu hình **_tcp_wmem_** thì hệ thống sẽ ưu tiên giá trị này hơn).  
_net.core.wmem_max = 16777216_ | Kích thước buffer tối đa của toàn bộ các socket gởi.  
**_Tăng số lượng kết nối (hàng đợi)_**  
_net.core.somaxconn = 16384_ | Giới hạn số kết nối tối đa nằm trong hàng đợi của các socket. Chỉ tăng tham số này lên khi lượng kết nối mới tới server tăng thường xuyên.  
_**Tăng số lượng gói tin xử lý**_  
_net.core.netdev_max_backlog = 16384_ | Số lượng gói tin tối đa nằm trong hàng đợi của card mạng.  
_net.core.dev_weight = 64_ | Số lượng gói tin tối đa mà mỗi CPU có thể xử lý được trong 1 interrupt.  
**_Connection Tracking_**  
_net.nf_conntrack_max = 100000_ Hoặc _net.netfilter.nf_conntrack_max = 100000_ (Thay đổi 1 trong 2 biến này, biến kia sẽ thay đổi theo) | Biến này quyết định số lượng kết nối đồng thời tới máy chủ. Mỗi một kết nối sẽ được ghi lại trong 1 bảng gọi là connection tracking table, mặc định thì bảng này có thể ghi lại được 65536 kết nối. Đối với những máy chủ có số lượt truy cập cao thì bản danh sách này sẽ bị đầy và các kết nối mới đến sẽ bị drop. Khi giá trị này đầy sẽ có một thông báo lỗi gửi trả về với nội dung "nf_conntrack: table full, dropping packet" Để khắc phục lỗi này, ta cần phải tăng kích thước của bảng này lên.  
_net.netfilter.nf_conntrack_tcp_timeout_established = 600_ | Giảm thời gian timeout của 1 connection trong tracking table (mặc định là 432000s = 5 ngày)  
  
## Network – Improving Security

Thay đổi các thông số dưới đây nhằm tăng cường tính bảo mật về kết nối cho máy chủ, giảm thiểu và ngăn chặn một số loại tấn công cơ bản, tiết kiệm được tài nguyên hệ thống. Tham khảo: [link](https://www.kernel.org/doc/Documentation/networking/ip-sysctl.txt)

**Tên thông số** | **Ý nghĩa**  
---|---  
_**Chặn SYN Attack bằng kỹ thuật SYNcookies**_  
_net.ipv4.tcp_syncookies = 1 (mặc định đã được bật sẵn)_ | Bật tính năng [SYN Cookies](https://en.wikipedia.org/wiki/SYN_cookies) để phòng chống [SYN Flood Attack](https://en.wikipedia.org/wiki/SYN_flood). Tính năng này sẽ được kích hoạt khi số gói tin SYN vượt quá giá trị max_syn_backlog. Không nên bật tính năng này nếu server **_thật sự_** nhận được nhiều gói tin SYN từ người dùng.  
_net.ipv4.tcp_max_syn_backlog = 4096_ | Tăng hàng đợi các bán kết nối đang chờ gói tin ACK từ người dùng (mặc định là 128). Giá trị này nên nhỏ hơn hoặc bằng giá trị "_**net.core.somaxconn** " _ Nếu giá trị "**_net.ipv4.tcp_max_syn_backlog_** " lớn hơn thì khi hàng đợi chạm ngưỡng của "_**net.core.somaxconn** " _các kết nối sau trong hàng đợi này sẽ tự động bị cắt bỏ.  
_net.ipv4.tcp_syn_retries = 2_ | Số lần gởi lại gói tin SYN cho 1 connection.  
_net.ipv4.tcp_synack_retries = 2_ | Số lần gởi lại gói tin SYN-ACK cho 1 connection.  
Tắt tính năng packet forwarding  
_net.ipv4.ip_forward = 0_ | Forward packet giữa các interface, chỉ nên bật tính năng này khi server nhận vai trò router  
_net.ipv4.conf.all.forwarding = 0_ | Tắt tính năng forwarding trên toàn bộ các interface hiện có.  
_net.ipv4.conf.default.forwarding = 0_ | Mặc định là tắt tính năng forwarding trên các interface được gắn thêm sau này.  
_net.ipv6.conf.all.forwarding = 0_ | Tương tự như với 2 cấu hình cho IPv4 ở trên  
_net.ipv6.conf.default.forwarding = 0_  
_**Tắt tính năng IP Source Routing**_  
_net.ipv4.conf.all.accept_source_route = 0_ | Tính năng source routing có thể bị người dùng lợi dụng cho [mục đích xấu](https://access.redhat.com/documentation/en-us/red_hat_enterprise_linux/6/html/security_guide/sect-security_guide-server_security-disable-source-routing), nên tắt tính năng này để ép gói tin tuân theo bảng định tuyến chuẩn trên các thiết bị. Tắt tính năng source routing trên toàn bộ các card mạng hiện tại.  
_net.ipv4.conf.default.accept_source_route = 0_ | Mặc định tắt tính năng source routing cho các card mạng được gắn thêm sau này.  
_net.ipv6.conf.all.accept_source_route = 0_ | Tắt source routing cho IPv6.  
_net.ipv6.conf.default.accept_source_route = 0_ | Tắt source routing cho IPv6.  
**Block các gói tin ICMP redirect để chặn kiểu tấn công Man-in-the-Middle**  
_net.ipv4.conf.all.accept_redirects = 0_ | Các gói tin ICMP redirect thường được các router gởi đi để thông báo cho server biết rằng có một đường đi khác tốt hơn tới đường đi hiện tại mà server đang chọn. Kẻ xấu có thể lợi dụng tính năng này để thay đổi bản định tuyến của server, ta nên chặn các gói tin này để đảm bảo an tòa cho hệ thống. Không chấp nhận các gói tin [ICMP redirect](https://askubuntu.com/questions/118273/what-are-icmp-redirects-and-should-they-be-blocked) trên các card mạng.  
_net.ipv4.conf.default.accept_redirects = 0_ | Mặc định không chấp nhận các gói tin ICMP redirect trên các card mạng gắn thêm sau này.  
_net.ipv4.conf.all.send_redirects = 0_ | Không gởi các gói tin ICMP redirect trên toàn bộ card mạng (có thể bật tính năng này nếu server nhận vai trò là router)  
_net.ipv4.conf.default.send_redirects = 0_ | Mặc định không cho phép các card mạng gắn thêm sau này được gởi gói tin ICMP redirect.  
_**Phòng chống IP spoofing**_  
_net.ipv4.conf.all.rp_filter = 1_ | IP spoofing là kỹ thuật giả mạo source IP của gói tin, thường được dùng trong những cuộc tấn công từ chối dịch vụ. Bật tính năng xác thực địa chỉ IP source của gói tin (Source Address Verification) trên toàn bộ các card mạng. Tính năng này sử dụng cơ chế **_reverse path filtering_** , nếu gói tin nhận được có thể được định tuyến ngược lại qua interface vừa nhận, hệ thống sẽ chấp nhận gói tin này, nếu không thì gói tin này sẽ bị drop.  
_net.ipv4.conf.default.rp_filter = 1_ | Mặc định là bật tính năng xác thực địa chỉ IP source của gói tin trên các card mạng được gắn thêm sau này.  
**Ghi lại log của các gói tin spoof IP, source routing, redirect**  
_net.ipv4.conf.all.log_martians = 1_ | Ghi lại log các gói tin lạ trên toàn bộ card mạng.  
_net.ipv4.conf.default.log_martians = 1_ | Mặc định ghi lại log các gói tin lạ trên các card mạng được gắn thêm sau này.  
**_Giảm thời gian chờ mặc định của trạng thái tcp_fin_timeout_**  
_net.ipv4.tcp_fin_timeout = 5_ | Giảm thời gian chờ ở trạng thái [FIN-WAIT-2](http://httpd.apache.org/docs/2.0/misc/fin_wait_2.html). Mặc định là 60s.  
**Giảm thời gian chờ của cơ chế keep alive**  
_net.ipv4.tcp_keepalive_time = 300_ |  TCP keep alive là cơ chế xác định các TCP connection còn hoạt động hay không. Giá trị này mặc định là 7200s (2 giờ), nếu connection không có bất cứ hoạt động nào thì socket sẽ chờ trong thời gian **_tcp_keepalive_time_** trước khi gởi đi lần lượt gởi đi 5 gói tin giữ kết nối, mỗi gói tin cách nhau 15s. Tổng cộng lại, ứng dụng sẽ biết được một kết nối TCP có còn hoạt động hay không sau 375s (300s + 15s + 15s + 15s + 15s + 15s)  
_net.ipv4.tcp_keepalive_probes = 5_  
_net.ipv4.tcp_keepalive_intvl = 15_  
_**Cấu hình ICMP**_  
_net.ipv4.icmp_echo_ignore_all = 1_ | Không nhận các gói tin ICMP ping. Tùy vào nhu cầu cụ thể mà ta nên **_bật/tắt_** tùy chọn này.  
_net.ipv4.icmp_echo_ignore_broadcasts = 1_ | Không trả lời các gói tin ICMP broadcast/multicast để phòng tránh [Smurf Attack](https://en.wikipedia.org/wiki/Smurf_attack)  
_net.ipv4.icmp_ignore_bogus_error_responses = 1_ | Không ghi log các gói tin ICMP bị lỗi/sai cú pháp  
**_Một số cấu hình khác_**  
_net.ipv4.conf.all.proxy_arp = 0_ | Tắt tính năng [Proxy ARP](https://en.wikipedia.org/wiki/Proxy_ARP)  
_net.ipv4.ip_local_port_range = 16384 65535_ | Xác định dãy port local để server dùng khi cần khởi tạo **_thật nhiều_** nhiều kết nối ra ngoài.  
_net.ipv4.tcp_rfc1337 = 1_ | Sửa lỗi “Time-Wait Assassination” của TCP theo [RFC1337](https://tools.ietf.org/html/rfc1337)  
  
## Filesystem Tuning

Tối ưu các thông số về filesystem, thường được dùng cho các hệ thống web server, database, log server…

_**Tên thông số**_ | _**Ý nghĩa**_  
---|---  
_fs.file-max = 300000_ | Tăng số lượng file handler, cần thiết cho những hệ thống sử dụng nhiều file như web server, database, log…  
  
## Memory Tuning

Các thông số dưới đây dùng để tối ưu bộ nhớ (virtual memory) của hệ thống

_**Tên thông số**_ | _**Ý nghĩa**_  
---|---  
_vm.swappiness = 10_ | Cho kernel biết khi nào thì nên dùng swap memory. Giá trị càng cao thì hệ thống sẽ thường xuyên dùng bộ nhớ swap. VD: Giá trị 10 có nghĩa là nếu dung lượng ram trống còn dưới 10%, hệ thống sẽ chuyển sang dùng swap memory.  
_vm.dirty_background_ratio = 5_ | Thông số này là số phần trăm bộ nhớ RAM dùng để chứa **_dirty page_** (những trang nhớ được cache lại trước khi ghi xuống đĩa cứng vật lý). Khi số **_dirty page_** vượt quá giá trị này, các tiến trình chạy ngầm như **_pdflush/flush/kdmflush_** sẽ bắt đầu ghi các giá trị cache này xuống đĩa cứng. Giá trị ở đây là 5 có nghĩa là 5% tổng số RAM của hệ thống, nếu hệ thống có 8GB RAM thì sẽ có 5% * 8GB tức là 400MB RAM chứa **_dirty page_** trước khi các tiến trình chạy ngầm ghi bớt dữ liệu xuống ổ đĩa. (Thực hiện chạy ngầm I/O vẫn nhận tín hiệu mới luôn nhỏ hơn giá trị**_vm.dirty_ratio_**)  
_vm.dirty_ratio = 10_ | Phần trăm bộ nhớ RAM tối đa để chứa các dirty page trước khi ghi toàn bộ các dữ liệu này xuống đĩa cứng. Khi lượng dữ liệu đạt mức này, toàn bộ các thao tác**I/O mới sẽ tạm thời bị block** cho tới khi toàn bộ các dirty page được các process lưu lại an toàn dưới ổ cứng.  
_vm.overcommit_memory = 0_ | Ta có thể đặt 3 giá trị cho tham số này: 

  * Với cờ là 0, mỗi khi ứng dụng yêu cầu thêm bộ nhớ, kernel sẽ ước tính lượng bộ nhớ trống còn lại trước khi cấp phát.
  * Với cờ là 1, kernel sẽ luôn cấp phát thêm bộ nhớ khi ứng dụng yêu cầu.
  * Với cờ là 2, kernel sẽ không bao giờ **_overcommit_** bộ nhớ.

Trong nhiều trường hợp, tính năng này sẽ rất hữu dụng vì sẽ có nhiều ứng dụng yêu cầu cấp lượng bộ nhớ nhiều hơn cần thiết.  
_vm.overcommit_ratio = 50_ | Nếu cờ của **_vm.overcommit_memory_** là 2 thì tổng bộ nhớ mà kernel được cấp phát cho các ứng dụng là: **_Swap Space + vm.overcommit_ratio * RAM Memory_**  
  
## Kernel Hardening

Mục này chứa các thông số cấu hình kernel nhằm ngăn chặn khai thác các lỗi về buffer, stack, heap…và tối ưu một số thông số trong kernel.

_**Tên thông số**_ | _**Ý nghĩa**_  
---|---  
_kernel.exec-shield = 1_ | Bật tính năng [Exec Shield](https://en.wikipedia.org/wiki/Exec_Shield) để phòng chống mã độc khai thác lỗi về buffer, stack, heap… Trong phiên bản CentOS7/RHEL7, tính năng này đã được bật tự động, không thể cấu hình lại.  
_kernel.randomize_va_space = 2_ | Bật tính năng [_Address space randomization_](https://access.redhat.com/solutions/44460) để ngăn ngừa nhiều loại tấn công của mã độc.  
_kernel.pid_max = 4194303_ | Tăng giới hạn process cho toàn hệ thống.  
  
## IPv6

Tắt tính năng tự động cấu hình IPv6 trên các card mạng.

_**Tên thông số**_ | _**Ý nghĩa**_  
---|---  
_net.ipv6.conf.all.autoconf=0_ | Không tự động cấu hình IPv6 trên các card mạng.  
_net.ipv6.conf.all.accept_ra=0_  
_net.ipv6.conf.default.autoconf=0_  
_net.ipv6.conf.default.accept_ra=0_  
  
Coi thêm những bài viết liên quan:

  * [Hướng dẫn cấu hình max file descriptor](https://cloudcraft.info/huong-dan-cau-hinh-max-file-descriptor/)
  * [Hướng dẫn upgrade linux kernel](https://cloudcraft.info/huong-dan-upgrade-kernel-linux/)



# Tham khảo

<https://en.wikipedia.org/wiki/Sysctl> <https://www.tecmint.com/change-modify-linux-kernel-runtime-parameters/> <https://klaver.it/linux/sysctl.conf> <https://wiki.archlinux.org/index.php/Sysctl> <https://www.kernel.org/doc/Documentation/sysctl/>
