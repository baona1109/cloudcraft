---
title: "Hướng dẫn cấu hình NTP"
date: 2018-10-17 14:19:46
categories: [Windows, Linux]
---

NTP (Network Time Protocol) là một giao thức dùng để đồng bộ hóa thời gian giữa các máy chủ với nhau, đặc biệt là các hệ thống tài chính, dịch vụ, khoa học… thì nhu cầu này đặc biệt quan trọng.

## Mô hình

Giao thức NTP phân chia các time server ra thành nhiều lớp (stratum), số stratum càng nhỏ thì thời gian càng chính xác (stratum nhỏ nhất là 0). Các server có stratum bằng 0 sẽ cung cấp thời gian chuẩn cho các client có stratum là 1, các client không thể truy cập vượt cấp stratum của mình (stratum 4 chỉ có thể truy cập stratum 3 chứ không thể lấy thời gian từ stratum 2).

_![](https://cloudcraft.info/wp-content/uploads/2018/10/huong-dan-cau-hinh-ntp-1.jpg)_

_Sơ đồ phân lớp các NTP Server_

NTP hoạt động bằng cách đo thời gian RTT (round trip time) của các gói tin trao đổi giữa client và server. Client sẽ điều chinh lại thời gian trên máy mình để đồng bộ thời gian với server. Thời gian điều chỉnh sẽ dựa trên hai thông số là timestamp khi gói tin được gởi đi và thời gian di chuyển của gói tin.

Chính vì vậy, ta nên chọn time server có độ delay thấp nhất có thể. Ví dụ như ở VN thì ta có thể chọn time server nằm trong **_vn.pool.ntp.org_**

Quy trình cơ bản khi cấu hình NTP Server là:

  1. NTP Server A lấy giờ chuẩn từ 1 server khác có level stratum cao hơn (từ _**pool.ntp.org**_)
  2. Các NTP Client khác sẽ lấy giờ từ NTP Server chuẩn này.



## Cấu hình

### **NTP Server**

Để cấu hình và cài đặt NTP Server (gói ntpd) trên CentOS 7, ta thực hiện những bước sau:

Cài đặt và cấu hình NTP server, cho phép các server trong subnet 10.0.0.0/24 truy cập được tới NTP server
    
    
    yum –y install ntp
    
    vi /etc/ntp.conf
    
    […]
    
    restrict 10.0.0.0 mask 255.255.255.0 nomodify notrap
    
    server vn.pool.ntp.org iburst

Start service và enable tính năng auto start cho ntpd 
    
    
    systemctl start ntpd
    
    systemctl enable ntpd

Kiểm tra NTP Server đã lấy được giờ chuẩn chưa 
    
    
    ntpq -p

  ![NTP 1](https://cloudcraft.info/wp-content/uploads/2018/10/huong-dan-cau-hinh-ntp-2-1.jpg)

_NTP Server đang lấy giờ từ một server chuẩn 128.9.176.30_

### **NTP Client (Linux)**

Đối với các client Linux lấy giờ từ NTP server, ta có 2 cách

  1. Cài NTP service deamon tương tự như trên, nhưng lấy thời gian từ NTP Server mà ta chỉ định (ví dụ ở đây là 10.0.0.4)
  2. Dùng ntpdate và set crontab sync time mỗi 5p

**Cách 1**

Cài NTP service deamon tương tự như trên Server, nhưng ta sẽ trỏ về địa chỉ IP/domain của server của mình và không nhận request đồng bộ thời gian từ các máy client khác.
    
    
    yum –y install ntp
    
    vi /etc/ntp.conf
    
    […]
    
    #server** <IP_of_your_server>** iburst
    server**10.0.0.4** iburst
    
    restrict default ignore

Start service và enable tính năng auto start cho ntpd 
    
    
    systemctl start ntpd
    
    systemctl enable ntpd

Kiểm tra NTP Client đã lấy được giờ chuẩn chưa 
    
    
    ntpq -p

**Cách 2** Dùng lệnh ntpdate để đồng bộ thời gian + set crontab cho lệnh này check thời gian theo giờ. Đầu tiên, tiến hành cài đặt ntpdate: 
    
    
    yum –y install ntpdate

Cấu hình ntpdate update thời gian từ Vietnam NTP Server pool 
    
    
    ntpdate vn.pool.ntp.org

hoặc chỉ định một IP 
    
    
    ntpdate <IP_of_your_server>

Enable tính năng auto start cho ntpupdate 
    
    
    systemctl enable ntpdate

Cấu hình sync tự động trong crontab mỗi 5p 
    
    
    sudo crontab -e
    
    */5 * * * * /usr/sbin/ntpdate 10.0.0.4 2>&1 | tee -a /var/log/ntpdate.log

### NTP Client (Windows)

Để đồng bộ thời gian trên Windows, ta có 2 cách: 

  1. Dùng giao diện đồ họa
  2. Dùng dòng lệnh

**Cách 1 - Cấu hình bằng giao diện**

Với giao diện đồ họa, ta truy cập vào phần: **_Control Panel = > Date and Time => Internet Time Settings_**

_![NTP 3](https://cloudcraft.info/wp-content/uploads/2018/10/huong-dan-cau-hinh-ntp-4.jpg)_

_Thay đổi giá trị mặc định của hệ thống thành IP/domain name_ _của NTP server mà ta muốn nhận thông tin và chọn_** _Update now_**

**Cách 2 - Giao diện dòng lệnh** Với giao diện dòng lệnh, ta sử dụng những lệnh sau: 
    
    
    #Tạm ngưng time service
    
    net stop w32time
    
    #Cấu hình trỏ tới các NTP server cần dùng
    
    w32tm /config /manualpeerlist:"vn.pool.ntp.org,0x1" /syncfromflags:manual /reliable:yes
    
    #Khởi động lại time service
    
    net start w32time
    
    #Kiểm tra lại thông tin cấu hình
    
    w32tm /query /configuration
    
    w32tm /query /status

_![NTP 4](https://cloudcraft.info/wp-content/uploads/2018/10/huong-dan-cau-hinh-ntp-5.jpg)_

_Cấu hình trỏ NTP bằng dòng lệnh trên Windows Server 2008_

_![NTP 5](https://cloudcraft.info/wp-content/uploads/2018/10/huong-dan-cau-hinh-ntp-6.jpg)_

_Kiểm tra lại các thông tin cấu hình_

## Tham khảo

[https://www.server-world.info/en/note?os=CentOS_7&p=ntp&f=1](https://www.server-world.info/en/note?os=CentOS_7&p=ntp&f=1) [https://www.server-world.info/en/note?os=Windows_Server_2012&p=ntp](https://www.server-world.info/en/note?os=Windows_Server_2012&p=ntp)
