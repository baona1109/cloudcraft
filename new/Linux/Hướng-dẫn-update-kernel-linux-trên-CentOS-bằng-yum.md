---
title: "Hướng dẫn update kernel linux trên CentOS bằng yum"
date: 2018-11-28 10:53:08
categories: [Linux]
---

# Hướng dẫn update kernel linux trên CentOS bằng yum

Khi quản trị các hệ thống chạy Linux thì việc update thường xuyên để vá các lỗ hổng bảo mật là một việc làm cần thiết và quan trọng. Trong bài viết này, mình sẽ hướng dẫn các bạn cách thức update kernel linux dễ dàng và nhanh chóng bằng yum trên CentOS (cần phải _**reboot server**_ sau khi update kernel) Sau đây mình sẽ hướng dẫn cách làm chi tiết từng bước cho các bạn: 

## Bước 1 - Kiểm tra file GRUB

Để cho kernel có thể tự động nhận kernel mới khi khi reboot, ta cần phải kiểm tra xem file /etc/grub.conf đã được link sang file /boot/grub2/grub.cfg chưa **Với CentOS 7** Nếu chưa có symbolic link, ta cần phải remove file cũ và tạo lại symbolic link mới 
    
    
    # Kiem tra symbolic link cua file grub
    ls -l /etc/grub2.cfg
    
    # Tao symbolic link moi
    rm -f /etc/grub2.cfg
    ln -s /boot/grub2/grub.cfg /etc/grub2.cfg
    ls -l /etc/grub2.cfg

**Với CentOS 6 trở về trước** Nếu chưa có symbolic link, ta cần phải remove file cũ và tạo lại symbolic link mới 
    
    
    # Kiem tra symbolic link cua file grub
    ls -l /etc/grub.conf
    
    # Tao symbolic link moi
    rm -f /etc/grub.conf
    ln -s /boot/grub/grub.conf /etc/grub.conf
    ls -l /etc/grub.conf

## Bước 2 - Update kernel linux
    
    
    # Kiem tra phien ban kernel dang chay
    uname -r
    
    # Chay lenh update kernel
    yum -y update kernel
    
    # Reboot server
    reboot

Sau khi server được reboot xong, ta hiến hành đăng nhập lại và kiểm tra phiên bản kernel hiện tại 

## Bước 3 - Kiểm tra và xóa các bản kernel cũ
    
    
    # Kiem tra kernel moi duoc update update
    uname -r

Ngoài ra, do kernel được lưu trên phân vùng /boot, nếu phân vùng này có kích thước nhỏ hơn 300MB thì ta nên xóa bớt các kernel cũ trong phân vùng này bằng tiện ích package-cleanu 
    
    
    # Cai dat tien ich package cleanup
    yum install -y yum-utils
    
    # Xoa cac phien ban kernel cu, chi giu lai mot phien ban dang dung
    # va mot kernel truoc do de backup
    package-cleanup --oldkernels --count=2

  Các bạn có thể xem thêm bài viết hướng dẫn upgrade kernel linux từ source tại đây: 

  * [Hướng dẫn upgrade kernel linux từ source](https://cloudcraft.info/huong-dan-upgrade-kernel-linux/)


