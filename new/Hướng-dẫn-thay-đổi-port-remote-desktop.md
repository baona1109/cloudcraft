---
title: "Hướng dẫn thay đổi port remote desktop"
date: 2018-04-24 09:00:33
categories: [Windows, Security]
---

Remote desktop là một tính năng trên các hệ điều hành Windows cho phép người dùng có thể truy cập và điều khiển máy tính của mình từ xa thông qua internet hoặc mạng nội bộ. Port hoạt động mặc định của remote desktop là 3389, và nhằm tăng độ bảo mật để tránh người ngoài truy cập, ta nên thay đổi port mặc định này. Ở bài viết này, Cloudcraft sẽ hướng dẫn các bạn cách thay đổi port mặc định của remote desktop trên **Windows server 2008**. Các bước thực hiện như sau: 

  * Mở cửa sổ **Registry Editor** bằng cách bấm nút **Start** => **Run** (hoặc dùng tổ hợp phím **Windows** \+ **R**) => gõ **regedit.exe**

![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-thay-doi-port-cua-remote-desktop-1.png)

  * Trước khi thực hiện chỉnh sửa bất kì registry nào, ta nên lưu lại một bản dự phòng của registry đó để tránh việc cấu hình sai làm ảnh hưởng tới hệ thống. Ta nhấp chuột phải vào registry cần backup, chọn **Export** , sau đó chọn nơi sẽ lưu trữ bản dự phòng

![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-thay-doi-port-cua-remote-desktop-2.png)

  * Để thay đổi port hoạt động của remote desktop, ta truy cập đường dẫn sau trên **Registry Editor**

**HKEY_LOCAL_MACHINE\System\CurrentControlSet\Control\Terminal Server\WinStations\RDP-Tcp\PortNumber**

  * Nhấp chuột phải vào **PortNumber****,** chọn **Modify…**

![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-thay-doi-port-cua-remote-desktop-3.png)

  * Tại cửa sổ **Edit DWORD** , ta chọn **Decimal** để sử dụng hệ số thập phân, và thay đổi port hoạt động tại ô **Value data** , bấm **OK**

![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-thay-doi-port-cua-remote-desktop-4.png)

  * Để truy cập và điều khiển máy tính từ xa, ta truy cập với địa chỉ <**hostname** >:<**port** > hoặc <**địa chỉ IP** >:<**port** >

![](https://cloudcraft.info/wp-content/uploads/2017/12/huong-dan-thay-doi-port-cua-remote-desktop-5.png)
