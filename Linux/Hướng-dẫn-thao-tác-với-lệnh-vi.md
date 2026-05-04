---
title: "Hướng dẫn thao tác với lệnh vi"
date: 2022-06-20 15:30:21
categories: [Linux]
---

## **Giới thiệu lệnh vi**

Đối với người dùng Linux, lệnh **vi** đã không còn xa lạ gì và còn là ứng dụng soạn thảo, chỉnh sửa văn bản không thể thiếu. **vi** \- viết tắt của **Vi** sual, là một ứng dụng terminal phổ biến trên hệ điều hành Linux/Unix với nhiều phím tắt chức năng khá đa dạng và hữu ích, với hai chế độ hoạt động chính là **Insert** và **Command**. 

## **Hướng dẫn sử dụng lệnh vi**

Vì là một ứng dụng terminal, để sử dụng lệnh **vi** , ta cần mở một cửa sổ dòng lệnh và dùng cú pháp căn bản sau: `_# vi /path/to/file_` Trong đó, _/path/to/file_ là đường dẫn đến file cần chỉnh sửa. Nếu đường dẫn file trên không tồn tại, ứng dụng sẽ tự tạo file tạm và sẽ lưu vào ổ cứng khi người dùng thực hiện thao tác lưu. **Lưu ý:** để ứng dụng tự tạo file mới thì đường dẫn thư mục chứa file đó buộc phải tồn tại trước đó. Lệnh **vi** hỗ trợ rất nhiều phím tắt chức năng khác nhau nên bài viết này mình sẽ không giải thích tất cả mà chỉ nói qua các thao tác mà mọi người hay sử dụng hàng ngày. 

  * Chế độ Insert

Mặc định, khi mở một file bất kì bằng lệnh **vi** , file đó sẽ ở chế độ **Visual**. Ở chế độ này, người dùng chỉ có thể đọc văn bản và truyền các phím tắt. Để nhập liệu vào văn bản, ta cần chuyển sang chế độ **Insert** , cụ thể, ta sẽ bấm phím **< I >** trên bàn phím. ![](http://cloudcraft.info/wp-content/uploads/2022/06/huong-dan-thao-tac-voi-lenh-vi-1.png) Để quay lại chế độ **Visual** , ta bấm phím **Esc** trên bàn phím. 

  * Hiển thị số dòng

Ở chế độ **Visual** , ta nhập phím tắt **:set nu** hoặc **:set number** để hiển thị số dòng: ![](http://cloudcraft.info/wp-content/uploads/2022/06/huong-dan-thao-tac-voi-lenh-vi-2.png) Để ẩn đi số dòng, ta nhập **:set nonu** hoặc **:set nonumber**

  * Di chuyển tới một dòng bất kì

Ở chế độ **Visual** , ta nhập **: <số dòng>** để di chuyển tới dòng bất kì: ![](http://cloudcraft.info/wp-content/uploads/2022/06/huong-dan-thao-tac-voi-lenh-vi-3.png)

  * Di chuyển tới đầu file hoặc cuối file

Để di chuyển lên đầu file, ta bấm hai lần phím **< G >** Để di chuyển đến cuối file, ta bấm **Shift + < G >**

  * Tìm kiếm chuỗi kí tự

Để tìm kiếm một chuỗi kí tự, ta dùng cú pháp: **/ <chuỗi kí tự>** ![](http://cloudcraft.info/wp-content/uploads/2022/06/huong-dan-thao-tac-voi-lenh-vi-4.png) Để tiếp tục tìm vì trị tiếp theo của chuỗi kí tự đó, ta chỉ cần nhập **< / >** và bấm enter. 

  * Copy, paste hay xóa một dòng

Để copy một dòng, ta bấm hai lần phím **< Y >** Để paste dòng đã copy, ta bấm phím **< P >.** Dòng được copy sẽ xuất hiện ngay dưới dòng đang có con trỏ. Để xóa một dòng, ta bấm hai lần phím **< D >**. Để xóa nhiều dòng liền nhau, ta dùng cú pháp **< xDD >**, trong đó **x** là số lượng dòng cần xóa. 

  * Undo thao tác trước đó

Để undo thao tác trước đó, ta bấm hai lần phím **< U >**

  * Lưu file

Để lưu file, ta có những cách sau: _ Lưu file đã chính sửa nhưng không tắt file: **:w** _ Lưu file đã chính sửa và tắt file: **:wq** _ Tắt file nhưng không lưu chỉnh sửa: **:q!**

  * Thay thế một chuỗi bất kì

_ Thay thế tất cả các chuỗi tìm thấy: :%s/<chuỗi cần tìm>/<chuỗi thay thế>/g ![](http://cloudcraft.info/wp-content/uploads/2022/06/huong-dan-thao-tac-voi-lenh-vi-5.png) _ Chỉ thay thế chuỗi kí tự ở dòng nhất định, ví dụ như dòng 18: :18s/<chuỗi cần tìm>/<chuỗi thay thế>/g ![](http://cloudcraft.info/wp-content/uploads/2022/06/huong-dan-thao-tac-voi-lenh-vi-6.png)
