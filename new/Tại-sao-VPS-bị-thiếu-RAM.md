---
title: "Tại sao VPS bị thiếu RAM?"
date: 2018-08-20 14:13:22
categories: [Linux, KVM]
---

Chắc hẳn có không ít người dùng thắc mắc rằng “Tại sao lượng RAM thực tế hiển thị trên các server / VPS sử dụng hệ điều hành Linux lại thấp hơn lượng RAM vật lý gắn vào hay lượng RAM cấp phát?” Ví dụ một VPS sử dụng hệ điều hành CentOS 7, được cấp phát 1GB RAM nhưng khi thực hiện lệnh **free -m** để kiểm tra lượng RAM thực tế thì chỉ hiển thị khoảng 992MB RAM như hình bên dưới: ![](https://cloudcraft.info/wp-content/uploads/2018/08/tai-sao-vps-bi-thieu-ram-1.png) Nguyên nhân dẫn tới sự khác biệt này là do khi một máy chủ Linux được khởi động, một phần nhỏ RAM sẽ được kernel sử dụng để chạy các process/thread và các module. Ngoài ra, kernel Linux cũng sử dụng lượng RAM khả dụng để thực hiện cache. 

## Làm thế nào để kiểm tra kernel đang sử dụng bao nhiêu RAM?

Để kiểm tra lượng RAM mà kernel đang sử dụng, các bạn có thể thực hiện các cách sau: 

  * Xem thông tin **Slab** tại file /proc/meminfo

Dùng lệnh **cat /proc/meminfo** ![](https://cloudcraft.info/wp-content/uploads/2018/08/tai-sao-vps-bi-thieu-ram-2.png)

  * Dùng lệnh **slabtop**

![](https://cloudcraft.info/wp-content/uploads/2018/08/tai-sao-vps-bi-thieu-ram-3.png)

## Vậy tất cả VPS sử dụng HĐH Linux đều gặp trường hợp này?

Câu trả lời là không nhé! Cụ thể là các VPS sử dụng công nghệ ảo hóa dạng container như LXC hay OpenVZ sẽ hiển thị đúng lượng RAM được cung cấp. Nguyên nhân là do các VPS sử dụng các công nghệ ảo hóa này sẽ dùng chung kernel với server vật lý. Ví dụ một VPS sử dụng công nghệ ảo hóa OpenVZ, chạy hệ điều hành CentOS 7, được cấp phát 1GB RAM, khi thực hiện lệnh **free -m** thì cho ra kết quả sau: ![](https://cloudcraft.info/wp-content/uploads/2018/08/tai-sao-vps-bi-thieu-ram-4.png)
