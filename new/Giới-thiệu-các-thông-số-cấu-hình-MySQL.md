---
title: "Giới thiệu các thông số cấu hình MySQL"
date: 2018-03-09 17:28:56
categories: [Database]
---

MySQL cho phép người dùng có thể tùy chỉnh config cho phù hợp với ứng dụng/server đang sử dụng. Người dùng có thể thay đổi, tùy chỉnh cấu hình của MySQL trong file **/etc/my.cnf** Dưới đây chỉ là các tham cơ bản cho việc cấu hình mysql. Nếu muốn biết chi tiết hơn thì các bạn có thể lên trên trang chủ mysql xem document để chi tiết hơn. **Một số thông số tinh chỉnh cho việc cấu hình**

## **Các thông số cơ bản**

**Biến** | **Ý nghĩa**  
---|---  
**port** | Định nghĩa port mà mysql sẽ sử dụng để lắng nghe kết nối  
**bind-address** | Để xác định mysql lắng nghe trên IP nào  
**socket** | Nơi chứa file socket dùng cho việc nghe kết nối  
**user** | Định nghĩa user mà MySQL sẽ dùng để thao tác  
**default_storage_engine** | Xác định storage engine sử dụng cho việc quản lý databases.  
**pid_file** | Nơi chứa file lưu trữ process id của mysql khi chạy được server cấp  
  
## **Cấu hình việc ghi log**

**Biến** | **Ý nghĩa**  
---|---  
**log_output = [FILE|TABLES|NONE]** | Chỉ định loại lưu trữ log mà ta sẽ lưu thông tin xuống, có thể là files hoặc là tables. Để sử dụng các biến xác định file log phía dưới thì cần chuyển biến này sang **FILE**  
**log_error** | Đường dẫn tới nơi chứa log lỗi khi khởi động, dừng hoặc trong quá trình hoạt động của mysql  
**long_query_time = [value]** | Khi một truy vấn thực thi bằng hoặc vượt qua giá trị **long_query_time** thì mysql sẽ xác định là truy vấn chậm và tiến hành ghi log vào **slow_query_log_file** những thông tin của truy vấn chậm này. Đơn vị tính bằng giây.  
**slow_query_log** | Bật/tắt việc sử dụng log truy vấn để ghi lại các truy vấn chậm tới database  
**slow_query_log_file** | Đường dẫn tới nơi chứa log các truy vấn thực hiện quá lâu cần nhiều thời gian để thực thi thành công trên database vượt quá thời gian **long_query_time**  
**general_log** | Bật/tắt việc sử dụng log chung để ghi lại mọi hoạt động thao tác trên mysql  
**general_log_file** | Đường dẫn tới nơi chứa log chung của toàn bộ mysql  
  
## **Các thông số có thể tăng hiệu suất hoạt động của mysql**

**Biến** | **Ý nghĩa**  
---|---  
**max_connections** | Tham số này tùy chỉnh số lượng kết nối tối đa tại cùng một thời điểm. Khi số lượng kết nối vào mysql đạt ngưỡng này thì các kết nối sau sẽ nhận phản hồi **“Too many connections”**  
**max_allowed_packet=[values]** | Tham số này là kích thước tối đa của gói tin truy vấn mà server có thể bắt được. Tham số này mặc định là khoảng 4MB, tuy nhiên nếu những gói tin chứa câu truy vấn quá lớn thì ta cần phải tăng tham số này lên để server có thể handle được. **Values** được set trong khoảng Min và Max. [Min: 1KB, Default: 4MB, Maximum: 1GB] Không như **innodb_buffer_pool_size** phần bộ nhớ được cấp cho buffer_pool không dùng cho mục đích khác, còn **max_allowed_packet** giá trị được cấp chỉ dùng khi server cần, bình thường thì khoảng trống này có thể được server sử dụng cho việc khác.  
**thread_cache_size** | Kích thước hàng đợi chứa thread cache, khi người dùng ngưng kết nối thì thread hiện tại sẽ được đưa vào để cache lại cho tới khi người dùng sử dụng tiếp thì sẽ được lấy ra. Nếu được set 0 hoặc hàng đợi chạm mức giá trị này thì các kết nối mới tới mysql sẽ được thực hiện tạo một thread mới để xử lý.  
**table_open_cache** | Số lượng tối đa bảng có thể mở cho tất cả các thread. Việc tăng giá trị này sẽ gia tăng số lượng file descriptors mà mysql yêu cầu. (mọi đối tượng trên linux đều quy ra file vì vậy mỗi bảng được mở tương ứng với một file). Giá trị này phải lớn hơn giá trị **open_tables** là giá trị chứa số bảng đang mở hiện tại  
**open_files_limit** | Điều chỉnh số lượng file tối đa mà mysqld có thể mở. Giới trị tối đa của biến này phụ thuộc vào nền tảng của hệ thống.  
**innodb_buffer_pool_size** | Đây là tham số quan trọng khi sử dụng innodb. InnoDB luôn duy trì một vùng lưu trữ được gọi là buffer pool để cache lại dữ liệu và chỉ mục bên trong memory. Với tham số này thì bộ nhớ đã cấp cho buffer pool server sẽ không sử dụng cho mục đích khác. Giá trị này cao thì khả năng chứa bộ nhớ đệm cho dữ liệu trong quá trình truy xuất dữ liệu database càng nhiều (truy xuất trên RAM thay vì trên ổ cứng với những giá trị được truy cập thường xuyên). Tùy vào lượng RAM hiện tại của server và các ứng dụng khác dùng trên server mà ta có thể tinh chỉnh thông số này cho phù hợp cho MySQL. Trên một server vật lý thì giá trị này nên bằng khoảng 70% lượng RAM của server vật lý  
**innodb_log_file_size** | Kích thước file log cho innodb, việc tùy chỉnh kích thước lớn phù hợp sẽ tăng hiệu suất xử lý. Nó còn phụ thuộc vào số lượng công việc hoạt động và phiên bản của máy chủ. Các phiên bản cũ thường khôi phục crash rất chậm với file log lớn. Thường sử dụng khoảng 128M hoặc 256MB là đủ.  
**innodb_flush_method** | Định nghĩa phương thức để làm sạch dữ liệu cho các file dữ liệu và log của InnoDB. Có nhiều phương thức nhưng người ta thường dùng **O_DIRECT** cho GNU/Linux versions, FreeBSD, Solaris.  
**innodb_file_per_table** | Nếu tắt (=OFF) thì InnoDB sẽ tạo bảng trong khoảng không gian chia sẽ dùng chung của các bảng. Nếu bật cờ này (=ON) thì khi tạo mỗi bảng sẽ sở hữu một file có đuôi là .idb để lưu trữ dữ liệu và chỉ mục. Từ MySQL 5.6, giá trị mặc định này đã được bật.  
  **Tham khảo:** <https://dev.mysql.com/doc/refman/5.7/en/preface.html> <http://sinhvienit.net/forum/innodb-myisam-va-memory-nen-su-dung-storage-engine-nao-khi-dung-mysql.209109.html> <http://www.codingpedia.org/ama/optimizing-mysql-server-settings/> <https://linode.com/docs/databases/mysql/how-to-optimize-mysql-performance-using-mysqltuner/>
