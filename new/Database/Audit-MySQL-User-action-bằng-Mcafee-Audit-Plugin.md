---
title: "Audit MySQL User action bằng Mcafee Audit Plugin"
date: 2021-08-07 13:35:49
categories: [Database, MySQL, Linux, Security]
---

Lưu trữ hoạt động của User để Alert khi có bất thường và Audit sau đó là một nhu cầu bảo mật quan trọng. Bài viết này tập trung vào Audit MySQL User action bằng Mcafee Audit Plugin, làm cách nào để theo dõi khi một User truy cập hệ thống, có Failed Login (brute force ???) hay khi đổi password... [caption id="attachment_3555" align="aligncenter" width="696"]![Image không liên quan nhưng Peaky Blinder là 1 seri thú vị cho mùa dịch đấy các bạn](https://cloudcraft.info/wp-content/uploads/2021/08/audit-plugin4-e1628316763221-1024x609.jpg) Image không liên quan nhưng Peaky Blinder là 1 seri thú vị cho mùa dịch đấy các bạn[/caption] Thông thường khi nhận yêu cầu trên, các DBAs sẽ nghĩ ngay đến một dạng built-in Log của MySQL là General Log. Đúng là General Log đã có những thông tin ta cần, nhưng General Log cũng tặng kèm tất cả các Query khác mà không có cách nào chọn lọc. Thường thì ta chỉ bật General Log khi cần Debug. Vậy nên nhu cầu một Plugin chỉ Log một số thông tin cần thiết để phục vụ Audit ra đời. Một vòng giang hồ ở năm 2021, ta có một số lựa chọn sau: \+ MySQL Enterprise Audit: hàng tốn tiền của MySQL. \+ MariaDB Audit Plugin: giờ đã built-in trong bộ cài của Mariadb. Không thể tự tải nữa, phải cài Mariadb mới có. Nếu bạn đang dùng Mariadb thì chúc mừng bạn, cách dùng tham khảo tại đây nhé: <https://mariadb.com/kb/en/mariadb-audit-plugin-installation>. \+ Percona Audit Log Plugin: hệt như Mariadb, Percona cũng phải cài trong Percona Server. \+ Mcafee Audit Plugin: opensource và vẫn còn được maintain đến thời điểm bài viết. (<https://github.com/mcafee/mysql-audit>). Mcafee dẫu sao cũng là một tên tuổi bảo mật lớn để có thể tin tưởng, chứ không phải hãng bảo mật B nào đấy không thể tự bảo vệ chính mình ^^. [caption id="attachment_3558" align="aligncenter" width="640"]![](https://cloudcraft.info/wp-content/uploads/2021/08/audit-plugin3.jpg) 1 phút tưởng niệm cụ Mcafee[/caption] **Setup và Configuration** : khá là đơn giản, ai biết dùng Linux cũng dùng được. Tuy nhiên đối với các hệ thống Production thì Mcafee lưu ý bạn cần chút downtime (restart mysqld ấy mà) để bật Plugin này. **Để tải** : bạn lựa chọn version phù hợp với mình tại: <https://github.com/mcafee/mysql-audit/releases> Tải về giải nén ra được file: **lib/libaudit_plugin.so** Lúc này bạn chỉ việc copy file trên vào đường dẫn chứa MySQL plugin trên MySQL-Server của bạn (**plugin_dir**). Để có thông tin này, bạn query như sau: 
    
    
    mysql> select @@plugin_dir;

![](https://cloudcraft.info/wp-content/uploads/2021/08/audit-plugin2-e1628317907299.png) Bạn edit file**/etc/my.cnf** (hoặc file mysql.cnf tùy version của bạn), thêm vào block **[mysqld]** một số cấu hình sau: 
    
    
    [mysqld]
    # Install Plugin
    plugin-load=AUDIT=libaudit_plugin.so
    # Log các hành động login, failded login (rất có ích để kiểm tra bruteforce) 
    audit_force_record_logins=1 
    # Tên file log và đường dẫn. Nếu chỉ có tên thì sẽ nằm trong data_dir. Nếu kèm đường dẫn nhớ tạo sẵn đường dẫn. 
    **audit_json_log_file=/var/lib/mysql/data/mysql-audit.json** 
    # Định dạng log json 
    audit_json_file=1 
    # Whitelist những Users trong danh sách. Lưu ý option này apply cho tất cả user có trùng tên này, không phân biệt @host nào. 
    audit_whitelist_users=root,user1,user2 
    # Các query khi xuất ra log sẽ che đi phần password. 
    audit_password_masking_cmds=ALTER_USER,CREATE_USER,GRANT,SET_OPTION,SLAVE_START,CREATE_SERVER,ALTER_SERVER,CHANGE_MASTER,UPDATE 
    # Các query sẽ được xuất ra log. VD: create user, alter user (đổi password). 
    audit_record_cmds=CREATE_USER,ALTER_USER

Save lại rồi restart mysqld: 
    
    
    systemctl restart mysqld

Để verify, bạn check bằng các query sau: 
    
    
    mysql> show plugins;
    mysql> show global status like 'AUDIT_version';

![](https://cloudcraft.info/wp-content/uploads/2021/08/audit-plugin6.png) Nếu có thông tin thì xem như đã install xong. Bạn check file log theo cấu hình tại **audit_json_log_file xem đã có log chưa.** Nếu có kết quả như sau thì chúc mừng bạn đã cấu hình thành công. ![audit-plugin1](https://cloudcraft.info/wp-content/uploads/2021/08/audit-plugin1.png) Tới đây, với output là file log dạng JSON, bạn hoàn toàn có thể tích hợp với các hệ thống quản lý Log tập trung như ELK stack để tạo thêm nhiều quá trị.
