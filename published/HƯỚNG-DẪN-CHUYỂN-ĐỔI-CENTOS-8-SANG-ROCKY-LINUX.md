---
title: "HƯỚNG DẪN CHUYỂN ĐỔI CENTOS 8 SANG ROCKY LINUX"
date: 2021-12-16 15:36:17
categories: [Uncategorized, Linux]
---

Như các bạn cũng đã biết, bản phân phối CentOS 8 sẽ kết thúc vòng đời của nó vào ngày 31/12/2021 (theo trang centos.org <https://www.centos.org/news-and-events/1322-october-centos-dojo-videos>). Điều này khiến một số quản trị viên đang sử dụng CentOS 8 phải quay xe để đảm bảo an toàn cho hệ thống của họ. Để chuyển đổi CentOS 8 sang một bản phân phối khác, thì bản phân phối đó cần phải tương thích 100% với RHEL, ví dụ như Rocky Linux hoặc AlmaLinux. Hôm nay mình sẽ hướng dẫn các bạn chuyển đổi CentOS 8 sang Rocky Linux. **Bước 1:** Backup Centos 8 

  * Để đảm bảo an toàn, trước khi chuyển đổi bản phân phối, các bạn cần backup lại toàn bộ hệ thống.

**Bước 2:** Vì Rooky Linux hiện chỉ đang hỗ trợ CentOS version 8.5, nên các bạn cần phải kiểm tra và update hệ thống lên bản mới nhất. 

  * Kiểm tra phiên bản CentOS hiện tại.


    
    
    # cat /etc/centos-release

![](https://cloudcraft.info/wp-content/uploads/2021/12/rockylinux_2-300x63.png)

  * Update hệ thống lên CentOS 8.5


    
    
    # dnf -y update

  * Sau khi update, các bạn reboot lại máy chủ và kiểm tra phiên bản.


    
    
    # reboot
    # cat /etc/centos-release

![](https://cloudcraft.info/wp-content/uploads/2021/12/rockylinux_3-300x76.png) **Bước 3** : Download rocky-tool. 

  * Cài đặt phần mềm git để download rocky-tool.


    
    
    # dnf -y install git

![](https://cloudcraft.info/wp-content/uploads/2021/12/rockylinux_4-300x142.png)

  * Download rocky-tool.


    
    
    # cd /tmp/
    # git clone https://github.com/rocky-linux/rocky-tools.git

![](https://cloudcraft.info/wp-content/uploads/2021/12/rockylinux_5-300x98.png) **Bước 4:** Chuyển đổi CentOS 8 sang Rocky Linux 

  * Phân quyền thực thi cho rocky-tool.


    
    
    # cd /tmp/rocky-tools/migrate2rocky
    # chmod -v +x migrate2rocky.sh

![](https://cloudcraft.info/wp-content/uploads/2021/12/rockylinux_6-300x80.png)

  * Thực hiện chuyển đổi CentOS 8 sang Rocky Linux.


    
    
    # ./migrate2rocky.sh -r

![](https://cloudcraft.info/wp-content/uploads/2021/12/rockylinux_7-300x131.png)

  * Quá trình chuyển đổi sau khi hoàn thành sẽ xuất hiện các dòng sau.


    
    
    Done, please reboot your system.
    A log of this installation can be found at /var/log/migrate2rocky.log

  * Các bạn reboot lại máy chủ.

**Bước 4:** Kiểm tra lại phiên bản và các dịch vụ đang chạy 

  * Kiểm tra phiên bản Rocky Linux


    
    
    # cat /etc/centos-release
    # cat /etc/rocky-release

![](https://cloudcraft.info/wp-content/uploads/2021/12/rockylinux_9-300x61.png)
