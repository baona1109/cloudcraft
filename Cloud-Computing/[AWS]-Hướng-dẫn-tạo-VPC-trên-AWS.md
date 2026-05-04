---
title: "[AWS] Hướng dẫn tạo VPC trên AWS"
date: 2023-02-23 10:03:25
categories: [Cloud Computing, AWS]
---

Chào các bạn, hôm nay mình sẽ hướng dẫn các bạn tạo một VPC cơ bản trên AWS. Trước khi vào bài hướng dẫn, các bạn cần nắm rõ một số khái niệm như VPC, Subnet, Route table, Internet gateway (IGW) là gì để hiểu rõ chức năng của từng thành phần. Các bạn có thể tham khảo bài viết bên dưới để tìm hiểu các khái niệm này. <https://cloudcraft.info/mot-lan-di-len-may-dao-mot-vong-aws-networking/> ![](http://cloudcraft.info/wp-content/uploads/2023/02/vpc-12-300x119.png) Bước 1: Tạo VPC Các bạn truy cập vào dịch vụ VPC chọn mục **Yours VPCs** trong **Virtual private cloud** , sau đó chọn nút **Create VPC** để tạo VPC mới. ![](http://cloudcraft.info/wp-content/uploads/2023/02/vpc-1-300x230.png)

  * Ở mục **Name tag** , các bạn sẽ đặt tên cho VPC.
  * Ở mục **IPv4 CIDR** , các bạn sẽ xác định range IPv4.
  * Ở mục **Tenancy** , sẽ có 2 option:
    * **Default** : Sử dụng VPC này cho EC2 Instance.
    * **Dedicated** : Sử dụng VPC này cho Dedicated Instance.

Sau khi tùy chọn các thông số, các bạn chọn nút **Create VPC** để tạo VPC. Bước 2: Tạo subnet Các bạn truy cập mục **Subnets,** click chọn nút **Create subnet** để tạo public và private subnet cho VPC vừa tạo.  Ở mục **VPC ID** , các bạn chọn VPC vừa tạo ở bước 1. ![](http://cloudcraft.info/wp-content/uploads/2023/02/vpc-2-300x119.png) Ở mục **Subnet settings** , các bạn tạo subnet tùy theo nhu cầu sử dụng của từng người. Ở bài hướng dẫn này, mình sẽ tạo 2 subnet là public và private subnet. Public subnet sẽ được sử dụng cho các kết nối Internet, còn private subnet sẽ được sử dụng cho giao tiếp nội bộ giữa các server. ![](http://cloudcraft.info/wp-content/uploads/2023/02/vpc-3-300x241.png)

  * Ở mục **Subnet name** , các bạn đặt tên cho subnet.
  * Ở mục **Availability Zone** , tùy chọn region mà các bạn muốn tạo EC2 instance
  * Ở mục **IPv4 CIDR** , các bạn thực hiện chia nhỏ range IPv4 mà mình đã xác định ở VPC

Với subnet thứ 2 mình cũng làm tương tự. ![](http://cloudcraft.info/wp-content/uploads/2023/02/vpc-4-300x234.png) Bước 3: Bật tính năng **Auto-assign public IPv4 address** trên public subnet Đối với public subnet để bật tính năng **Auto-assign public IPv4 address** , các bạn làm như sau: Truy cập mục **Subnets** , chọn public subnet → click nút **Actions** → chọn **Edit subnet settings.** ![](http://cloudcraft.info/wp-content/uploads/2023/02/vpc-5-300x88.png) ![](http://cloudcraft.info/wp-content/uploads/2023/02/vpc-6-300x86.png) Mặc dù đã có IP public nhưng hiện tại instance sử dụng public subnet vẫn chưa thể truy cập Internet được. Vậy nên chúng ta cần phải tạo thêm một **Internet Gateway** cho VPC**.** Bước 4: Tạo **Internet Gateway (IGW)** Truy cập mục **Internet Gateway** , click nút **Create internet gateway** để tạo Internet Gateway (IGW) mới vì một IGW chỉ có thể attach được một VPC và ngược lại. ![](http://cloudcraft.info/wp-content/uploads/2023/02/vpc-7-300x227.png) Các bạn truy IGW vừa tạo, chọn nút **Action** → **Attach to VPC** ![](http://cloudcraft.info/wp-content/uploads/2023/02/vpc-8-300x115.png) ![](http://cloudcraft.info/wp-content/uploads/2023/02/vpc-9-300x131.png) Sau khi tạo IGW, các bạn phải chỉnh sửa lại Route Table của VPC thì các instance sử dụng public subnet mới có thể kết nối Internet. Bước 5: Thêm rule vào Route Table của VPC Truy cập mục **Route Tables** , chọn Route Table của VPC vừa tạo. Chọn tab **Routes** , click nút**Edit routes.** Thêm rule 0.0.0.0/0 đến IGW vừa tạo và click nút**Save Changes** để lưu thay đổi. ![](http://cloudcraft.info/wp-content/uploads/2023/02/vpc-10-300x77.png) Như vậy là các instance đang sử dụng public subnet đã có thể kết nối được Internet. Chúc các bạn thành công.
