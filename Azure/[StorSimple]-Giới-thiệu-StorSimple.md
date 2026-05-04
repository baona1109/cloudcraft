---
title: "[StorSimple] Giới thiệu StorSimple"
date: 2018-02-08 11:48:08
categories: [Azure]
---

StorSimple là một giải pháp Hybrid SAN **(S** torage **A** rea **N** etworking) chuyên dụng, tương thích tốt với Azure Cloud (do cùng từ Microsoft đẻ ra). Ngoài chức năng lưu trữ dữ liệu tại local như bao loại SAN khác, StorSimple với firmware-đã-được-xào-nấu-kĩ có thể tương thích tốt với Azure Storage Service. Dữ liệu của người dùng dễ dàng được luân chuyển giữa thiết bị vật lý ở DC của khách hàng và DC của Azure. Điều này tạo ra thêm một giải pháp cho bài toán Backup và Disaster Recovery. Ngoài thiết bị vật lý, StorSimple còn giải pháp SAN mềm chạy trên máy ảo (Virtual Machine - VM) gọi là Virtual Appliance. Virtual Appliance này có thể được host trên VMWare, Hyper-V hoặc Azure Virtual Machine. 

# MÔ HÌNH GIẢI PHÁP

Với StorSimple và Azure Storage, ta sẽ có một mô hình giải pháp như sau: [caption id="" align="aligncenter" width="1055"]![](https://docs.microsoft.com/en-us/azure/storsimple/media/storsimple-overview/overview-big-picture.png) Mô hình ứng dụng StorSimple (Nguồn Microsoft)[/caption] 

## Tại on-prem của người dùng:

Như bao loại SAN khác, các máy client có thể thông qua kết nối iSCSI để mount volume của StorSimple lên và sử dụng. StorSimple hỗ trợ client Windows Server/Linux/VMWare. Đối với các client Windows Server, admin có thể cài đặt thêm StorSimple Snapshot Manager Plug-in (free) để trực tiếp quản trị các bản snapshot của các volume được cấp phát. Lưu ý: là chỉ quản trị được snapshot của các volume được mount cho riêng client đó, không phải tất cả volume hiện có trên thiết bị StorSimple. Đừng nghe MCS chém gió : )). 

## Tại DataCenter của Azure:

Azure Storage Service sẽ lưu trữ một phần dữ liệu của người dùng. Ngoài ra, người dùng còn có thể tạo ra một các StorSimple Virtual Appliance trên Azure Virtual Machine. Giao diện quản trị chính của StorSimple là qua StorSimple Service Manager trên Azure. [caption id="attachment_533" align="aligncenter" width="1058"]![](https://cloudcraft.info/wp-content/uploads/2017/12/StorSimpleManagementService-e1514887319307.jpg) Giao diện của StorSimple Device Manager trên Azure Portal (Censored)[/caption] Tại giao diện này, admin có thể quản lý được tất cả physical device đang được kết nối, các volume được cấp phát, update software cho thiết bị (lưu ý là chỉ software, còn disk firmware phải cắm console trực tiếp), check status thiết bị (cpu, ram, disk, network i/o,...),... Ngoài giao diện trên Azure Portal, người quản trị vẫn có thể trực tiếp cắm Serial Console vào thiết bị (cắm dây, rồi cấu hình Putty để kết nối) và tương tác qua giao diện command line. ([Link hướng dẫn sử dụng Putty)](https://docs.microsoft.com/en-us/azure/storsimple/storsimple-windows-powershell-administration) [caption id="" align="aligncenter" width="814"]![](https://docs.microsoft.com/en-us/azure/storsimple/media/storsimple-windows-powershell-administration/ic740906.png) Giao diện Serial Console của StorSimple[/caption] 

# CÁCH THỨC LƯU TRỮ

Bên trong thiết bị vật lý là tập hợp của nhiều thiết bị SSD và HDD. Những dữ liệu được truy xuất thường xuyên (hot data) sẽ được ưu tiên lưu vào SSD, những dữ liệu ít được truy xuất hơn, các bản local backup (warm data) sẽ được lưu về HDD. Cuối cùng, những bản cloud backup, dữ liệu đã lâu không truy xuất (cold data) sẽ được đẩy về cloud. **Update 17-04** : Khi tạo backup, người quản trị có thể phân loại đó là cloud hay local backup. Tuy nhiên, việc xác định dữ liệu nào là thường xuyên truy xuất, ít truy xuất hay đã lâu không truy xuất lại phụ thuộc hoàn toàn vào thuật toán của StorSimple và người quản trị không thể tự tinh chỉnh cho phù hợp với nhu cầu. (Thật ra trên console vật lý, MCS có chừa 1 mode thần thánh chỉ technical supporter của MCS đụng vào được và có trời biết nó có thể chỉnh gì ở mode này. Tuy nhiên để mở được mode này, người MCS phải nắm được password quản trị và console của thiết bị nên tạm là ta an toàn ?) ![](https://docs.microsoft.com/en-us/azure/storsimple/media/storsimple-overview/hcs-data-services-storsimple-components-tiers.png) Dựa trên ý tưởng phân loại dữ liệu trên, StorSimple cho phép ta tạo hai loại volume: 

  * **Locally pinned volume** : Volume sẽ được tạo ở dạng thick tại thiết bị vật lý, chỉ có các bản cloud snapshot và dữ liệu dạng archive được đẩy về cloud. Ưu điểm của dạng này là tốc độ truy xuất nhanh, nhưng bù lại là dung lượng tối đa sẽ thấp. Ví dụ bạn tạo một volume 1 TB thì thiết bị vật lý sẽ dành ra đúng 1 TB để lưu trữ volume này.
  * **Tiered volume** : Volume sẽ được tạo ra ở dạng thin-provisioning. Điều này có nghĩa là chỉ một phần của volume được tạo tại thiết bị vật lý, phần còn lại nằm trên cloud. Ưu điểm của dạng này thì ngược lại, dung lượng lưu trữ lớn nhưng tốc độ truy xuất sẽ chậm đi một chút. Ví dụ bạn tạo một volume 50TB thì thiết bị vật lý sẽ dành ra khoảng vài GB đến vài TB đại diện để lưu trữ hot và warm data, còn lại cold data sẽ nằm ở DC của Azure.



# ỨNG DỤNG

Mô hình Hybrid SAN kết hợp SAN truyền thống và các dịch vụ Storage trên cloud để vừa có thể lưu trữ dữ liệu cũng như tận dụng để phục vụ backup, disaster recovery đang dần trở thành một xu hướng mới. Với việc vừa đảm bảo những dữ liệu quan trọng được bảo mật tại LAN/WAN cũng như vừa sử dụng được các lợi ích của Public Cloud, đây hứa hẹn là một hướng đi triển vọng. Tuy nhiên, trước khi sử dụng, cần cân nhắc kĩ các giới hạn lưu trữ của StorSimple ([Link](https://docs.microsoft.com/en-us/azure/storsimple/storsimple-8000-limits)). Trong đó, một số đặc điểm đáng lưu tâm bao gồm: 

  * **Tốc độ truy xuất dữ liệu** : do đây là thiết bị Hybrid, tốc độ truy xuất sẽ phụ thuộc không nhỏ vào kết nối giữa thiết bị và DC của Azure.
  * **Dung lượng tối đa có thể sử dụng** : trên website hãng, thông số kỹ thuật của Microsoft (chắc do bọn Sale ranh ma của MCS viết) dựa trên tổng dung lượng các volume, dung lượng dùng để làm cache, dung lượng dùng để chứa metadata,... Do vậy sẽ có độ chênh đáng kể so với thực tế. Để hiểu chính xác nhất, cần tham khảo bài viết Understand Storsimple Limit (chắc là do bọn Dev chân chất lương thiện viết) đã dẫn link ở trên.


