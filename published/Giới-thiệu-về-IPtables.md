---
title: "Giới thiệu về IPtables"
date: 2017-11-29 17:29:07
categories: [Linux, Security]
---

IPtables là một ứng dụng rất phổ biến trên nhiều hệ điều hành Linux, iptables cho phép người quản trị Linux cấu hình cho phép/chặn luồng dữ liệu đi qua mạng. IPtables có thể đọc, thay đổi, chuyển hướng hoặc hủy các gói tin đi tới/đi ra dựa trên các tables, chains và rules. Mỗi một table sẽ có nhiều chain chứa các rule khác nhau quyết định cách thức xử lý gói tin (dựa trên giao thức, địa chỉ nguồn, đích….). Hiện có nhiều phiên bản khác nhau của iptables dùng cho các giao thức khác nhau như: iptables (IPv4), ip6tables (IPv6), arptables (ARP) và ebtables (Ethernet frame). Một phiên bản mới của iptables là nftables, nftables được hỗ trợ từ bản kernel 3.13. Để đi sâu vào cách thức hoạt động của Iptables, ta cần phải hiểu rõ về các khái niệm như table, chain và rule. Vẫn là cheat-sheet cho bạn nào lười đọc^^: [GitHub](https://github.com/cloudcraftteam/System-Engineer-Cheat-Sheets/blob/master/Firewall/IPtables_basic.txt)

## Các bảng trong IPtable

IPtable gồm có 5 bảng với với mục đích và thứ tự xử lý khác nhau. Thứ tự xử lý các gói tin được mô tả cơ bản trong bảng sau: **Filter Table** Filter là bảng được dùng nhiều nhất trong IPtables. Bảng này dùng để quyết định xem có nên cho một gói tin tiếp tục đi tới đích hoặc chặn gói tin này lại (lọc gói tin). Đây là chức năng chính yếu nhất của IPtables. **NAT Table** Bảng NAT được dùng để NAT (Network Address Translation – Phiên dịch địa chỉ mạng), khi các gói tin đi vào bảng này, gói tin sẽ được kiểm tra xem có cần thay đổi và sẽ thay đổi địa chỉ nguồn, đích của gói tin như thế nào. Bảng này được sử dụng khi có một gói tin từ một connection mới gởi đến hệ thống, các gói tin tiếp theo của connection này sẽ được áp rule và xử lý tương tự như gói tin đầu tiên mà không cần phải đi qua bảng NAT nữa. ![](http://cloudcraft.info/wp-content/uploads/2017/11/gioi-thieu-ve-iptables-1.png)

_Sơ đồ xử lý gói tin cơ bản qua 2 bảng NAT và FILTER_

**Mangle Table** Bảng mangle dùng để điều chỉnh một số trường trong IP header như TTL (Time to Live), TOS (Type of Serivce) dùng để quản lý chât lượng dịch vụ (Quality of Serivce)… hoặc dùng để đánh dấu các gói tin để xử lý thêm trong các bảng khác. **Raw Table** Theo mặc định, iptables sẽ lưu lại trạng thái kết nối của các gói tin, tính năng này cho phép iptables xem các gói tin rời rạc là một kết nối, một session chung để dễ dàng quản lý. Tính năng theo dõi này được sử dụng ngay từ khi gói tin được gởi tới hệ thống trong bảng raw. Với bảng raw, ta có thể bật/tắt tính năng theo dõi này đối với một số gói tin nhất định, các gói tin được đánh dấu NOTRACK sẽ không được ghi lại trong bảng connection tracking nữa. **Security Table** Bảng security dùng để đánh dấu policy của SELinux lên các gói tin, các dấu này sẽ ảnh hưởng đến cách thức xử lý của SELinux hoặc của các máy khác trong hệ thống có áp dụng SELinux. Bảng này có thể đánh dấu theo từng gói tin hoặc theo từng kết nối. 

## Các chain trong table

Mỗi một table đều có một số chain của riêng mình, sau đây là bảng cho biết các chain thuộc mỗi table  **Tables/Chain** | **PREROUTING** | **INPUT** | **FORWARD** | **OUTPUT** | **POSTROUTING**  
---|---|---|---|---|---  
**_raw_** | ✓ |  |  | ✓ |   
**_mangle_** | ✓ | ✓ | ✓ | ✓ | ✓  
**_nat (DNAT)_** | ✓ |  |  | ✓ |   
**_filter_** |  | ✓ | ✓ | ✓ |   
**_security_** |  | ✓ | ✓ | ✓ |   
**_nat (SNAT)_** |  | ✓ |  |  | ✓  
  
_Các chain có trong từng table_

Giới thiệu về các chain: 

  * **INPUT** – Chain này dùng để kiểm soát hành vi của những các kết nối tới máy chủ. Ví dụ một user cần kết nối SSH và máy chủ, iptables sẽ xét xem IP và port của user này có phù hợp với một rule trong chain INPUT hay ko.
  * **FORWARD** – Chain này được dùng cho các kết nối chuyển tiếp sang một máy chủ khác (tương tự như router, thông tin gởi tới router sẽ được forward đi nơi khác). Ta chỉ cần định tuyến hoặc NAT một vài kết nối (cần phải forward dữ liệu) thì ta mới cần tới chain này.
  * **OUTPUT** – Chain này sẽ xử lý các kết nối đi ra ngoài. Ví dụ như khi ta truy cập google.com, chain này sẽ kiểm tra xem có rules nào liên quan tới http, https và google.com hay không trước khi quyết định cho phép hoặc chặn kết nối.
  * **PREROUTING** –Header của gói tin sẽ được chỉnh sửa tại đây trước khi việc routing được diễn ra.
  * **POSTROUTING** – Header của gói tin sẽ được chỉnh sửa tại đây sau khi việc routing được diễn ra.

Mặc định thì các chain này sẽ không chứa bất kỳ một rule nào, tuy nhiên mỗi chain đều có một policy mặc định nằm ở cuối chain, policy này có thể là **ACCEPT** hoặc **DROP** , chỉ khi gói tin đã đi qua hết tất cả các rule ở trên thì gói tin mới gặp phải policy này. Ngoài ra, thứ tự gói tin di chuyển giữa các chain sẽ có hơi khác tùy vào tình huống: 

  * Gói tin được gởi đến máy chủ: **_PREROUTING = > INPUT_**
  * Gói tin được forward đến một máy chủ khác: **_PREROUTING = > FORWARD => POSTROUTING_**
  * Gói tin được máy chủ hiện tại gởi ra ngoài: **_OUTPUT = > POSTROUTING_**

_![](https://cloudcraft.info/wp-content/uploads/2017/11/gioi-thieu-ve-iptables-2.png.jpg)_

_Đường đi của gói tin qua các bảng và chain trong IPtables_

## Các rule trong chain

Các rule là tập điều kiện và hành động tương ứng để xử lý gói tin (ví dụ ta sẽ tạo một rule chặn giao thức FTP, drop toàn bộ các gói tin FTP được gởi đến máy chủ). Mỗi chain sẽ chứa rất nhiều rule, gói tin được xử lý trong một chain sẽ được so với lần lượt từng rule trong chain này. Cơ chế kiểm tra gói tin dựa trên rule vô cùng linh hoạt và có thể dễ dàng mở rộng thêm nhờ các extension của IPtables có sẵn trên hệ thống. Rule có thể dựa trên protocol, địa chỉ nguồn/đích, port nguồn/đích, card mạng, header gói tin, trạng thái kết nối… Dựa trên những điều kiện này, ta có thể tạo ra một tập rule phức tạp để kiểm soát luồng dữ liệu ra vào hệ thống. Mỗi rule sẽ đươc gắn một hành động để xử lý gói tin, hành động này có thể là: 

  * **_ACCEPT_** : gói tin sẽ được chuyển tiếp sang bảng kế tiếp.
  * **_DROP_** : gói tin/kết nối sẽ bị hủy, hệ thống sẽ không thực thi bất kỳ lệnh nào khác.
  * **_REJECT_** : gói tin sẽ bị hủy, hệ thống sẽ gởi lại 1 gói tin báo lỗi ICMP – Destination port unreachable
  * **_LOG_** : gói tin khớp với rule sẽ được ghi log lại.
  * **_REDIRECT_** : chuyển hướng gói tin sang một proxy khác.
  * **_MIRROR_** : hoán đổi địa chỉ IP nguồn, đích của gói tin trước khi gởi gói tin này đi.
  * **_QUEUE_** : chuyển gói tin tới chương trình của người dùng qua một module của kernel.



## Các trạng thái của kết nối

Đây là những trạng thái mà hệ thống connection tracking (module conntrack của IPtables) theo dõi trạng thái của các kết nối: 

  * **NEW** : Khi có một gói tin mới được gởi tới và không nằm trong bất kỳ connection nào hiện có, hệ thống sẽ khởi tạo một kết nối mới và gắn nhãn NEW cho kết nối này. Nhãn này dùng cho cả TCP và UDP.
  * **ESTABLISHED** : Kết nối được chuyển từ trạng thái _**NEW**_ sang _**ESTABLISHED**_ khi máy chủ nhận được phản hồi từ bên kia.
  * **RELATED** : Gói tin được gởi tới không thuộc về một kết nối hiện có nhưng có liên quan đến một kết nối đang có trên hệ thống. Đây có thể là một kết nối phụ hỗ trợ cho kết nối chính, ví dụ như giao thức FTP có kết nối chính dùng để chuyển lệnh và kết nối phụ dùng để truyền dữ liệu.
  * **INVALID** : Gói tin được đánh dấu _**INVALID**_ khi gói tin này không có bất cứ quan hệ gì với các kết nối đang có sẵn, không thích hợp để khởi tạo một kết nối mới hoặc đơn giản là không thể xác định được gói tin này, không tìm được kết quả trong bảng định tuyến.
  * **UNTRACKED** : Gói tin có thể được gắn hãn _**UNTRACKED**_ nếu gói tin này đi qua bảng raw và được xác định là không cần theo dõi gói này trong bảng connection tracking.
  * **SNAT** : Trạng thái này được gán cho các gói tin mà địa chỉ nguồn đã bị NAT, được dùng bởi hệ thống connection tracking để biết khi nào cần thay đổi lại địa chỉ cho các gói tin trả về.
  * **DNAT** : Trạng thái này được gán cho các gói tin mà địa chỉ đích đã bị NAT, được dùng bởi hệ thống connection tracking để biết khi nào cần thay đổi lại địa chỉ cho các gói tin gởi đi.

Các trạng thái này giúp người quản trị tạo ra những rule cụ thể và an toàn hơn cho hệ thống. 

# Cấu hình IPtables cơ bản

Các lệnh cơ bản của iptables

**Option** | **Mô tả**  
---|---  
**-A chain rule** | Thêm một rule vào chain  
**-D chain rule** | Xóa một rule khỏi chain  
**-F [chain]** | Xóa tất cả các rule thuộc một chain (hoặc mọi chain)  
**-I chain index rule** | Chèn rule mới vào chain tại vị trí có giá trị là index  
**-L [chain]** | Liệt kêt toàn bộ rule của một chain (hoặc mọi chain)  
**-P chain target** | Đặt policy mặc định cho chain đó  
**-R chain index rule** | Thay một rule của chain tại ví trí có giá trị là index  
**-S [chain]** | Liệt kê nội dung chi tiết của các rule thuộc một chain (hoặc mọi chain)  
**-t table** |  Chọn table để áp rule  
  
Các cờ cấu hình rule cho iptables

**Option** | **Mô tả**  
---|---  
**-d address** | Địa chỉ đích  
**-g chain** | Chuyển sang một chain mới  
**-i name** | Interface nhập  
**-j target** | Hành động sẽ làm khi gói tin khớp rule (tức là khớp toàn bộ các điều kiện của 1 rule)  
**-o name** | Interface xuất  
**-p protocol** | Giao thức: tcp, udp, icmp, ftp, dns…  
**-s address** | Địa chỉ nguồn  
**\--dport** | Port đích  
**\--sport** | Port nguồn  
**!** | Phủ định 1 mệnh đề  
**\--state** | Khớp với một tập các trạng thái của kết nối (ESTABLISHED, RELATED….)  
**\--string** | Khớp với một chuỗi của dữ liệu ở tần ứng dụng (layer 7 – application layer)  
  
Sau đây là một mẫu cấu hình iptables cơ bản cho các bạn tham khảo:
    
    
    ###  Allow INBOUND connections ###
    
    ## Rules are evaluated in order, put busiet rules at the front!! ##
    ## Accept all traffic to the looback interface, ##
    ## which is necessary for many applications and services ##
    iptables -A INPUT -i lo -j ACCEPT
    
    ### Stateful table ###
    ## Allow traffic from existing connections or new connection related to these connections ##
    iptables -A INPUT -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT
    
    ## Block invalid packets ##
    iptables -A INPUT -m conntrack --ctstate INVALID -j DROP
    
    ## Allow inbound port 22, 80, 443 ##
    iptables -A INPUT -i <input_interface> -d <server_IP> -p tcp --dport 80 -j ACCEPT
    iptables -A INPUT -i <input_interface> -d <server_IP> -p tcp --dport 443 -j ACCEPT
    iptables -A INPUT -i <input_interface> -d <server_IP> -p tcp --dport 22 -j ACCEPT
    
    ## Block dropped packets ##
    #iptables -A INPUT -j LOG --log-prefix "IPTables-Dropped: "
    
    ## Allow DNS server (UDP/TCP) return result ##
    ## If use stateless table, enable the two below ##
    #iptables -A INPUT -i <input_interface> -p tcp --sport 53 -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT
    #iptables -A INPUT -i <input_interface> -p udp --sport 53 -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT
    
    ## Allow NTP Server return result ##
    iptables -A INPUT -p udp --sport 123 -j ACCEPT
    
    ## Except the listed above, other connections will be dropped ##
    iptables -t filter -P INPUT DROP
    --------------------------------------
    
    ###  Allow OUTBOUND connections ###
    ## Accept all traffic to the looback interface, ##
    ## which is necessary for many applications and services ##
    iptables -A OUTPUT -o lo -j ACCEPT
    
    ## Allow Established outgoing connections ##
    iptables -A OUTPUT -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT
    
    ## Allow outbound SSH, Web Traffic ##
    iptables -A OUTPUT -o <output_interface> -p tcp --sport 80 -j ACCEPT
    iptables -A OUTPUT -o <output_interface> -p tcp --sport 443 -j ACCEPT
    iptables -A OUTPUT -o <output_interface> -p tcp --sport 22 -j ACCEPT
    
    ## Allow HTTP/HTTPS traffic to other server (yum install) ##
    iptables -A OUTPUT -o <output_interface> -p tcp --dport 80 -j ACCEPT
    iptables -A OUTPUT -o <output_interface> -p tcp --dport 443 -j ACCEPT
    
    ## Allow DNS (TCP/UDP port 53), NTP (port 123) ##
    iptables -A OUTPUT -p udp --dport 53 -j ACCEPT
    iptables -A OUTPUT -p udp --dport 123 -j ACCEPT
    
    ## Block dropped packets ##
    #iptables -A OUTPUT -j LOG --log-prefix "IPTables-Dropped: "
    
    ## Except the listed above, other connections will be dropped ##
    iptables -t filter -P OUTPUT DROP

# Tham khảo

Dataflow hoàn chỉnh của IPtables: [Wikipedia](https://upload.wikimedia.org/wikipedia/commons/3/37/Netfilter-packet-flow.svg) <http://ipset.netfilter.org/iptables.man.html> <https://wiki.archlinux.org/index.php/iptables> <https://www.digitalocean.com/community/tutorials/a-deep-dive-into-iptables-and-netfilter-architecture> <https://wiki.archlinux.org/index.php/Simple_stateful_firewall>
