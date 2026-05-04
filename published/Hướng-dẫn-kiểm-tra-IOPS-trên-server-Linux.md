---
title: "Hướng dẫn kiểm tra IOPS trên server Linux"
date: 2019-02-25 07:30:01
categories: [Linux, Hardware]
---

Đây là bài hướng dẫn cách benchmark hiệu năng đọc ghi của disk trên server Linux, nôm na là kiểm tra IOPS của server để tránh bị hớ khi mua và sử dụng server ~~(Đón đọc tập sách "_Đọc thông số server - Để không bị hố hàng và lợi dụng_ ")~~ Mình cũng xin nhắc lại về thuật ngữ IOPS một chút, **IOPS (Input/Output operations per second)** là số thao tác nhập xuất trên một giây. Đây là một thông số thường được dùng để kiểm tra hiệu suất của các thiết bị lưu trữ như HDD, SSD, SAN.... Đối với các thao tác trên server thì chỉ số IOPS rất quan trọng, nó quyết định độ “NHẠY” và độ “NHANH” của một server. Cụ thể là đối với các thao tác như bật/tắt server, backup file, truy xuất dữ liệu, đọc/ghi database.... Giờ ta bước vào quá trình kiểm tra IOPS. Trước tiên ta cần cài đặt phần mềm hỗ trợ việc test này là _**fio**_ trên CentOS: 
    
    
    sudo yum install fio -y

Tiến hành kiểm tra tốc độ read random trên server 
    
    
    fio -filename=/home/dangtgh/testfio.txt \
    -direct=1 \
    -rw=randread \
    -bs=4k \
    -size=2G \
    -runtime=1000 \
    -group_reporting \
    -name=mytest

Với lệnh trên có nghĩa sau 

  * **-rw=randread** : tiến hành read random nội dung của 1 file liên tục, nếu file không tồn tại thì lệnh fio sẽ random 1 file có kích thước là **-** size
  * **-bs=4k** : Mỗi lần đọc là một block có kích thước block size là 4k bytes
  * **-size=2G** : Kích thước file tiến hành read
  * **-runtime=1000** : thể hiện số lần chạy lệnh trong một giây
  * **-group_reporting -name=mytest** : chỉ là cho vào nhóm report và tên tiến trình là mytest

Kết quả ![](https://cloudcraft.info/wp-content/uploads/2018/12/hướng-dẫn-kiểm-tra-iops-tren-server-linux-1.png) Theo kết quả này thì IOPS cho read random vào khoảng **797**. Các bạn có thể tham khảo bản này để nắm được các thông số IOPS của các dòng ổ cứng phổ biến. Vì tùy vào disk, loại RAID mà IOPS dao động khác nhau. Bạn có thể tham khảo bảng bên dưới để xác định được mức IOPS trên server của bạn là đã đạt mức chuẩn hay chưa ~~(hay là bị Sales nó lừa mua hàng dỏm)~~. Coi thêm chi tiết [tại đây](https://en.wikipedia.org/wiki/IOPS) **Device** | **Type** | **IOPS** | **Interface**  
---|---|---|---  
5,400 rpm SATA drives | HDD | ~50–80 IOPS | SATA 3 Gbit/s  
7,200 rpm SATA drives | HDD | ~75–100 IOPS | SATA 3 Gbit/s-SAS 12Gbps  
10,000 rpm SAS drives | HDD | ~125–150 IOPS | SAS  
15,000 rpm SAS drives | HDD | ~175–210 IOPS | SAS  
Samsung SSD 850 PRO | SSD | 100,000 read IOPS 90,000 write IOPS | SATA 6 Gbit/s  
Samsung SSD 960 EVO | SSD | 380,000 read IOPS 360,000 write IOPS | NVMe over PCIe 3.0 x4, M.2  
Samsung SSD 960 PRO | SSD | 440,000 read IOPS 360,000 write IOPS | NVMe over PCIe 3.0 x4, M.3  
  Kế tiếp hành kiểm tra tốc độ write random trên server 
    
    
    fio -filename=/home/dangtgh/testfio.txt \
    -direct=1 \
    -rw=randwrite \
    -bs=4k \
    -size=2G \
    -runtime=1000 \
    -group_reporting \
    -name=mytest

Ta chỉ thay randread thành randwrite là được ![](https://cloudcraft.info/wp-content/uploads/2018/12/hướng-dẫn-kiểm-tra-iops-tren-server-linux-2.png) Kết quả trên ta thấy IOPS cho việc write random là **445** Các tham số ở phần report kết quả trong hình có thể lên trang chủ lệnh fio để xem [Link](https://linux.die.net/man/1/fio) Ngoài ra, nếu các bạn sử dụng **Windows** thì có thể dùng tool này để kiểm tra, tool này khá đơn giản nên mình sẽ không viết hướng dẫn cho tool này: [CrystalMark](https://crystalmark.info/en/software/crystaldiskinfo/)
