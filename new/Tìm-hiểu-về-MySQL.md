---
title: "Tìm hiểu về MySQL"
date: 2018-02-09 16:05:32
categories: [Database]
---

**Giới thiệu sơ lược về MySQL** **MySQL** là một loại cơ sở dữ liệu theo cấu trúc quan hệ (**RDBMS** – Relational Database Management System) dùng để lưu và quản lý khối lượng lớn dữ liệu. MySQL được gọi là cơ sở dữ liệu quan hệ vì nó sử dụng các cấu trúc bảng để lưu trữ dữ liệu, các bảng có mối quan hệ với nhau thông qua các khóa. Mysql là một hệ cơ sở dữ liệu theo cấu trúc quan hệ dễ dàng sử dụng và quản lý, MySQL thường được sử dụng cho nhiều công việc từ lớn tới nhỏ. Nó phổ biến vì nhiều lý do như sau: 

  * MySQL là mã nguồn mở, nó hoàn toàn miễn phí.
  * MySQL là một chương trình rất mạnh mẽ.
  * MySQL sử dụng một form chuẩn của ngôn ngữ dữ liệu nổi tiếng là SQL.
  * MySQL làm việc được trên nhiều hệ điều hành cùng với nhiều ngôn ngữ phổ biến như PHP, PERL, C, C++, Java...
  * MySQL làm việc nhanh và khỏe ngay cả với các tập dữ liệu lớn.
  * MySQL rất thân thiện với PHP, một ngôn ngữ thường được dùng để phát triển web

**Tạo cơ sở dữ liệu và người dùng** MySQL có thể hỗ trợ cho nhiều cơ sở dữ liệu khác nhau (CSDL), thường thì khi ta sử dụng sẽ có một CSDL cho mỗi ứng dụng và để quản lý từng CSDL riêng và phân biệt người ta thường tạo user và gán quyền của user đó đối với CSDL được tạo. Một CSDL có thể được gán cho nhiều user và mỗi user sẽ được phân một số quyền nhất định để thao tác trên CSDL đó. **Trước tiên ta cần tạo database:**
    
    
    create database <dbname>;

Với **dbname:** là tên của CSDL cần tạo **Lưu ý:** Khi gõ bất kì lệnh nào để thực thi trong giao diện console của mysql và để lệnh đó thực thi ta cần có dấu “**;** ” ở cuối lệnh. Đây là dấu kết thúc có ý bảo là câu lệnh đã hoàn thành và cần thực thi. VD: 
    
    
    mysql> create database test;
    
    Query OK, 1 row affected (0.00 sec)

Với ví dụ trên thì khi gõ lệnh create database test; đã tạo ra thành công database. **Về người dùng và các quyền:** Vì lý do bảo mật nên mysql chúng ta nên gán mật khẩu cho user có quyền thực thi trên CSDL. Ngoài ra ta cũng cần phân quyền đúng với mục đích của từng user đã được tạo ra. Mysql cung cấp vã hỗ trợ tất cả quyền phức tạp trên hệ thống của Mysql, ta có thể trao quyền cho một user rằng nó có thể và không thể làm gì đối với cơ sở dữ liệu được tạo. Để tạo user sử dụng lệnh **Create** : 
    
    
    CREATE USER ‘username’@’localhost’ identified by ‘password’;

Tuy nhiên việc này chỉ thực hiện tạo một user và user đó không có quyền gì cả, thậm chị người dùng cũng không thể sử dụng user này để vào được console của MySQL. Vì vậy khi tạo user ta cũng cần phải phân quyền cho user này bằng lệnh GRANT. Thực tế khi sử dụng lệnh **GRANT** để **phân quyền** thì nếu user không tồn tại thì hệ thống sẽ tạo user mới kèm với quyền được gán cho. Đây là một số quyền thường dùng:  **Quyền** | **Ý nghĩa**  
---|---  
**ALL PRIVILEGES** | Lệnh này cho phép user có toàn quyền trên database hoặc một vùng nào đó đã được phân (bảng của database)  
**CREATE** | Cho phép user có quyền tạo tables hoặc database mới  
**DROP** | Cho phép user có thể xóa tables hoặc database mới  
**DELETE** | Cho phép xóa bản ghi dữ liệu trong bảng  
**INSERT** | Cho phép chèn thêm bản ghi dữ liệu trong bảng  
**UPDATE** | Cho phép thay đổi nội dung của bản ghi dữ liệu trong bảng  
**SELECT** | Cho phép dùng lệnh select để tìm kiếm dữ liệu  
**GRANT OPTION** | Cho phép gán hoặc xóa quyền của người dùng khác.  
**Cú pháp phân quyền:**
    
    
    GRANT [Các loại quyền]
    
    ON [tên database].[tên bảng]
    
    TO ‘username’@’localhost’ IDENTIFIED BY ‘password’;

VD: 
    
    
    mysql> grant all privileges on test.* to 'testuser'@'localhost' identified by ‘abcdefgh’;

Với ví dụ trên ta sẽ phân toàn quyền cho testuser trên database có tên là test với tất cả các bảng (test.*). Mỗi khi thực hiện phân quyền xong ta cần làm mới lại quyền để có thể phân quyền cho user khác bằng lệnh: 
    
    
    FLUSH PRIVILEGES;

Có phân quyền thì cũng có thể **lấy lại quyền** đã phân cho user. Ta có thể dùng cú pháp **REVOKE** : 
    
    
    REVOKE [Các loại quyền]
    
    ON [tên database].[tên bảng]
    
    FROM ‘username’@’localhost’;

VD: 
    
    
    revoke create, drop on test.* from 'testuser'@'localhost';

Ở ví dụ này ta sẽ lấy lại 2 quyền là create và drop đã phân cho testuser Để thực hiện đổi mật khẩu cho user ta cần log vào user đó và thực hiện đổi với các bước sau (Sử dụng cú pháp **ALTER**): 
    
    
    [dangtgh@localhost log]$ sudo mysql -u testuser -p
    
    mysql> alter user 'testuser'@'localhost' identified by ‘newpassword'

Ngoài ra còn có một số lệnh quản lý CSDL, bảng và user:  **Lệnh** | **Ý nghĩa**  
---|---  
**mysql > show databases;** | Thực hiện in danh sách CSDL hiện có mà user có quyền ra màn hình (Cần phải có quyền)  
**mysql > use <tên CSDL>;** | Chuyển sang thao tác trên CSDL chỉ định  
**mysql > show tables;** | HIển thị danh sách bảng mà user có quyền thao tác trên CSDL đã chỉ định  
**mysql > describe tablename;** | Hiển thị đầy đủ chi tiết của một bảng cụ thể  
**mysql > show grants [for username]** | Hiển thị thông tin phân quyền của user. VD: show grants for ‘test’@’localhost’  
**Kiểu dữ liệu** Một số kiểu dữ liệu thông dụng thường gặp trong MySQL như sau: **\+ Kiểu dữ liệu số:** **Kiểu** | **Ý nghĩa**  
---|---  
**INT** | Kiểu số nguyên có thể lên tới 11 chữ số  
**TINYINT** | Kiểu số nguyên có độ rộng 4 chữ số  
**Float (M,D)** | Kiểu số thực với (M) là độ dài hiển thị và (D) là số vị trí sau dấu phẩy  
Ngoài ra còn một số kiểu như Double, Decimal, Smallint,... **\+ Kiểu dữ liệu date và time:** **Kiểu** | **Ý nghĩa**  
---|---  
**DATE** | Kiểu số dữ liệu ngày YYYY-MM-DD  
**DATE TIME** | Tổ hợp date và time định dạng YYYY-MM-DD HH:MM:SS  
**TIME** | Kiểu dữ liệu giờ HH:MM:SS  
Ngoài ra còn có một số kiểu: Timestamp, year(m),... **\+ Kiểu dữ liệu chuỗi:** **Kiểu** | **Ý nghĩa**  
---|---  
**VARCHAR(M)** | Kiểu chuỗi có độ dài từ 1 đến 255 kí tự  
**Tạo bảng trong CSDL**
    
    
    CREATE TABLE <tablename> (<Các cột của bảng với các loại dữ liệu từng cột>)

VD: 
    
    
    mysql> create table hocsinh (ho_ten varchar(20), ngay_sinh date, gioi_tinh varchar(5), sdt varchar(11), Email varchar(30));
    
    Query OK, 0 rows affected (0.02 sec)

**Backup dữ liệu** Để backup dữ liệu MySQL ta có thể sử dụng lệnh mysqldump để export database cần backup ra một file script sql chứa cấu trúc database và dữ liệu. 
    
    
    mysqldump –opt –u [uname] –p [dbname] > [backupfile.sql]

Trong đó: **[uname]:** user sở hữu có full quyền trên database cần export **[dbname]:** tên database cần export để backup (có thể thay thế thành cờ **\--all-databases** để có thể export một lúc nhiều databases) **\--opt:** các tùy chọn mysqldump **Lưu ý:** \+ Nếu dùng cờ **-p** thì nếu user có password thì phải nhập password đúng để lệnh dump có thể được chạy. Nếu không muốn nhập password sau khi chạy lệnh mysqldump thì có thể thay **–p** thành **–password=’yourpassword’** mysqldump –opt –u [uname] –password=’yourpassword’ [dbname] > [backupfile.sql] \+ **\--all-databases** cho phép dump một lúc nhiều database, sau cờ này ta có thể liệt kê tên những database cần dump (Có thể dùng **\--databases [db1] [db2]**... nếu dùng **\--all-databases** thì không cần chỉ đích danh database nào) VD: Để thực hiện backup cho database test với cung cấp sẵn password 
    
    
    mysqldump --opt -u admin --password=’password’ test > /home/dangtgh/backupfile.sql

Để thực hiện backup cho toàn bộ database 
    
    
    mysqldump --opt -u admin --password=’password’ –all-databases > /home/dangtgh/allbackup.sql

Để thực hiện backup cho một vài table trong database 
    
    
    mysqldump --opt -u admin --password=’password’ test t1 t2 t3 > /home/dangtgh/backupfile.sql

Có thể sử dụng lệnh nén kết hợp để tạo file backup có dung lượng nhỏ hơn như: 
    
    
    mysqldump --opt -u admin --password=’password’ test | gzip -9 > /home/dangtgh/backupfile.sql.gz

**Restore dữ liệu** Sử dụng cú pháp: 
    
    
    mysql -u [uname] --password='yourpassword’ [dbname] < [bakupfile.sql]

VD: Khôi phục dữ liệu cho database test 
    
    
    mysql -u admin --password='password' test < bakuptest.sql

Nếu sử dụng file backup chứa nhiều/toàn bộ các databases đã được export bằng lệnh --**database** hoặc --**all-databases**
    
    
    mysql -u admin --password='password' < allbackup.sql

Link: <http://thuthuatvietnam.com/backup-va-restore-mysql-database.html> <http://vietjack.com/mysql/index.jsp> <https://serverfault.com/questions/679399/how-to-dump-all-databases-from-mysql-server-with-mysqldump-without-passwords> <https://dev.mysql.com/doc/mysql-backup-excerpt/5.7/en/mysqldump-sql-format.html>
