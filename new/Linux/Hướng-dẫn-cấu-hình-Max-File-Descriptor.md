---
title: "Hướng dẫn cấu hình Max File Descriptor"
date: 2018-02-07 13:53:03
categories: [Linux]
---

Khi chạy dịch vụ, phần mềm trên Linux thì các bạn sẽ thường gặp phải lỗi _**"Open File Limit"**_. Lỗi này xảy ra là do hệ thống không cấp đủ file descriptor cho ứng dụng (đặc biệt là DB, Web Server chịu nhiều tải). Vậy file descriptor là gì và làm cách nào để phòng tránh được lỗi này. Ta sẽ cùng tìm hiểu qua bài viết sau nhé.

# Giới thiệu về File Descriptor

Trong các hệ điều hành *nix, file descriptor (FD) là một công cụ dùng để quản lý truy cập file, các thao tác nhập xuất, network socket, library files, file thực thi chương trình, chuột, bàn phím,…. Nói cho đơn giản thì toàn bộ mọi thứ trên *nix điều được biểu diễn dưới dạng file. Và FD là các số nguyên không âm đại diện cho những file này.

Khi ta mở hoặc tạo một file, kernel sẽ trả về giá trị file descriptor cho process tương ứng. Khi ta đóng file đó lại thì file descriptor này sẽ được giải phóng để cấp phát cho những lần mở file sau. Ví dụ. nếu người dùng A mở 10 tập tin để đọc thì sẽ có 10 FD tương ứng (có thể được đánh số lần lượt là 101, 102, 103,…, 110) và các giá trị này sẽ được lưu trong bảng danh sách chứa file descriptors.

Mỗi một process sẽ có một bảng danh sách file descriptor riêng do kernel quản lý, kernel sẽ chuyển danh sách này sang danh sách file table quản lý toàn bộ file được truy cập bởi tất cả các process. File table này sẽ lưu lại chế độ mà file đó đang được sử dụng (đọc, ghi, chèn). Và file table này sẽ được mapping qua một bảng thứ 3 là inode table thật sự quản lý các file nằm bên dưới. Khi một tiến trình muốn đọc hoặc ghi file, tiến trình này sẽ chuyển file descriptor cho kernel xử lý (bằng các lệnh system call) và kernel sẽ truy cập file này thay cho process. Process không thể truy cập trực tiếp các file hoặc inode table

_![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-cau-hinh-max-file-descriptor-01.png)_

_Mapping giữa File Descriptor, File table và Inode table_

# Giới thiệu về ulimit

Trước khi đi vào phần cấu hình thì ta cần phải nắm được một số thuật ngữ cơ bản như sau:

  * **ulimit** quản lý toàn bộ các tài nguyên mà một _**user**_ có thể sử dụng được thông qua shell. Ta có thể dùng lệnh “** _ulimit –a_** ” để liệt kê toàn bộ danh sách các thiết lập hiện có của một user.
  * **Hard limit** là giới hạn sử dụng tài nguyên tối đa mà user root cấp cho một user. User root có thể thay đổi giá trị này trong file _/etc/security/limits.conf._
  * **Soft limit** là giới hạn sử dụng một loại tài nguyên hiện tại của user. Mỗi user có thể tự tăng chính giới hạn này của mình nếu cần thêm tài nguyên, nhưng không thể tăng giá trị này cao hơn giá trị hard limit đã được user root set sẵn.



# Cấu hình

## Kiểm tra số FD hiện tại của 1 user

Để kiểm tra giới hạn file descriptor hiện tại, ta dùng lệnh **_ulimit_**

  * _ulimit –a_ : coi toàn bộ các limit hiện có của user đang dùng
  * _ulimit -S –n_ : coi soft open file limits
  * _ulimit -H –n_ : coi hard open file limits

![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-cau-hinh-max-file-descriptor-02.png)

_Kiểm tra hard và soft limit file descriptor hiện tại_

Mỗi user sẽ có một giới hạn file descriptor của mình, ta nên dùng chính xác user của process đang chạy để kiểm tra các tham số này.

## Kiểm tra số FD hiện tại của 1 process

Để kiểm tra giới hạn Open File Limits hiện có của 1 process, ta thực hiện những bước sau: Lấy process ID của process đó bằng lệnh 
    
    
    ps aux | grep sshd

Hoặc 
    
    
    pidof sshd

Kiểm tra giới hạn FD tối đa của process đó 
    
    
    cat /proc/{process_id}/limits

![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-cau-hinh-max-file-descriptor-03.png)

_Kiểm tra các giới hạn tài nguyên tối đa của process có PID 1036_

Kiểm tra số FD mà process đó hiện đang dùng: 
    
    
    ls /proc/1036/fd | wc –l

## Cấu hình giới hạn FD cho từng user

**Cách 1**

Ta có thể dùng lệnh **_ulimit –Sn 32000_** và **_ulimit –Hn_** **_64000_** để điều chỉnh limit FD cho user, tuy nhiên, 2 lệnh này chỉ có tác dụng cho session đó, khi mất session thì cấu hình sẽ không được lưu lại. Để lưu lại cấu hình limit cho các user thì ta phần phải chỉnh sửa file limits.conf như sau:
    
    
    vi /etc/security/limits.conf
    
    […]
    
    root soft nofile 32000
    root hard nofile 64000
    
    nduytg soft nofile 4096
    nduytg hard nofile 8192

![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-cau-hinh-max-file-descriptor-04.png)

_Cấu hình limit cho user trong file /etc/security/limits.conf_

Ta sẽ cấu hình soft limit cho user root là 32000 FD và hard limit là 64000 FD và cho user nduytg là 4096:8192. Tiếp theo, ta cần cấu hình thêm vào file /etc/pam.d/login để mỗi khi có session mới, hệ thống sẽ tự nạp thay đổi cho các user này.
    
    
    vi /etc/pam.d/login
    
    […]
    session required pam_limits.so

Cấu hình dùng PAM trong file sshd_config 
    
    
    vi /etc/ssh/sshd_config
    
    […]
    UsePAM yes

Khởi động lại hệ thống và kiểm tra lại các giá trị 
    
    
    init 6
    
    ulimit –n
    ulimit –Hn
    ulimit –Sn

![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-cau-hinh-max-file-descriptor-05.png)

_Kết quả sau khi cấu hình ulimit_

## Cấu hình giới hạn FD cho toàn bộ hệ thống

Ở mục trước, ta đã làm quen với việc cấu hình cho từng user, trong mục này ta sẽ chỉnh sửa giới hạn max file descriptor cho toàn bộ hệ thống bằng cách [điều chỉnh thông số của kernel](https://cloudcraft.info/huong-dan-toi-uu-linux-kernel/). Tùy vào ứng dụng bạn dùng mà ta sẽ tăng giới hạn cho user/hệ thống hoặc là tăng cả hai.

Để cấu hình tùy chỉnh giới hạn FD cho toàn bộ hệ thống, ta dùng lệnh sysctl như sau:
    
    
    sysctl -w fs.file-max=500000
    
    cat /proc/sys/fs/file-max

![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-cau-hinh-max-file-descriptor-07.png)

_Cấu hình giới hạn FD cho toàn hệ thống bằng lệnh sysctl_

Tuy nhiên, khi dùng lệnh sysctl, giá trị thay đổi chỉ có hiệu lực cho tới trước lần khởi động kế tiếp, để thay đổi được lưu lại vĩnh viễn, ta cần chỉnh sửa trực tiếp file **_sysctl.conf_** như sau: 
    
    
    vi /etc/sysctl.conf
    
    […]
    fs.file-max=700000

Nạp lại giá trị mới của biến **_fs.file-max_** bằng lệnh sau: 
    
    
    sysctl -p
    
    cat /proc/sys/fs/file-max

_![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-cau-hinh-max-file-descriptor-08-1.png)_

_Kiểm tra lại kết quả cấu hình_

Như vậy, cấu hình giá trị FD tối đa cho toàn hệ thống sẽ được lưu lại kể cả khi ta reboot hệ thống, do giá trị này đã được ghi lại trong file _**/etc/sysctl.conf**_

Phù....vậy là xong rồi đó các bạn, mình ngâm bài này cả 2 tháng, nay mới publish được :))), hy vọng các bạn đã hiểu thêm về các cách tăng file descriptor limit trong Linux nhé.

Coi thêm những bài viết liên quan:

  * [Hướng dẫn tối ưu linux kernel](https://cloudcraft.info/huong-dan-toi-uu-linux-kernel/)
  * [Hướng dẫn upgrade linux kernel](https://cloudcraft.info/huong-dan-upgrade-kernel-linux/)



# Tham khảo

<https://en.wikipedia.org/wiki/File_descriptor> <http://pro.benjaminste.in/post/318453669/increase-the-number-of-file-descriptors-on-centos> <https://www.cyberciti.biz/tips/linux-procfs-file-descriptors.html> <http://lzone.de/blog/Apply-changes-to-limits.conf-immediately>
