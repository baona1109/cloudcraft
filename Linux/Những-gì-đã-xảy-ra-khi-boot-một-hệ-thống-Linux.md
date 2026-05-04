---
title: "Những gì đã xảy ra khi boot một hệ thống Linux?"
date: 2022-06-20 16:20:15
categories: [Linux]
---

Có thể bạn đã biết, một hệ thống thì bao gồm nhiều thành phần hỗ trợ lẫn nhau để có thể làm việc. Trong bài viết này, chúng ta sẽ cùng tìm hiểu về quá trình khởi động của một hệ thống Linux từ lúc bạn nhấn nút nguồn để bật hệ thống lên cho tới khi bạn có thể sử dụng hệ thống để thực hiện các công việc của mình. Quá trình này chỉ diễn ra trong vòng vài ba phút (hoặc ít hơn) nhưng đằng sau đó là cả một quy trình xử lý và tính toán phức tạp. ![](http://cloudcraft.info/wp-content/uploads/2022/06/swap-la-gi-tong-quan-ve-swap-1.jpg) Việc hiểu được các bước trong quá trình khởi động máy Linux có thể giúp bạn phần nào trong việc xác định và khắc phục các sự cố liên quan tới hoạt động khởi động của hệ thống. **Lưu ý:** những mô tả trong bài viết này chỉ ở mức khái quát chung (high-level), không thật sự đi sâu vào từng tiểu tiết xảy ra ở từng giai đoạn khởi động. 

## **QUÁ TRÌNH KHỞI ĐỘNG HỆ THỐNG LINUX**

![](http://cloudcraft.info/wp-content/uploads/2022/06/nhung-gi-da-xay-ra-khi-boot-mot-he-thong-linux-2.png)

### **(1) Power-on:**

BIOS là phần mềm được cài đặt sẵn (embedded) vào các chip PROM, EPROM hay bộ nhớ flash nằm trên bo mạch chủ, là chương trình được chạy đầu tiên khi bạn nhấn nút nguồn hoặc nút reset trên hệ thống của mình. BIOS thực hiện một công việc gọi là POST (Power-on Self-test) nhằm kiểm tra thông số và trạng thái của các phần cứng hệ thống khác như bộ nhớ, CPU, thiết bị lưu trữ, card mạng… Đồng thời, BIOS cũng cho phép bạn thay đổi các thiết lập, cấu hình của nó (tùy từng máy mà bạn nhấn phím F2, Delete, F10,… để vào giao diện cài đặt cho BIOS). Nếu quá trình POST kết thúc thành công (tức, các phần cứng ở trạng thái tốt, BIOS không phát hiện ra các trục trặc nào), thì sau đó BIOS sẽ cố gắng tìm kiếm và khởi chạy (boot) một hệ điều hành được chứa trong các thiết bị lưu trữ như ổ cứng, CD/DVD, USB…. Thứ tự tìm kiếm có thể được thay đổi bởi người dùng trong BIOS Setup. 

### **(2) Master Boot Record (MBR):**

Sector đầu tiên (được đánh số 0) của một thiết bị lưu trữ dữ liệu được gọi là MBR, thường sector 0 này có kích thước là 512-byte. Sau khi BIOS xác định được thiết bị lưu trữ nào sẽ được ưu tiên để tìm kiếm đầu tiên thì thực chất BIOS sẽ đọc trong MBR của thiết bị này để nạp vào bộ nhớ một chương trình rất nhỏ (dưới 512-byte). Chương trình nhỏ này sẽ định vị và khởi động boot loader – đây là chương trình chịu trách nhiệm cho việc tìm và nạp nhân (kernel) của hệ điều hành. Chú ý, hệ điều hành sẽ được nạp bởi boot loader không nhất thiết phải nằm chung thiết bị lưu trữ với boot loader đó, chẳng hạn như việc bạn cài boot loader và sda nhưng OS lại nằm ở sdb vậy. (những năm trước đây có thể bạn đã từng nghe tới _đĩa mềm khởi động_ , thực ra chúng chỉ chứa boot loader mà thôi.) 

### **(3) Boot loader:**

Có 2 bootloader phổ biến trên Linux là GRUB và LILO (tiền thân của GRUB). Cả 2 chương trình này đều có chung mục đích: cho phép bạn lựa chọn một trong các hệ điều hành có trên hệ thống để khởi động, sau đó chúng sẽ nạp kernel của hệ điều hành đó vào bộ nhớ và chuyển quyền điều khiển hệ thống cho kernel này. GRUB hay LILO đều có thể khởi động cho cả Linux và Windows, nhưng ngược lại các bootloader trên Windows như (NTLDR, BOOTMGR) thì không hỗ trợ khởi động cho các hệ điều hành Linux. Trong các hệ thống Linux, các bootloader cũng có thể nạp thêm các ramdisk hoặc các INITRD, sẽ được đề cập ở dưới. 

### **(4) Linux kernel được nạp và khởi chạy:**

Kernel đóng vai trò để shell có thể giao tiếp và điều khiển phần cứng. Khi bootloader nạp một phiên bản dạng nén của Linux kernel, và ngay lập tức nó sẽ tự giải nén và tự cài đặt mình lên RAM – bộ nhớ hệ thống, nơi mà nó sẽ nằm ở đó cho tới khi bạn tắt máy. 

### **(5) Các script trong (các) INITRD được thực thi:**

Một vấn đề mà những nhà phát triển Linux phải đối mặt là không thể nào đoán trước được chính xác cấu trúc hệ thống của người sẽ sử dụng bản Linux của họ… Hệ thống của người dùng có những thành phần linh kiện nào? Các INITRD cung cấp một giải pháp: một tập các chương trình nhỏ sẽ được thực thi khi kernel vừa mới được khởi chạy. Các chương trình nhỏ này sẽ dò quét phần cứng của hệ thống và xác định xem kernel cần được hỗ trợ thêm những gì để có thể quản lý được các phần cứng đó. Chương trình INITRD có thể nạp thêm vào kernel các module bổ trợ. Khi chương trình INITRD kết thúc thì quá trình khởi động Linux sẽ tiếp diễn. 

### **(6) Chương trình init được thực thi:**

Khi kernel được khởi chạy xong, nó triệu gọi duy nhất một chương trình tên là init. Tiến trình này có PID (process ID) =1, init là cha của tất cả các tiến trình khác mà có trên hệ thống Linux này. Do tính chất cực kỳ quan trọng này mà init sẽ không bao giờ bị chết (khi sử dụng lệnh kill) và không được phép chết! Sau đó, init sẽ xem trong file /etc/inittab để biết được nó cần làm gì tiếp theo như: dựa vào runlevel mặc định để thực thi các script khởi động (initscript) tương ứng trong thư mục /etc/rc.d. Sau đây là các run level trên Linux 

  * Run level 0 (init 0): tắt máy.
  * Run level 1 (init 1): chế độ này chỉ sử dụng được 1 người dùng.
  * Run level 2 (init 2): chế độ đa người dùng nhưng không có dịch vụ NFS.
  * Run level 3 (init 3): chế độ đa người dùng, có đầy đủ các dịch vụ.
  * Run level 4 (init 4): _*không sử dụng*_
  * Run level 5 (init 5): chế độ đồ họa.
  * Run level 6 (init 6): khởi động lại máy.

Cú pháp sử dụng: `_# init 6_`
