---
title: "Hướng dẫn tự động start dịch vụ trên Linux - Giới thiệu về các Init System (Phần 1)"
date: 2019-03-19 11:00:40
categories: [Linux]
---

# Hướng dẫn tự động start dịch vụ trên Linux Phần 1 - Giới thiệu về các Init System

Đối với các SysAdmin, SysEngineer, DevOps thì việc quản trị các dịch vụ như web, mail, application, database... là những công việc quen thuộc. Mối quan tâm chính của người quản trị khi làm việc với các dịch vụ này là:

  * Đảm bảo các dịch vụ này luôn chạy.
  * ~~Service có chết cũng phải cắn răng cắn cỏ nắm đầu nó lên chạy tiếp~~.
  * Cần phải có một cách để tự động start các dịch vụ này lên khi bị crash hoặc khi reboot server



Có khá nhiều cách để làm được việc này, chẳng hạn như viết script check services còn sống hay không. Hoặc sử dụng một phần mềm monitor trạng thái các dịch vụ là monit, supervisor...

![](https://cloudcraft.info/wp-content/uploads/2019/03/InitSystem.png)

Trong loạt bài này, mình sẽ hướng dẫn các bạn sử dụng các init system sẵn có của Linux để quản lý các dịch vụ, tiến trình tự động reboot khi gặp sự cố ~~mỗi lần die service mà lên start tay thì lười lắm.~~

Ở bài đầu tiên thì mình sẽ giới thiệu sơ lược về các runlevel trên hệ thống và các loại Init System hay gặp cũng như một số đặc điểm cơ bản của các hệ thống này.

## Giới thiệu một số Init System

Trên các hệ điều hành linux, tùy thuộc vào phiên bản, distro mà sử dụng các init system khác nhau, bạn có thể tham khảo một số init system mặc định được các hệ điều hành linux sử dụng bên dưới:

### **System V**

System V (Đọc là System Five) được phát triển bởi AT&T và được ra mắt lần đầu tiên vào năm 1983, do khá cổ và còn nhiều điểm không hoàn thiện nên SysV đang dần được thay thế bởi các Init System khác

Một số Linux OS sử dụng System V: 

  * Debian 6 và trước nữa
  * Ubuntu 9.04 và trước đó nữa
  * CentOS 5 trở về trước



### **Upstart**

Upstart là một init system được phát triển bởi những người đã tạo nên Ubuntu nhằm thay thế cho SysV trên các distro Ubuntu. Upstart có nhiệm vụ khởi chạy các tiến trình và tác vụ khác nhau, kiểm tra các process đang hoạt động và stop những process này khi shutdown hệ thống.

**Homepage** : <http://upstart.ubuntu.com/index.html> Một số distro hỗ trợ Upstart (đa phần là Ubuntu vì Upstart vốn được phát triển cho riêng Ubuntu) 

  * Từ Ubuntu 9.10 tới Ubuntu 14.04, 14.10
  * CentOS 6



### Systemd

**Systemd** là init system tiêu chuẩn (de facto) cho các distro được release gần đây như CentOS 7, Ubuntu 16, 18...

Systemd là một init system quản lý các tiến trình trên Linux, chữ **'d'** ở cuối có nghĩa là trình daemon. Tương tự như init, systemd là process của tất cả các process khác trong một hệ thống linux, systemd là process đầu tiên được start lên khi boot và có **pid là 1**.

**systemd** được thiết kế khắc phục những khuyết điểm của init. Ví dụ như một tiến trình của init sẽ khởi động **tuần tự** , từng tiến trình một sẽ được start lên và đưa vô bộ nhớ. Do hoạt động theo cơ chế này nên thời gian boot hệ thống sẽ rất lâu. Trái ngược với init, systemd được thiết kế để khởi động tác tác vụ song song, vì vậy giảm thiểu được boot time và tài nguyên tính toán. Ngoài ra, systemd còn có nhiều tính năng hơn so với init, cụ thể như:

  * Khả năng xử lý các tiến trình song song.
  * Dùng socker và D-Bus để khởi chạy các dịch vụ.
  * Khởi tạo các trình daemon theo nhu cầu, quản lý các process sử dụng Linux cgroups.
  * Hỗ trợ snapshot và restore trạng thái của hệ thống.
  * Có khả năng giữ các mount point và auto mount point.
  * Có cơ chế kiểm soát dependency của service chặt chẽ.



Lệnh `systemctl` là công cụ chính dùng để quản lý systemd. Lệnh này kết hợp cả 2 lệnh `service` và `chkconfig` thành 1 tool duy nhất để có thể quản lý hiệu quả các dịch vụ trên hệ thống.

## Hướng dẫn kiểm tra init system của hệ thống

Để biết hệ thống của mình hiện đang hỗ trợ những init system nào, ta có 2 cách 

### **Cách 1**

Kiểm tra xem các folder sau có tồn tại không: 

  * Nếu có `/usr/lib/systemd` thì hệ thống có hỗ trợ Systemd
  * Nếu có `/usr/share/upstart` thì hệ thống có hỗ trợ Upstart
  * Nếu có `/etc/init.d` thì hệ thống có hỗ trợ System V


    
    
    root@ubuntu:~# ls /usr/lib/systemd/
    boot  catalog  network  user  user-generators
    
    root@ubuntu:~# ls /usr/share/upstart/
    sessions
    
    root@ubuntu:~# ls /etc/init.d/
    apparmor                cron         hwclock.sh      mountall-bootclean.sh  skeleton      umountroot
    bootmisc.sh             dbus         irqbalance      mountall.sh            ssh           urandom
    checkfs.sh              grub-common  keyboard-setup  mountdevsubfs.sh       udev          uuidd
    checkroot-bootclean.sh  halt         killprocs       mountkernfs.sh         ufw           x11-common
    checkroot.sh            hostname.sh  kmod            mountnfs-bootclean.sh  sendsigs      umountfs
    console-setup           httpd        memcached       mountnfs.sh            single        umountnfs.sh
    

==> Như mình check trên 1 con Ubuntu 16.04 thì nó có cả 3 thằng này luôn <3, tức là hỗ trợ cả 3 loại, nhưng dùng chính vẫn là _**systemd**_.

### **Cách 2**

Vì init process luôn có PID là 1, ta có thể dùng lệnh sau để kiểm tra hệ thống của mình đang dùng init system nào chính: 
    
    
    ps -p 1

Đây là những init system cho phép hệ thống theo dõi và auto-start lại các dịch vụ bị crash. Trong loạt bài viết này, mình sẽ tập trung vào Upstart và Systemd. System V thì cũ quá rồi nên mình sẽ không nói tới

## Giới thiệu về Runlevel

Để hiểu một chút về cơ chế hoạt động của chúng thì ta cần phải nắm được kiến thức về các runlevel cơ bản trong Linux.

Runlevel là biểu thị một trạng thái trong Linux, mỗi runlevel sẽ biểu thị cho một trạng thái riêng của Linux server như shutdown, single-user mode, restart mode, mỗi một con số sẽ biểu thị một runlevel riêng

Các số runlevel này chạy từ 0 tới 6 và có ý nghĩa như sau:

  * **Runlevel 0:** Shutdown hệ thống
  * **Runlevel 1:** Chế độ rescue mode, 1 user
  * **Runlevels 2, 3, 4:** Chế độ nhiều user, có kết nối mạng, giao diện CLI
  * **Runlevel 5:** Chế độ nhiều user, có kết nối mạng, giao diện đồ họa GUI.
  * **Runlevel 6:** Reboot hệ thống



Để kiểm tra runlevel đang chạy, ta dùng lệnh `runlevel` hoặc _**who -r**_

Còn đối với những hệ thống có hỗ trợ systemd thì ta có thể dùng lệnh _**systemctl get-default**_

Ở bài này, mình đã giới thiệu sơ lược đến các bạn về các init system và run level trong linux. Ở những phần sau, mình sẽ hướng dẫn các bạn cách thức sử dụng Upstart và Systemd một cách chi tiết.

## Tham khảo

<https://docs.fedoraproject.org/en-US/quick-docs/understanding-and-administering-systemd/index.html> <https://www.tecmint.com/systemd-replaces-init-in-linux/>
