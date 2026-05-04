---
title: "Giải thích tên dịch vụ trên Amazon Web Service (AWS)"
date: 2018-02-09 16:04:50
categories: [Cloud Computing, AWS]
---

Dịch vụ trên AWS được đặt tên theo 2 kiểu như sau: 

  * Tên viết tắt, như: EC2, S3, RDS...
  * Tên của vật thực, như: BeanStalk, Kinesis, Snowball...

Vậy làm sao để nhớ, chỉ có những người đã từng làm việc qua những dịch vụ này mới có thể nhớ nổi. Đối với người mới bắt đầu, việc thích ứng với những cái tên này thật sự gặp nhiều trở ngại. Cùng tôi tìm hiểu xem chúng có những ý nghĩa, và tại sao lại đặt ra như vậy nhé.  

STT | Tên dịch vụ  | Viết tắt/giải thích | Tên gợi nhớ  | Sử dụng  
---|---|---|---|---  
1 | EC2 | Elastic Compute Cloud | Amazon Virtual Servers | Chứa một số thứ mà user nghĩ đó là computer. Hay gọi nó là máy ảo cho dễ.  
2 | S3 | Simple Storage Service | Amazon Unlimited FTP Server | Chứa bất kì thứ gì, dân IT gọi là object. Chứa hình ảnh, file tĩnh, html, css, backup, file chia sẻ, còn gọi là persistent storage. Nhiều lắm! ( > ^-^ < )  
3 | RDS | Relational Database Service | Amazon SQL | Database thôi, chỉ có điều chứa từ MS SQL, MySQL, PostgresDB, OracleDB, đến MariaDB.  
4 | BeanStalk | Represent the stem of a bean plant, proverbially fast growing and tall. | Amazon App Service | Giống như AppService của Azure. Nói trắng ra là host web app, mobile app. Tự scale, tự HA, lớn lên theo nhu cầu, như "dây đậu" (beanstalk).  
5 | VPC | Virtual Private Cloud | Amazon Virtual Colocated Rack | Tách môi trường AWS ra thành nhiều môi trường nhỏ, gọi là private cloud. Của riêng bạn, không lầm lẫn với bất kì môi trường nào khác, có gateway, có security rule....  
6 | IAM | Identity and Access Management | Users, Keys and Certs | Quản lý users, key, certs. Tập trung, dễ sử dụng bởi nhiều dịch vụ khác, quản lý dễ hơn. Chỉ vậy thôi đấy.  
7 | SQS | Simple Queue Service | Amazon Queue | Hàng đợi, dịch sát nghĩa, giữ request lại theo thứ tự, tích hợp với các dịch vụ khác trong AWS như SNS. Giống RabbitMQ.  
8 | SNS | Simple Notification Service | Amazon Messenger | Gửi thông báo đến điện thoại, email hay SMS gì đấy tùy bạn cấu hình nhé!  
9 | EMR | Elastic MapReduce | Amazon Hadooper | Ứng dụng trong BigData. Tạm gọi là Hadooper  
10 | ECS | Elastic Container Service | Amazon Docker as a Service | Chạy container, tất cả xoay quanh containers đều có ở đây.  
11 | Kinesis | Represent an undirected movement of a cell, organism, or part in response to an external stimulus | Amazon High Throughput | Sử dụng trong trường hợp dữ liệu cần đưa đi với tốc độ cao, ví dụ như dùng cho việc phân tích dữ liệu hay lượt like hotgirl trên Facebook chẳn hạn, hoặc là lượt xem MV Lạc Trôi mới của Sơn Tùng.  
12 | SES | Simple Email Service | Amazon Transactional Email | Gửi email tự động như "Bạn đã mất tiền ở ví Bitcoin của mình", hoặc là "Website của bạn đã bị hack". Cũng có thể dùng để gửi email chúc ngủ ngon cho 100 cô người yêu mỗi đêm, nhưng không khuyến khích.  
13 | EBS | Elastic Block Store | Amazon EC2 Block Storage | Đĩa gắn vào máy ảo (EC2 ở trên) để chạy OS. Tưởng tượng chính là chiếc Samsung SSD 960 Pro vậy hay là Seagate 20GB 7200rpm thời tống cũng được.  
14 | Lambda | Function as a Service | AWS App Scripts | Function as a Service, mỗi lần chạy Function là tính tiền, không cần dựng môi trường như máy ảo rườm rà đối với 1 function bé tí, bắt event và thế là execute Function thôi.  
15 | Route53 | Route DNS (53) | Amazon DNS + Domains | Quản lý tất cả những bài toán nào liên quan đến DNS, bao gồm cả việc mua domain mới.  
16 | CloudFront | In Front of Cloud | Amazon CDN | CDN như Akamai, nhưng ít edge node hơn, hiệu năng chưa ai so sánh giữa CloudFront và Akamai. Chỉ biết là CloudFront dùng để stream các trận đấu của Việt Nam tại U23 Châu Á 2017 vừa rồi thôi. Có xem là có trải nghiệm CloudFront rồi đấy nhé.  
17 | Snowball | A ball of snow pressed together in the hands, especially for throwing | AWS Big Old Portable Storage | Dịch vụ chuyển dữ liệu hàng petabyte đến AWS DataCenter, bằng cách shipping phần cứng đến AWS thông qua UPS (công ty chuyển phát hàng hóa).  
  
Sẽ còn cập nhật...
