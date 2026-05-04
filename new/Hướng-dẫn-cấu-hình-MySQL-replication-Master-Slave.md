---
title: "Hướng dẫn cấu hình MySQL replication Master-Slave"
date: 2017-12-25 14:54:01
categories: [Database]
---

Xin chào tất cả các bạn. Hôm nay, Cloudcraft sẽ hướng dẫn các bạn cấu hình **MySQL replication** đơn giản để dùng cho backup database thời gian thực nhé.

![](https://cloudcraft.info/wp-content/uploads/2017/12/mysql-replication-master-slave-1-1024x526.jpg)

# **Phần 1: Nói nhảm (Review)**

Ở đây đơn giản mình chỉ làm đơn giản 1 master và 1 slave thôi, các slave còn lại các bạn làm tương tự.

Môi trường cấu hình gồm 2 máy ảo CentOS chạy MySQL 5.6, đương nhiên các bạn hoàn toàn có thể cấu hình trên các VPS, mô hình này đơn giản và không phụ thuộc vào môi trường cấu hình nhé các bạn. Có MySQL là chơi được tất.

Tuy nhiên, nhược điểm của MySQL replication kiểu này là chỉ ở mức đơn giản, với hệ thống lớn thì chắc chẳng ai dùng cả đâu. Cần lưu ý rằng, thiết lập MySQL replication cần thực hiện trước khi website của bạn đi vào hoạt động, nếu không việc dữ liệu được thao tác liên tục trên các databases nào đó bạn dùng để replicate thì sẽ nảy sinh các vấn đề vô cùng phức tạp. Vì tính năng này không có đồng bộ và kiểm tra đúng sai đâu nhé. Nó dựa vào một bảng danh sách các thay đổi theo thời gian, rồi replicate dựa theo thời gian chúng ta chọn cho nó. Quá tệ đúng không =)).

# **Phần 2: Cấu hình (Configuration)**

Gửi các bạn một số thông tin IP (IP này dùng local, với IP public cũng tương tự)

  * Master:



OS: CentOS 6

IP: 192.168.100.129

MySQL: 5.6.36

  * Slave:



OS: CentOS 7

IP: 192.168.100.131

MySQL: 5.6.36

**\- Tại Master ta cấu hình như sau:**

Cấu hình Master trên config của mysql theo mặc định tại **/etc/my.cnf** như sau:

`vim /etc/my.cnf`

Thêm nội dung:

`[mysql]` `server-id=1` `log_bin=/var/log/mysql/mysql-bin` `bind-address=192.168.100.129`

Giải thích:

  * server-id: là số định danh duy nhất cho một thành phần tham gia vào replication, không nhất thiết master phải là "1".
  * log_bin: là tập tin sẽ chứa các sự thay đổi khi mà các database được cập nhật.
  * bind-address: là tham số xác định mysql sẽ bind ở địa chỉ nào.



Sau khi hoàn tất cấu hình Master, ta khởi động lại mysql

`service mysqld restart`

Bạn có thể kiểm tra việc master tạo các mysql-bin như thế nào như sau:

`ls -l /var/log/mysql/`

![](https://cloudcraft.info/wp-content/uploads/2017/12/mysql-replication-master-slave-2.png)

Mỗi tập tin chứa sự thay đổi của database sẽ có prefix là "mysql-bin.", suffix là "00000X". Số X sẽ tăng dần mỗi đơn vị khi mà ta Start hoặc Restart mysql

Bây giờ sử dụng mysql để lấy thông số trạng thái thay đổi database trên master, để nhằm mục đích là đặt mốc bắt đầu replication trên slave. Để cố định mốc này, ta sẽ lock tạm thời master về chế độ Read Only.

`mysql`

hoặc

`mysql -u root -p`

`flush tables with read lock;`

`set global read_only = ON;`

`show master status;`

![](https://cloudcraft.info/wp-content/uploads/2017/12/mysql-replication-master-slave-3.png)

Trong này ghi mysql-bin.000007 và 262, thì có thể hiểu master đã thay đổi theo mysql-bin.000007 tại vị trí 262. Bạn cần nhớ số này để thiết lập bên slave, tuy nhiên vấn đề cũng thực sự bắt nguồn từ việc chọn file và possition để cho slave biết khi nào thì bắt đầu replication, nếu chọn quá sớm thì sẽ bị trùng lấp dữ liệu đã có, gây lỗi ở Slave, nếu chọn quá muộn có thể làm thiếu mất dữ liệu (records hoặc tables nào đó).

Cấp quyền cho một user Slave kết nối với Master

`GRANT REPLICATION SLAVE ON *.* TO 'slave'@'%' IDENTIFIED BY 'password';`

`FLUSH PRIVILEGES;`

Dump tất cả các database đang có trên master.

mysqldump --all-databases --single-transaction -u root -p > /root/all_databases.sql

Sau khi dump xong, ta có thể gỡ lock ở trên:

  * Master:



`mysql -u root -p`

`set global read_only = OFF;`

`unlock tables;`

**\- Tại Slave ta cấu hình như sau:**

Sao chép tập tin dump từ Master sang Slave:

`scp root@192.168.100.129:/root/all-databases.sql /root`

Ta import trở lại cho Slave

mysql -u root -p < /root/all-databases.sql

Cấu hình Slave trên config của mysql theo mặc định tại **/etc/my.cnf** như sau:

`vim /etc/my.cnf`

Thêm nội dung:

`[mysqld]` `server-id=2` `log_bin=/var/log/mysql/mysql-bin` `relay-log=/var/log/mysql/mysql-relay-bin`

Giải thích:

  * relay-logs: tương tự như log_bin, nếu bạn không cấu hình cũng không sao nhưng nếu không thiết lập cũng sẽ gây một số vấn đề phức tạp khác.



Sau khi hoàn tất cấu hình Master, ta khởi động lại mysql

`service mysqld restart`

Kết nối Slave mới master

`mysql`

`CHANGE MASTER TO MASTER_HOST='192.168.100.129',MASTER_USER='slave', MASTER_PASSWORD='password', MASTER_LOG_FILE='mysql-bin.000007', MASTER_LOG_POS=262;`

Lúc này mới cần đến file và possition lấy được từ "show master status;"

Khởi động Slave

`START SLAVE;`

Xem trạng thái Slave

`SHOW SLAVE STATUS\G`

Nếu như Slave_IO_Running và Slave_SQL_Running đều Yes hết là Slave đã kết nối và đang replicate thành công nha các bạn.

![](https://cloudcraft.info/wp-content/uploads/2017/12/mysql-replication-master-slave-4.png)

Nếu như kết nối đến Master gặp vấn đề, các bạn cần check lại port mysql trên Master đã listen hay chưa, iptables có chặn kết nối hay không. Kiểm tra xong ta chỉ cần start lại Slave là được nhé.

**"Một số vấn đề khác mình hẹn trong Phần 3 (nếu có) để giải thích hết toàn bộ vướng mắc về sự lựa chọn file và possition, khác nhau giữa log_bin và relay-log (ở đây mình không có viết sai đâu, log_bin thì dùng gạch chân, còn relay-log là gạch ngang). Cũng như chỉ replication một vài database thôi chứ không phải toàn bộ databases theo hướng dẫn trên. Hoặc replication toàn bộ nhưng trừ một số database nào đó."**
