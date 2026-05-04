---
title: "Hướng dẫn cài đặt và mount NFS trên CentOS 7"
date: 2022-06-20 15:09:31
categories: [Linux]
---

## **NFS là gì?**

**NFS (Network File System)** là một hệ thống giao thức chia sẻ file phát triển bởi Sun Microsystems từ năm 1984, cho phép người dùng mount (móc) một file system từ một hay nhiều máy khác về máy của mình thông qua không gian mạng, và sử dụng nó như truy cập trực tiếp trên ổ cứng. Hiện tại có 3 phiên bản NFS là NFSv2, NFSv3, NFSv4. 

## **Hướng dẫn cài đặt NFS server và NFS client**

Ở bài viết này, mình sẽ sử dụng 02 máy ảo như sau: 

  * server: 10.10.123.11/24
  * client: 10.10.123.12/24



### **NFS Server**

  * Cài đặt các package cần thiết:

`_# yum install nfs-utils -y_`

  * Tạo thư mục chia sẻ:

`_# mkdir -p /var/nfs/share_`

  * Cấu hình phân quyền cho các client:

`_# vi /etc/exports_`

  * Ta thêm dòng sau:

`/var/nfs/share 10.10.123.0/24(rw,no_root_squash)` Ý nghĩa của dòng này là ta sẽ cấp phép cho các client có IP nằm trong dải 10.10.123.0/24 được phép mount và read/write lên thư mục chia sẻ. 

  * Khởi động NFS server:

`_# systemctl start rpcbind nfs-server_` `_# systemctl enable rpcbind nfs-server_`

  * Kiểm tra mount point:

`_# showmount -e_` Kết quả dự kiến: ![](http://cloudcraft.info/wp-content/uploads/2022/06/huong-dan-cai-dat-va-mount-nfs-tren-centos-7-1.png)

### **NFS Client**

  * Cài đặt các package cần thiết:

`_# yum install nfs-utils nfs-utils-lib -y_`

  * Kiểm tra mount point trên một server:

`_# showmount -e 10.10.123.11_` Kết quả dự kiến: ![](http://cloudcraft.info/wp-content/uploads/2022/06/huong-dan-cai-dat-va-mount-nfs-tren-centos-7-2.png)

  * Tạo thư mục mới và mount thư mục share vào thư mục vừa tạo:

`_# mkdir -p /var/node01/share_` `_# mount -t nfs 10.10.123.11:/var/nfs/share /var/node01/share_`

  * Kiểm tra thông tin các thư mục đã mount:

`_# nfsstat -m_` Kết quả dự kiến: ![](http://cloudcraft.info/wp-content/uploads/2022/06/huong-dan-cai-dat-va-mount-nfs-tren-centos-7-3.png)

  * Cấu hình cho hệ thống tự động mount khi reboot:

`_# vi /etc/fstab_` Thêm dòng sau vào cuối file: `10.10.123.11:/var/nfs/share /var/node01/share nfs rw,sync,hard,intr 0 0`

# **3\. Một số command thường dùng**

  * showmount -e: Hiển thị thư mục share trên hệ thống
  * showmount -e <server-ip or hostname>: Hiển thị danh sách thư mục share trên một server khác
  * showmount -d: Liệt kê danh sách các thư mục con
  * exportfs -v: Hiển thị danh sách các thư mục chia sẻ và các option trên server
  * exportfs -a: Export toàn bộ thư mục share trong /etc/exports
  * exportfs -u: Unexports toàn bộ thư mục share trong /etc/exports
  * exportfs -r: Refresh sau khi đã chỉnh sửa /etc/exports


