---
title: "Tự động đồng bộ 2 thư mục trên Windows"
date: 2017-12-12 09:28:47
categories: [Windows]
---

Hiện tại, Windows chưa phát triển một công cụ nào thực hiện việc đồng bộ 2 thư mục trên cùng 1 server. Trong khi đó, việc sử dụng các công cụ download trên internet mang lại nhiều rủi ro về bảo mật. Dưới đây là hướng dẫn sử dụng script “cây nhà lá vườn” do đội ngũ CloudCraft phát triển để thực hiện việc đồng bộ 2 thư mục: _ Truy cập đường dẫn sau để download và giải nén file script: <https://github.com/cloudcraftteam/Sync-folders> _ Mở cửa sổ command line và di chuyển tới thư mục chứa file script. (hoặc mở thư mục chứ file script, bấm Shift + chuột phải => chọn **Open command window here**) _ Thực thi script với cú pháp sau: 
    
    
    sync.bat <source> <dest> <timeout>

Trong đó: **timeout** : thời gian chờ giữa 2 lần sync, tính bằng giây (để trống sẽ thực hiện đồng bộ 1 lần duy nhất) Ví dụ: 
    
    
    sync.bat C:\homework D:\secret 5

Lệnh trên sẽ thực hiện sync từ thư mục **C:\homework** tới thư mục **D:\secret** , thời gian giữa các lần sync là 5 giây **Lưu ý** : _ Cần phải đảm bảo thư mục nguồn và thư mục đích đã được tạo. _ Phải giữ cửa sổ command line luôn mở (nếu đóng thì chương trình sẽ dừng). _ Đường dẫn thư mục nguồn và thư mục đích không được có dấu hoặc kí tự đặt biệt, ví dụ: #, $, @, %...
