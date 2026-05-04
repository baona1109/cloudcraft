---
title: "Hướng dẫn chặn toàn bộ IP của một quốc gia trên Windows"
date: 2017-12-01 10:13:37
categories: [Windows, Security]
---

Nếu các bạn đang sử dụng VPS/Server chạy HĐH Windows và gặp trường hợp bị kẻ xấu từ nước ngoài thực hiện tấn công như DDoS, dò mật khẩu...thì bài viết này sẽ hướng dẫn các bạn cách để hạn chế tối đa các trường hợp đó bằng cách chặn toàn bộ IP của một quốc gia. Để chặn toàn bộ IP của một quốc gia trên Windows, các bạn thực hiện như sau: _ Truy cập đường dẫn sau để download và giải nén file script: <https://github.com/cloudcraftteam/Import-firewall-blocklist> _ Mở cửa sổ **Windows PowerShell** với quyền Administrator và di chuyển tới thư mục chứa file script, ví dụ thư mục sau khi giải nén đặt ở ổ đĩa D, các bạn thực hiện lệnh sau: `cd "D:\Import-firewall-blocklist-master"` _ Thực thi lần lượt các lệnh sau: `PowerShell.exe -ExecutionPolicy Bypass` (cấu hình policy cho phép thực thi script)   `.\Import-Firewall-Blocklist.ps1 -zone CN` (thực thi script, với **CN** là mã quốc gia 2 kí tự, tham khảo tại: <http://www.worldatlas.com/aatlas/ctycodes.htm>)__ Thực thi lại lệnh trên và thay mã quốc gia để chặn các quốc gia khác.   Để xóa các rule chặn IP đã tạo, các bạn thực hiện lệnh sau: `.\Import-Firewall-Blocklist.ps1 -zone CN -deleteonly`   `PowerShell.exe -ExecutionPolicy Restricted` (cấu hình lại policy)
