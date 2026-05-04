---
title: "Hướng dẫn vô hiệu hóa giao thức SMB trên Windows server"
date: 2018-01-02 10:32:37
categories: [Windows, Security]
---

SMB (Server Message Block) là một giao thức trên Windows cho phép người dùng chia sẻ tập tin, máy in, serial port...giữa các máy. Trong quá khứ, không ít các trường hợp kẻ xấu đã lợi dụng giao thức này để thực hiện tấn công các máy khác, và nổi bật nhất là sự kiện WannaCry xảy ra vào tháng 5/2017.

Để đề phòng việc bị kẻ xấu khai thác lổ hỏng bảo mật thông qua giao thức SMB, các bạn có thể chủ động tắt giao thức này trên Server/VPS bằng cách sau:

# **Vô hiệu hóa SMB server**

_ Mở cửa sổ **Windows PowerShell** với quyền Administrator bằng cách bấm nút Start, tìm từ khóa **Windows PowerShell** , nhấp chuột phải vào kết quả tìm được và chọn Run as Administrator

_ Thực thi các lệnh sau:

  * Đối với Windows server 2012: 
    * _Set-SmbServerConfiguration -EnableSMB1Protocol $false_
    * _Set-SmbServerConfiguration -EnableSMB2Protocol $false_
  * Đối với Windows server 2008: 
    * _Set-ItemProperty -Path “HKLM:\SYSTEM\CurrentControlSet\Services\LanmanServer\Parameters" SMB1 -Type DWORD -Value 0 -Force_
    * _Set-ItemProperty -Path "HKLM:\SYSTEM\CurrentControlSet\Services\LanmanServer\Parameters" SMB2 -Type DWORD -Value 0 -Force_



![](https://cloudcraft.info/wp-content/uploads/2018/01/huong-dan-vo-hieu-hoa-giao-thuc-smb-tren-windows-server-1.png)

_ Khởi động lại Server/VPS

# **Vô hiệu hóa SMB client**

**_** Mở cửa sổ **Command Prompt** với quyền Administrator bằng cách bấm nút **Start** , tìm từ khóa **Command Prompt** , nhấp chuột phải vào kết quả tìm được và chọn **Run as Administrator**

_ Thực thi các lệnh sau:

_sc.exe config lanmanworkstation depend= bowser/mrxsmb20/nsi_

_sc.exe config mrxsmb10 start= disabled_

_sc.exe config lanmanworkstation depend= bowser/mrxsmb10/nsi_

_sc.exe config mrxsmb20 start= disabled_

![](https://cloudcraft.info/wp-content/uploads/2018/01/huong-dan-vo-hieu-hoa-giao-thuc-smb-tren-windows-server-2.png)

_ Khởi động lại Server/VPS
