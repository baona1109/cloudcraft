---
title: "Hướng Dẫn Xử Lý Tình Trạng Full Inode"
date: 2022-06-30 08:35:39
categories: [Linux]
---

Inode là gì? Inode (index node) là một cấu trúc dữ liệu trong hệ thống tập tin Unix-style, đối tượng tập tin của hệ thống được mô tả như một file hoặc một thư mục. Nói cách khác inode chính là số lượng file và thư mục trên một máy chú.

  1. **Để xử lý tình trạng full inode, chúng ta có hai bước cơ bản sau:**


  * **Bước 1:** Kiểm tra thông số inode trên máy chủ

Đầu tiên ta sử dụng câu lệnh df –i để kiểm tra thông số inode. Trường hợp **cột IUSE% hiện thông số là 100%** , điều này có nghĩa số lượng file và thư mục lưu trữ đã đạt giới hạn tối đa. `# df -hi` ![](http://cloudcraft.info/wp-content/uploads/2022/06/inode_1-300x101.png) Tiếp theo ta kiểm tra và xác định thư mục, đường dẫn nào đang chiếm thông số inodes cao nhất bằng lệnh sau: `# find / -xdev -printf '%h\n' | sort | uniq -c | sort -k 1 -n` ![](http://cloudcraft.info/wp-content/uploads/2022/06/inode_2-300x111.png)

  * **Bước 2:** Để xử lý tình trạng full inode, chúng ta có thể xóa những file, thư mục chiếm thông số inode cao bằng cách sử dụng lệnh **rm****,** sau đó tiến hành reboot hoặc restart lại các dịch vụ có liên quan.

**2\. Các cách hạn chế tình trạng full inode:**

  * Loại bỏ thư mục và file không sử dụng đến:

Ví dụ những thư mục hay file backup trên host sẽ chiếm dung lượng lớn. Thế nên, bạn cần kiểm tra xem mình có lưu thư mục hay file backup trên host không. Bạn có thể xử lý bằng cách tải thư mục hay file backup về máy hoặc sao lưu trên dịch vụ lưu trữ đám mây thay vì đặt trên host.

  * Kiểm tra hòm thư:

Nếu bạn đang sử dụng host để làm mail server thì bạn nên kiểm tra thư mục **Sent** hay **Trash** trong tài khoản email của bạn. Bạn có thể tải về máy của mình và xoá nó trên host.

  * Kiểm tra số lượng file cache:

Những website hiện nay đều có sử dụng plugin cache. Vì vậy bạn nên kiểm tra thư mục cache của website. Giảm số lượng file bạn đang giữ trong đó. Những plugin cache đều có tuỳ chọn Purge cache. Bạn cũng có thể sử dụng tính năng này để giảm số lượng file cache. Chúc các bạn thành công !!!
