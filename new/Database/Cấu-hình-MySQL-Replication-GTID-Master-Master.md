---
title: "Cấu hình MySQL Replication GTID Master-Master"
date: 2020-07-14 16:52:50
categories: [Database]
---

Tiếp nối loạt bài **MySQL Toàn tập** , lần này Cloudcraft giới thiệu bài hướng dẫn cấu hình MySQL Replication Master-Master sử dụng GTID. Mô hình Master-Master này cho phép bạn Write/Read đồng thời trên cả hai node chứ không chỉ một node Write/Read và một node Read-only như Master-Slave. Đây là một biến thể tuy không chính thống (rủ ro rất lớn cho tính nhất quán của dữ liệu) nhưng lại rất phổ biến của MySQL Replication.

![mysql-replica-master-master01](https://cloudcraft.info/wp-content/uploads/2020/07/mysql-replication-master-master.png)  

### **Tl;DR:**

  * Cách làm tương tự như Replication Master-Slave nhưng mỗi Node đóng cả 2 vai trò cùng lúc.
  * Có thể sử dụng **Binlog** hoặc **GTID**.
  * Vẫn chỉ là MySQL asynchronous, không phải là MySQL synchronous như NDB Cluster hay Galera Cluster.
  * **Lợi điểm** : tăng một phần nào đó khả năng HA cho hệ thống và giảm thiểu số tác vụ khi failover.
  * **Hạn chế** : **luôn luôn** phải đảm bảo (ở client) cùng một thời điểm chỉ write vào một node. Nếu không thể đảm bảo việc này, cân nhắc sử dụng giải pháp khác.
  * Thường phải đi kèm một giải pháp Database Proxy nào đó: Keepalived + LVS/Haproxy, ProxySQL, MaxScale,...



### **Lảm nhảm**

Ý tưởng của mô hình này cũng khá đơn giản, thay vì mỗi node MySQL chỉ đóng 1 vai trò Master hoặc Slave, thì ta có thể để mỗi node đóng cả 2 vai trò cùng lúc. Một node vừa là Master và vừa là Slave của node khác. Bạn có thể để một node làm Active, node còn lại Backup. Khi xảy ra incident, chỉ việc trỏ lại Application về node Backup, không cần phải tương tác với MySQL để promote từ Slave Read-only thành Master như cách làm Master-Slave bình thường. Khi kết hợp với Keepalived + LVS/Haproxy, việc failover hoàn toàn có thể tự động (tự động chứ chưa hẳn là transparent :D ).

Tuy nhiên mô hình này vẫn chỉ là **MySQL asynchronous** và không có cơ chế đảm bảo dữ liệu được đồng bộ đã được Write thành công cũng như có phát sinh confict trên tất cả các node còn lại hay không. Do vậy, mô hình này chỉ nên áp dụng ở qui mô hệ thống nhỏ đến vừa. Nếu có thể đáp ứng yêu cầu cấu hình tối thiểu của các giải pháp **MySQL Synchronous** như **Galera Cluster** , **NDB Cluster** ,... bạn nên sử dụng các giải pháp này để Scale Out phần Write cũng như HA cho hệ thống.

Do [bài viết trước](https://cloudcraft.info/mysql-replication-master-slave/) Cloudcraft đã giới thiệu **Binlog** , bài viết này sẽ theo cách **GTID**. Với Binlog, bạn cần quan tâm File Log & Position cho phù hợp. GTID ra đời nhằm giảm thiểu độ phức tạp ở đây. GTID viết tắt của Global Transaction ID. Với mỗi sự thay đổi trong 1 node MySQL sẽ phát sinh một ID. ID được đánh mã theo format <node-id>:<transaction-id>. Việc quyết định một change có cần được apply hay không, cần phải apply change nào tiếp theo sẽ dựa trên GTID. Bạn có thể tham khảo thêm ý tưởng về GTID tại [link](https://dev.mysql.com/doc/refman/8.0/en/replication-gtids-concepts.html). Hình minh họa trong bài viết này là mô hình 2 node, bạn hòan toàn có thể mở rộng lên thành 3,4,5,... node. Tuy nhiên như đã đề cập, nếu đáp ứng được resource dư dả, bạn nên chuyển thành dạng MySQL Synchronous. 

### **Bước 1: bật GTID trên cả hai node.**

Thêm vào /etc/my.cnf: 
    
    
    gtid_mode=on
    binlog_format = MIXED
    enforce_gtid_consistency = ON
    gtid_mode = ON
    log_slave_updates = ON
    relay_log_info_repository = TABLE
    relay_log_recovery = 1
    relay_log_purge = 1

Restart mysql & check lại variable **gtid_mode** : 
    
    
    systemctl restart mysql
    
    
    mysql -e 'show global variables like "gtid_mode"'
    +---------------+-------+
    | Variable_name | Value |
    +---------------+-------+
    | gtid_mode     | ON    |
    +---------------+-------+

 

### **Bước 2: Backup dữ liệu và Restore trên các node.**

Để cấu hình replication từ node này (source) sang node kia (dest), lần lượt backup data từ node này (source) và restore sang node kia (dest). Để backup tất cả data (source), bạn có thể dùng **mysqldump** : `mysqldump --all-databases > backup.sql` các option nâng cao hơn chẳng hạn: chỉ backup một số database nhất định (--database), backup nhưng không kèm các trigger, stored procedure,... bạn tham khảo tại [link](https://dev.mysql.com/doc/refman/8.0/en/mysqldump.html). Ngoài ra, bạn có thể các công cụ khác như **Xtrabackup**. Sau đó, copy file dump sang và restore ra trên node còn lại (dest). 
    
    
    scp source-ip:/backup.sql ./
    mysql -e "reset master"
    mysql < backup.sql

 

### **Bước 3: Cấu hình GTID Replication:**

Lần lượt trên các node, cấu hình như sau: `mysql> change master to master_host="IP/Hostname", ` `> master_port=3306, ` `> master_user="Replication_User", ` `> master_password="password", ` `> master_auto_position=1; ` `mysql> start slave; ` `mysql> set global read_only=0;`

  * IP/Hostname: IP/Hostname của node làm source. Lưu ý nếu dùng hostname thì không nên cấu hình **skip-name-resolve** và **skip-host-cache** trong **/etc/my.cnf**
  * Replication User: user chỉ có quyền **Replication Slave** trên node source. Đây là một best practice về security, bạn có thể bỏ qua nếu muốn.
  * Master_auto_postion=1: cấu hình replication bằng GTID. Một lợi điểm của GTID là bạn không cần phải ghi nhận File Binlog và Log Position trên source.

Kiểm tra lại bằng lệnh: 
    
    
    mysql -e "show slave status\G" | grep Running

Nếu cả **Slave_IO_Running** và **Slave_SQL_Running** đều là**YES** , thì chúc mừng bạn đã cấu hình thành công, nếu không, kiểm tra MySQL Log để biết thêm chi tiết lỗi.

Lặp lại Bước 3 trên tất cả các node.

Sau khi cấu hình xong, bạn có thể thử **Create Databas** e, **Create User** , **Create Table** , **Insert data** ,... để kiểm tra quá trình đồng bộ.

Như vậy, ta đã có gạch đầu dòng đầu tiên "**Bidirectional MySQL Replication** " trong hình minh họa ở đầu bài viết. Ở bài viết sau, Cloudcraft sẽ mở rộng HA bằng cách kết hợp **Keepalived**.
