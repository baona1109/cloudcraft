---
title: "Hướng dẫn kiểm tra trạng thái RAID"
date: 2019-02-22 10:29:03
categories: [Linux, General, Hardware, RAID]
---

RAID như mọi người biết là một hình thức ghép nhiều đĩa cứng vật lý để tạo nên 1 hệ thống đĩa cứng có dung lượng lớn hơn tăng tốc độ đọc ghi hoặc tăng thêm độ an toàn cho dữ liệu chứa trên hệ thống đĩa. Bài viết này không hướng dẫn cách tạo RAID hay cấu hình phân loại. Ở bài này sẽ hướng dẫn một số thủ thuật để các bạn kiểm tra cấu hình, tình trạng sức khỏe của các disk bên trong 1 hệ thống RAID của 2 dòng server là DELL và HP trên Linux Trước tiên bạn cần kiểm tra server của bạn là thuộc hãng nào đã 
    
    
    dmidecode -t1 | grep -Ei "serial|manufacturer|product"

Nếu dòng Manufacturer hiện ra là HP thì là dòng server HP ngược lại hiện DELL thì là dòng server DELL, như ảnh dưới thì hiện thị là server HP. ![](https://cloudcraft.info/wp-content/uploads/2018/08/Huong-dan-kiem-tra-trang-thai-raid-1.png) Đối với dòng server HP sử dụng lệnh _**hpacucli**_ hoặc  _**hpssacli** _do HP cung cấp 
    
    
    hpacucli/hpssacli ctrl all show config

![](https://cloudcraft.info/wp-content/uploads/2018/08/Huong-dan-kiem-tra-trang-thai-raid-2.png) Kết quả ta sẽ thấy được thông tin của RAID hiện có, số lượng RAID server đang cấu hình, số disk vật lý trong RAID, dung lượng, trạng thái của disk,... Đối với dòng server DELL ta sẽ sử dụng lệnh _**omreport**_ do DELL cung cấp. Để kiểm tra virtual disk (vdisk) tức là disk được tạo từ RAID dựa trên disk vật lý dùng lệnh 
    
    
    omreport storage vdisk

![](https://cloudcraft.info/wp-content/uploads/2018/08/Huong-dan-kiem-tra-trang-thai-raid-4.png) Lệnh này dùng để kiểm tra trạng thái disk raid đang chạy (vdisk). Nếu vdisk khi kiểm tra có vấn đề thì để xác định disk vật lý nào đang chạy bị lỗi gây cho vdisk lỗi theo thì dùng lệnh 
    
    
    omreport storage pdisk controller=0

Với controller xác định vị trí controller đang điều khiển RAID trên server ![](https://cloudcraft.info/wp-content/uploads/2018/08/Huong-dan-kiem-tra-trang-thai-raid-5.png) ID thể hiện vị trí của disk vật lý được gắn trên server, Status là trạng thái hiện tại của disk, Khi chạy lệnh này sẽ liệt kê toàn bộ disk vật lý được dùng để cấu hình RAID Ngoài ra, một số server custom chạy RAID ngoài, không có lệnh hỗ trợ của nhà sản xuất mà bạn cần xác định nó đang dùng kiểu cấu hình RAID nào thì có thể dùng nhanh lệnh sau để kiểm tra 
    
    
    lspci | grep RAID

**Kết quả**

  * 3ware — Bạn đang dùng 3ware RAID.
  * Hewlett-Packard — Bạn đang dùng HP RAID.
  * megaRAID — Bạn đang dùng MegaRAID.
  * Nếu hiện lên các tên khác (hoặc không có kết quả) — Bạn đang dùng software RAID.


