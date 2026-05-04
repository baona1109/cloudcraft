---
title: "Swap là gì? Tổng quan về swap"
date: 2022-06-20 15:54:43
categories: [Linux]
---

## **Khái niệm**

Trên mọi hệ điều hành, ngoài lượng RAM được dành riêng để xử lý hệ điều hành đó, thì sẽ luôn có một khoảng RAM trống dùng để xử lý các tiến trình khác. Khi một process được thực thi, các file / đường dẫn cần thiết của process đó sẽ được load lên RAM và được xử lý. Tuy nhiên, lượng RAM trên server luôn là có hạn (hiển nhiên), vậy khi dung lượng còn trống của RAM không còn đủ để chứa những dữ liệu đó thì chuyện gì sẽ xảy ra? Nâng cấp RAM chứ còn làm gì nữa. Khi RAM hết memory, sẽ có một lựa chọn thay thế để giúp cho process của ta vẫn có thể hoạt động được. Cơ chế này đều có ở cả hệ điều hành Windows lẫn Linux. Khi lượng memory cần thiết để xử lý process không đủ, hệ điều hành sẽ “mượn” thêm memory từ một kho lưu trữ phụ (gọi là virtual memory) để chứa các nội dung không hoạt động (inactive). Nhờ đó, hệ thống của ta sẽ có thêm lượng memory trống để xử lý các process mới. Lượng memory phụ này được mượn từ ổ cứng và được gọi là Swap memory. Ở bài viết này, tớ sẽ mô tả từ A tới Ă các khía cạnh, ngóc ngách của Swap để các cậu hoang mang hơn nhé. <3 

## **Cách hoạt động**

Như tớ đã mô tả ở trên, swap memory là một phần riêng biệt của ổ cứng được sử dụng khi RAM hết memory. Đối với Linux, sẽ có một chương trình quản lý memory có nhiệm vụ xử lý công việc này. Khi RAM dần hết memory, chương trình trên sẽ thực hiện tìm kiếm những block dữ liệu inactive trên RAM mà không được sử dụng trong một khoảng thời gian dài. Sau khi tìm kiếm thành công, nó sẽ thực hiện bê block dữ liệu đó sang swap memory. Bằng cách này, không gian trên RAM sẽ được giải tỏa và nhờ đó các process cần thiết sử dụng memory hơn sẽ có đất để xài. Đối với Windows, thuật ngữ swap được thay thế bằng page, nhưng ý tưởng và cách hoạt động là tương tự. 

## **Các loại swap memory**

Có hai loại swap memory: 

  * Phân vùng swap (swap partition): là loại swap memory mặc định của hệ thống. Khi đó, một phần vùng của ổ cứng sẽ được dành cho việc swapping. Loại swap này có thể được tạo từ lúc cài đặt HĐH và tự động mount vào sử dụng khi khởi động.

![](http://cloudcraft.info/wp-content/uploads/2022/06/swap-la-gi-tong-quan-ve-swap-1.png)

  * Tập tin swap (swap file): là loại swap memory do ta tự tạo. Trong trường hợp ổ cứng không còn đủ dung lượng để tạo một phân vùng mới dành cho swap, ta có thể tự tạo một file swap dùng cho việc swapping. Loại swap này có thể được tạo sau khi cài đặt HĐH, và không được tự động mount mà phải mount tay hoặc mount qua fstab.



## **Tần suất thực hiện swap (swappiness) lý tưởng là bao nhiêu?**

Trên hệ điều hành Linux, ta có thể set tần suất thực hiện swap (swappiness) theo ý mình, và giá trị này sẽ thuộc khoảng từ 0 tới 100. Ví dụ, ta set giá trị swappiness thấp, hệ thống sẽ rất hiếm khi thực hiện swapping, chỉ thực hiện khi lượng memory trên RAM còn rất ít. Ngược lại, nếu ta set giá trị swappiness cao, hệ thống sẽ thực hiện swapping thường xuyên hơn dù memory trên RAM còn nhiều. Vậy ta nên set swappiness bao nhiêu là hợp lý? Câu trả lời là: không có giá trị swappiness lý tưởng cho mọi trường hợp. Đối với máy chủ vật lý sử dụng ổ cứng SSD gắn trực tiếp vào server, tốc độ của ổ cứng sẽ khá tốt, ta có thể set giá trí này ở khoảng 20 đến 60 (60 là giá trị được nhiều tài liệu khuyên dùng). Đối với máy ảo, tốc độ ổ cứng được share cho nhiều máy ảo khác, việc sử dụng memory của ổ cứng làm swap sẽ không đạt hiệu quả tốt, ta nên set giá trị swappiness thấp, ví dụ khoảng 0 đến 10. Tuy nhiên, ta không nên lạm dụng và trông cậy quá nhiều vào swap, bởi vì: 

  * Tốc độ của ổ cứng chậm hơn rất nhiều tốc độ của RAM, việc để các process được xử lý trên ổ cứng cũng sẽ rất chậm.
  * Swap chỉ là phương án dự phòng, hệ thống sẽ chỉ dùng tới swap khi memory trên RAM không còn đủ để đáp ứng, kết hợp với ý ở trên thì việc nâng cấp RAM cho hệ thống sẽ là phương án tối ưu và lâu dài hơn.



## **Lợi ích của việc sử dụng swap**

  * Swap có ích trong việc lưu trữ những block dữ liệu ít được truy xuất, từ đó giải phóng bớt memory trên RAM để RAM có không gian xử lý các chương trình có độ ưu tiên cao hơn.
  * Giúp RAM không bị hết dung lượng.
  * Là phương án backup trong trường hợp RAM hết dung lượng và ta chưa thể nâng cấp kịp thời.
  * Hỗ trợ một phần khi chạy các chương trình yêu cầu memory lớn.
  * Khi hệ thống vào trạng thái ngủ đông (hibernation), tất cả nội dung trên RAM sẽ được chép vào swap. Từ đó việc quay trở lại trạng thái trước ngủ đông sẽ thuận tiện hơn.


