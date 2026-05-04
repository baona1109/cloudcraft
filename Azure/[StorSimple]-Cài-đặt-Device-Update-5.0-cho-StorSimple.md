---
title: "[StorSimple] Cài đặt Device Update 5.0 cho StorSimple"
date: 2018-08-26 02:51:09
categories: [Azure]
---

\- Alô, Microsoft VietNam (MCS VN) đấy phải không ? Tôi đang cài đặt Device Update 5.0 cho Storsimple và có một số thắc mắc...?

\- Chào bạn, cảm ơn bạn đã liên hệ nhưng thiệt tình chắc phải xin lỗi bạn vì mười mấy năm nay tôi chưa từng được đụng đến con này nữa, lấy gì mà hỗ trợ đây. Bạn liên hệ support cho MCS... vùng thử ?

Và thế là bài "trải nghiệm" này ra đời.

# ![](https://cloudcraft.info/wp-content/uploads/2018/08/1536918795702.jpeg)

# Tổng quan về StorSimple Device Update:

StorSimple là một thiết bị Hybrid SAN chuyên biệt cho Azure. Bạn có thể tìm đọc bài giới thiệu tại đây. ([Giới thiệu StorSimple](https://cloudcraft.info/gioi-thieu-hybrid-san-storsimple/)).

StorSimple có hai dạng là: **thiết bị vật lý** (SAN thật) và **Virtual Appliance** (PC/VM chạy phần mềm StorSimple). Cập nhật phần mềm với SAN ảo thì không có gì phải bàn, bài viết này sẽ chỉ tập trung vào đồ thật. Tính đến thời điểm bài viết này, Update 5.0 vẫn là phiên bản phần mềm mới nhất của StorSimple. Tuy nhiên, đa phần các thiết bị StorSimple đến tay khách hàng đều mới chỉ dừng lại ở các phiên bản cũ hơn (do bán ế chứ đâu).

Vậy thì cập nhật thiết bị StorSimple có những cách thế nào và những bước ra sao ?

Theo tài liệu của MCS, một gói **Device Update** của StorSimple sẽ gồm 2 phần tương ứng với 2 bước lớn sau:

  * **Software Update** bao gồm: Device software, Storport, Spaceport, OS Security Updates và OS Updates. Ta có thể cập nhật gói này ở **Regular Mode** (mode làm việc thông thường của StorSimple).
  * **Disk Firmware Updates** nghĩa là cập nhật Firmware cho các ổ cứng. Để thực hiện, ta cần bật **Maintenance Mode** của thiết bị. Lưu ý: ở mode này, không thể thực hiện bất kì thao tác đọc/ghi dữ liệu nào cả.



Qui trình đề xuất là ta sẽ thực hiện Software Update trước rồi mới thực hiện Disk Firmware Update. MCS bảo rằng ta có thể thực hiện Software Update mà không có downtime, chỉ có Disk Firmware mới có downtime. Nhưng vì chúa (hay ai cũng được), **ĐỪNG NGHE !!!** Trong suốt quá trình Update, ta không nên thực hiện bất kì thao tác đọc/ghi nào cả.****

Để update StorSimple, ta có 2 cách:

  * **Thủ công** : tải từng gói hotfix thông qua trang Hotfix Catalog của MCS và cài đặt từng gói một thông qua Console thiết bị. Cách làm này thích hợp với môi trường có kết nối mạng kém cũng như yêu cầu qui trình bảo mật cao. Bên cạnh đó, cách làm này áp dụng được cho tất cả các phiên bản StorSimple từ 5.0 trở về trước. Nhược điểm của cách làm này là cần một nơi để chứa các gói Hotfix (thường là một host trung gian khác) chung mạng LAN với thiết bị.
  * **Bán tự động** : thực hiện Software Update thông qua Azure Portal & cắm Serial để thực hiện Firmware Update. Ưu điểm của cách làm này là Azure sẽ tự động detect ra những gói Device Update phù hợp với thiết bị của bạn. Tuy nhiên nhược điểm là phụ thuộc rất nhiều vào kết nối mạng (quá trình tải khá lâu) và chỉ áp dụng được cho thiết bị đang chạy phiên bản 3.0 trở về sau.



# Thực hiện StorSimple Device Update

Ở bài viết này, người viết "may mắn" vớ được một thiết bị phiên bản 3.1 và sẽ thực hiện update nó lên 5.0. Như đã đề cập, ta sẽ có 2 bước: Software Update và Disk Firmware Update.

## a. Software Update:

Ta mở **Azure Portal** lên, chọn vào **StorSimple Device Manager** và chọn vào thiết bị StorSimple cần Update.

![](https://cloudcraft.info/wp-content/uploads/2018/08/UpdateDeviceStorSimple-00.png)

Có thể thấy Azure đã tự check và hiển thị thông báo "New Software Update Available".

![](https://cloudcraft.info/wp-content/uploads/2018/08/UpdateDeviceStorSimple-01.png)

Tuy nhiên không nên dễ tin người như thế. Ta vào **Settings** > **Device Updates** và click **Scan** để make sure lại một lần nữa.

![](https://cloudcraft.info/wp-content/uploads/2018/08/UpdateDeviceStorSimple-02.png)

Sau khi check, click chọn **Install** **Updates** > **Install** và chờ một khoảng thời gian để quá trình update diễn ra.

![](https://cloudcraft.info/wp-content/uploads/2018/08/UpdateDeviceStorSimple-03.png) Khi xong xuôi sẽ có 1 cái tick xanh thế này. ![](https://cloudcraft.info/wp-content/uploads/2018/08/UpdateDeviceStorSimple-04.png)

## b. Disk Firmware Update

Giờ đến lượt Disk Firmware Update. **Bước 1** , ta mặc áo khoác vào và xách mông lên DC để cắm dây serial. 

Đối với mỗi một thiết bị StorSimple, ở mức phần cứng, ta đều có 2 **Controllers** (phần cứng điều khiển thiết bị) là **Controller 0** và **Controller 1** chạy H.A (1 active, 1 passive). Tương ứng, ta sẽ có 2 Serial Ports. Quá trình Disk Firmware Update sẽ diễn ra **_tuần tự_** trên cả hai Controllers. Tuy nhiên, ta chỉ cần cắm serial và cấu hình một Controller trước, Controller còn lại sẽ tự hiểu và thực hiện update liền sau đó.

Sau khi cắm dây, đối với máy Windows, ta có thể vào **Device Manager** và check xem thiết bị đã được nhận chưa, đồng thời ghi nhớ lại thông tin kết nối.

![](https://cloudcraft.info/wp-content/uploads/2018/08/UpdateDeviceStorSimple-05.png)

**Bước 2** , ta cần một tool terminal nào đó. Để đơn giản, người viết sẽ dùng **Putty**. Tại Config của Putty, mở đến thẻ **Connection** > **Serial** và cấu hình với các thông số sau.

  * Serial Line to connects to: tên kết nối ở bước 1.
  * Speed: 115,200
  * Data bits: 8
  * Stop bits: 1
  * Parity: None
  * Flow control: None

![](https://cloudcraft.info/wp-content/uploads/2018/08/UpdateDeviceStorSimple-06.png) Nhấn **Open** để kết nối đến thiết bị. 

**Bước 3** : Sau khi kết nối thành công, chọn option **1\. Log in with full access.** và nhập password để chứng thực. Vậy là ta đã kết nối đến **Regular Mode** của thiết bị. Ta có thể thấy được thông tin Controller (0 hay 1, Active hay Passive).

**Bước** 4, trước khi thực hiện Disk Firmware Update, ta check lại xem trạng thái của Software Update bằng lệnh: `Get-HcsSystem` Nếu output sau xuất hiện thì chúc mừng bạn đã thực hiện thành công phần Software Update cho Update 5.0 `FriendlySoftwareVersion: StorSimple 8000 Series Update 5.0` `HcsSoftwareVersion: 6.3.9600.17845` Tiếp theo, ta có thể check xem có bản update nào đang available cho thiết bị không bằng lệnh: 

`Get-HcsUpdateAvailability`

Kết quả sẽ trả về True/False cho cả **Regular Updat** e (Software Update) và **Maintenance Mode Update** (Disk Firmware Update). Tùy vào kết quả mà ta sẽ phải double-check lại Regular Update với Azure Portal (có thể thông tin chưa kịp đồng bộ giữa hai đầu on-cloud và on-prem). Nếu Regular Update bằng False và Maintenance Mode Update bằng True, ta có thể yên tâm để thực hiện Disk Firmware Update.

**Bước 5** , dùng lệnh sau để vào Maintenance Mode: `Enter-HcsMaintenanceMode`

Lúc này, cả hai Controller sẽ **tự động reboot** vào Maintenance Mode (qui trình nó thế, đừng lo lắng :D ). Sau khi reboot, ta lại chọn option **1\. Log in with full access** và nhập password.

**Bước 6** , gõ lệnh sau để bắt đầu quá trình Update. `Start-HcsUpdate`

Nhấn **Y** để xác nhận lần cuối. Nếu suôn sẻ, quá trình Update sẽ chỉ vài giờ là hoàn tất.

![](https://cloudcraft.info/wp-content/uploads/2018/08/UpdateDeviceStorSimple-08.jpg)

Trong quá trình update, ta sẽ không thể thực hiện thêm được bất cứ thao tác gì. Để tracking, ta có thể cắm Serial sang Controller còn lại để theo dõi quá trình Update. Sau khi đã kết nối đến Controller còn lại, ta gõ lệnh:

`Get-HcsUpdateStatus`

Nếu RunInProgress là True thì quá trình đang diễn ra thuận lợi. Nếu False thì có thể Update đã xong. Sau khi hoàn tất, cả hai Controller sẽ lại đồng loạt tự động reboot một lần nữa (theo dõi đèn thiết bị).

**Bước 7** , sau khi thiết bị online trở lại, lần lượt connect vào hai Controller và gõ lệnh `Get-HcsFirmwareVersion` Nếu kết quả là một trong các version sau thì có nghĩa là đã thành công: `XMGJ, XGEG, KZ50, F6C2, VR08, N003, 0107`. Có thể gõ lại lệnh `Get-HcsUpdateAvailabilit` để kiểm tra. ![](https://cloudcraft.info/wp-content/uploads/2018/08/UpdateDeviceStorSimple-07.png) Nếu tất cả đều False là đúng. **Bước 8** , ta nhẹ nhàng gõ lệnh sau để thiết bị thoát khỏi Maintainance Mode: `Exit-HcsMaintenanceMode` Thiết bị sẽ...một lần nữa tự reboot lân cuối. :D. 

# Kết

Đọc đến đây thì chắc bạn sẽ thắc mắc vậy thì qui trình này có quái gì khó để phải viềt ra ? Đúng, qui trình này thật chất không có gì phức tạp lắm nếu bạn nắm rõ các bước và các tình huống sẽ xảy ra (chẳng hạn như việc cứ reboot đi reboot lại liên tục). Lời khuyên ở đây là giữ một... đường truyền Internet ổn định và một cái đầu lạnh (check đi check lại thật kĩ trước khi click bất cứ điều gì, không ổn là pause lại ngay). Nếu đủ "may mắn", bạn sẽ có dịp gọi điện thoại trực tiếp với support cấp vùng hoặc thậm chí là... Product Engineer của StorSimple. Những anh chàng Ấn Độ này khá vui tính, nói sõi tiếng Anh và cũng rất quyền năng. Quyền năng ở đây đúng theo nghĩa đen. Ngoài hai Mode làm việc chính, StorSimple có hẳn một Mode ẩn dành riêng cho team Support và chỉ có team Support mới mở được (nghe chẳng khác gì backdoor :D ). Tuy vậy, ta có thể tạm tin người vì muốn vào mode này cần có key. Key này được tạo từ một tool riêng của team Support, nhưng để tạo ra key này, lại cần input là một key gen ra từ thiết bị StorSimple do người dùng cung cấp. Tuy vậy, chắc chẳng ai muốn trải nghiệm qui trình này :D

Vì lười, nên mình xin hết, các bạn có thể tham khảo thêm link hướng dẫn chính chủ của MCS: [Hướng dẫn chính chủ từ 2017 của MCS](https://docs.microsoft.com/en-us/azure/storsimple/storsimple-8000-install-update-5#install-update-5-via-the-azure-portal).
