---
title: "Hướng dẫn sử dụng mRemoteNG để Remote Desktop"
date: 2019-04-24 15:28:04
categories: [Windows]
---

# Hướng dẫn sử dụng mRemoteNG để Remote Desktop

Nếu các bạn thường xuyên phải làm việc với số lượng lớn server Windows thì việc sử dụng tool Remote Desktop có sẵn của Microsoft khá là bất tiện, đặc biệt là khi bạn cần phải thao tác cùng lúc trên nhiều server, cần phải chuyển đổi qua lại giữa các connection.

![mremoteng-0](https://cloudcraft.info/wp-content/uploads/2018/12/mremoteng-0.jpg)_Remote Desktop, người bạn đường lâu năm, tiện nhưng không lợi_

Trong bài viết này, mình sẽ hướng dẫn các bạn cách sử dụng và cài đặt mRemoteNG giúp cho việc Remote Desktop trên Windows được dễ dàng và thuận tiện hơn.

## Giới thiệu về mRemoteNG

mRemoteNG (Multi-Remote Next Generation) là 1 phần mềm mã nguồn mở, được dùng để quản lý các remote connection. Một số ưu điểm nổi trội của mRemoteNG so với trình Remote Desktop của Microsoft là:

  * Gọn nhẹ.
  * Quản lý được nhiều tab RDP cùng 1 lúc.
  * Quản lý các connection dễ dàng theo dạng cây thư mục.
  * Hỗ trợ nhiều loại giao thức khác nhau.
  * Và quan trọng nhất là hoàn toàn miễn phí!!!



Hiện nay, mRemote hỗ trợ nhiều giao thức khác nhau như:

  * RDP
  * VNC
  * ICA (Citrix)
  * SSH (Tích hợp với Putty)
  * Telnet
  * HTTP/HTTPS
  * rlogin
  * Raw Socket Connections



Mình sẽ tập trung nói tới tính năng RDP của mremoteNG trong bài này. Các bạn có thể tự tìm hiểu các giao thức khác của tool, cách cấu hình cũng tương tự.

Để cài đặt mRemoteNG trên Windows khá đơn giản, các bạn có thể tải về file cài đặt trên trang chủ của project hoặc trên GitHub:

  * [GitHub](https://github.com/mRemoteNG/mRemoteNG/releases)
  * [Trang chủ của project](https://mremoteng.org/download)



mRemoteNG hiện chỉ cài được trên Windows (chia buồn với team Linux và Mac :( ), hỗ trợ từ phiên bản Win 7 và Windows Server 2008 trở về sau (bạn vẫn có thể dùng mRemoteNG để RDP tới các phiên bản Windows cũ bình thường).

## Hướng dẫn cài đặt

Cài đặt tool đơn giản như cài đặt những phần mềm bình thường khác. Chạy file .msi tải về từ trang chủ, next next, pặc pặc là xong.

Mặc định thì trình cài đặt sẽ cài luôn Putty để hỗ trợ SSH trên mRemoteNG.

![mremoteng-1](https://cloudcraft.info/wp-content/uploads/2018/12/mremoteng-1.jpg)_Next, Next, Next = > Install_

## Hướng dẫn sử dụng

Trước hết để có thể sử dụng RDP để remote vào server, ta cần phải cấu hình cho 1 connection trước. Ta chọn **File = > New Connection** để khởi tạo một kết nối mới với tên là **Test**

![mremoteng-4](https://cloudcraft.info/wp-content/uploads/2018/12/mremoteng-4.png)_Tạo và lưu 1 kết nối mới_

![](https://cloudcraft.info/wp-content/uploads/2018/12/mremoteng-5.png) _Nhập các thông số cấu hình cho kết nối mới_

Việc cấu hình và cài đặt khá đơn giản, sau khi đã nhập các thông tin cần thiết như username/pass, protocol, IP hoặc domain. Ta click chuột vào kết nối để đăng nhập vào server.

**Một số hình ảnh khác** ![mremoteng-2](https://cloudcraft.info/wp-content/uploads/2018/12/mremoteng-2.jpg)

_Giao diện mRemoteNG, các connection được lưu lại và tổ chức dưới dạng cây thư mục_

![](https://cloudcraft.info/wp-content/uploads/2018/12/mremoteng-3.jpg)_Giao diện quản trị multi-tab của tool_

Hy vọng qua bài viết này, các bạn sẽ có 1 thêm tool để dễ dàng quản trị Windows hơn. mRemoteNG cũng hỗ trợ quản lý connection SSH qua putty, nhưng cá nhân mình thấy khá chuối (mình ko thích putty cho lắm) nên chủ yếu dùng tool này để quản lý Windows là chính. Các bạn có thể dùng tool này để quản lý các kết nối khác như VNC hoặc ICA.... Chúc các bạn thành công ^^.
