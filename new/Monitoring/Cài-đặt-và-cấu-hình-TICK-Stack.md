---
title: "Cài đặt và cấu hình TICK Stack"
date: 2018-07-12 16:15:41
categories: [Monitoring, Linux, Database]
---

# **TICK stack là gì**

TICK stack là một mô hình technical stack về time series data (dữ liệu liên tục theo thời gian thực), nó được thiết kế để thu thập, phân tích các thông số và sự kiện trên hệ thống rồi vẽ đồ thị đưa lên giao diện website. Bộ stack này gồm 4 dự án open source là Telegraf, InfluxDB, Chronograf và Kapacitor. Nó được phát triển bởi InfluxData và được cấp phép bởi MIT InfluxData chia bộ sản phẩm stack này làm 3 loại là open source, InfluxEnterprise và InfluxCloud. Với opensource thì hiện nay InfluxData đã loại một số tính năng như là cluster, Access Control. Nếu muốn dùng các tính năng này thì phải sử dụng 2 bản trả phí là InfluxEnterprise và InfluxCloud ![](https://cloudcraft.info/wp-content/uploads/2018/07/cai-dat-va-cau-hinh-tick-stack-4.png)

# **Những lợi ích TICK stack mang lại**

TICK stack cung cấp một số lợi ích khá toàn diện cho người quản trị hệ thống khi sử dụng nó. TICK stack phân tích dữ liệu theo thời gian thực, đáp ứng được khả năng ghi và đọc với luồng dữ liệu lớn. TICK stack sử dụng một cơ sở dữ liệu chuyên biệt dành cho time series data đó là influxdb để có thể đáp ứng nhu cầu lưu trữ lượng lớn dữ liệu mỗi giây, việc ghi và truy xuất dữ liệu tốc độ và hiệu suất cao. Theo như bên trên có nhắc tới time series data, mọi người sẽ thắc mắc rằng nó là gì?. Dựa theo mình đọc hiểu thì time series data trong thống kê, xử lý tín hiệu, kinh tế lượng và toán tài chính là một chuỗi các điểm dữ liệu, được đo từng thời điểm liên tiếp nhau theo một tần suất thời gian nhất định (như nhiệt độ CPU tại mỗi thời điểm), vì vậy tại một thời điểm có một lượng lớn thông số và dữ liệu được ghi xuống database cũng như dùng để truy vấn và report lại cho người quản trị theo dõi. Để đáp ứng được nhu cầu này thì cần một cơ sở dữ liệu có thể đáp ứng yêu cầu trên, thì influxdb được xây dựng nhằm cho mục đích này theo mô hình time series DBMS viết tắt từ time series database managerment system là một mô hình được tối ưu hóa để nắm bắt dữ liệu dạng time series chẳng hạn như là thông số cảm ứng của thiết bị IoTs, hoặc thông số dành cho monitor hệ thống,... Nói nãy giờ có vẻ dài dòng, mọi người có thể lên trang chủ của [influxdata](https://www.influxdata.com/) để xem thêm. Giờ ta sẽ bước vào cài đặt và cấu hình 

# **Cài đặt và cấu hình**

Môi trường test cài đặt

  * OS: CentOS 7
  * IP: 192.168.100.22

Trước tiên ta cần tải 4 gói rpm của 4 dịch vụ
    
    
    mkdir -p ~/tick
    cd ~/tick
    wget https://dl.influxdata.com/telegraf/releases/telegraf-1.7.0-1.x86_64.rpm
    wget https://dl.influxdata.com/influxdb/releases/influxdb-1.5.4.x86_64.rpm
    wget https://dl.influxdata.com/chronograf/releases/chronograf-1.5.0.1.x86_64.rpm
    wget https://dl.influxdata.com/kapacitor/releases/kapacitor-1.5.0.x86_64.rpm

Tiến hành cài đặt tất cả các gói vừa tải
    
    
    sudo yum localinstall * -y

## **Cấu hình influxdb**

Giờ chúng ta bắt đầu tiến hành cấu hình từng dịch vụ, trước tiên thằng chủ yếu nhất trong bộ TICK stack là Influxdb, thằng này sẽ lưu trữ thông tin, các thông số đã phân tích từ telegraf Trước khi sử dụng ta cần tặng độ bảo mật cho influxdb, chúng ta cần bật chứng thực khi kết nối tới thông qua HTTP, bởi vì influxdb nhận dữ liệu từ từ telegraf thông qua http với port 8086 (truy vấn qua API) hoặc udp port 8089
    
    
    sudo vi /etc/influxdb/influxdb.conf

Tìm đoạn dưới đây và chỉnh sửa dòng auth-enabled thành true để bật tính năng chứng thực
    
    
    ...
    [http]
    # Determines whether HTTP endpoint is enabled.
    # enabled = true
    # The bind address used by the HTTP service.
    # bind-address = "192.168.100.22:8086"
    # Determines whether HTTP authentication is enabled.
    auth-enabled = true
    ...

Ở trong file này ta có thể chỉnh cấu hình cho influxdb, chẳng hạn như bind-address dùng để cấu hình influxdb sẽ nghe trên ip và port nào (mặc định là localhost:8086) Tùy vào mục đích sử dụng và mong muốn cấu hình thì ta có thể thay đổi các cấu hình cho influxdb trong file này. Sau khi đã bật chứng thực thông qua http, việc cần làm kế tiếp của chúng ta là tạo user để chứng thực. Ở giao diện terminal gõ lệnh influx để truy cập giao diện terminal của influxdb trên localhost. Nếu ta cho influxdb nghe trên ip cố định khác mà không phải trên localhost thì ta cần phải thêm cờ -host để xác định và với –port để chỉ định port truy cập (nếu có thay đổi)
    
    
    influx –host=192.168.100.22

Sau khi truy cập được influxdb chúng ta tiến hành tạo user
    
    
    CREATE USER "dangtgh" WITH PASSWORD 'password' WITH ALL PRIVILEGES;

Ta kiểm tra user đã tạo bằng lệnh
    
    
    SHOW users;

Khởi chạy dịch vụ dịch vụ
    
    
    sudo systemctl start influxd

## **Cấu hình telegraf**

Telegraf là một công cụ dùng để sưu tập, phân tích log của hệ thống, dịch vụ để lấy ra các số liệu và đẩy về nơi lưu trữ. Telegraf có hỗ trợ kết nối tới nhiều loại hình database trong đó có influxdb. Trước khi sử dụng chúng ta cần chỉnh sửa cấu hình cho Telegraf kết nối và đẩy dữ liệu tới influxdb
    
    
    sudo vi /etc/telegraf/telegraf.conf

Telegraf cung cấp nhiều plugin đầu vào (input để lấy dữ liệu, có khoảng hơn 60 plugins đối ứng với các dịch vụ phổ biến như nginx, apache, mysql,….) lẫn đầu ra (output để đẩy dữ liệu về nơi lưu trữ) Tìm dòng **[[outputs.influxdb]]** để chỉnh sửa
    
    
    ...
    [[outputs.influxdb]]
    ## The full HTTP or UDP URL for your InfluxDB instance.
    ##
    ## Multiple URLs can be specified for a single cluster, only ONE of the
    ## urls will be written to each interval.
    # urls = ["unix:///var/run/influxdb.sock"]
    # urls = ["udp://127.0.0.1:8089"]
    urls = ["http://192.168.100.22:8086"]
    ## The target database for metrics; will be created as needed.
    database = "telegraf"
    ## If true, no CREATE DATABASE queries will be sent. Set to true when using
    ## Telegraf with a user without permissions to create databases or when the
    ## database already exists.
    # skip_database_creation = false
    ## Name of existing retention policy to write to. Empty string writes to
    ## the default retention policy. Only takes effect when using HTTP.
    # retention_policy = ""
    ## Write consistency (clusters only), can be: "any", "one", "quorum", "all".
    ## Only takes effect when using HTTP.
    # write_consistency = "any"
    ## Timeout for HTTP messages.
    # timeout = "5s"
    ## HTTP Basic Auth
    username = "dangtgh"
    password = "password"
    ...

Trong đó:

  * **urls** ta chọn phương thức đẩy dữ liệu, có thể qua socket (chỉ dùng khi telegraf và influxdb trên cùng 1 host), qua giao thức upd, hoặc sử dụng http (khuyến khích dùng vì influxdb hỗ trợ tốt hơn với api http)
  * **database** : Ta sẽ định nghĩa tên database dùng để lưu trữ dữ liệu trên influxdb, khi telegraf gửi dữ liệu tới nếu chưa có database nó sẽ tự tạo ra database với tên này.
  * **username** và **password** : Nhập user và pass đã tạo ở influxdb để telegraf dùng nó và chứng thực

Ngoài ra còn các tham số khác tùy mục đích sử dụng có thể tham khảo thêm Sau cùng chúng tiến hành khởi chạy dịch vụ dịch vụ
    
    
    sudo systemctl start telegraf

Kiểm tra
    
    
    sudo systemctl status telegraf

Để kiểm tra coi việc kết nối giữa telegraf và infuxdb, chúng ta vào influx bằng username và password đã tạo
    
    
    influx -host 192.168.100.22 -username 'dangtgh' -password 'password'

Lưu ý ở đây do chúng ta đã tạo user và mật khẩu ở bước trên, nên khi truy cập ta phải chỉ rõ user và password nào dùng dể truy cập. Sau khi truy cập influx chúng ta liệt kê danh sách database để xem influxdb có tạo database cho telegraf theo như ta cấu hình hay không
    
    
    SHOW DATABASES;

Nếu telegraf kết nối thành công sẽ tạo một database ta đã setup trong telegraf và có output như sau:
    
    
    > show databases
    name: databases
    name
    ----
    _internal
    telegraf

Ta kiểm tra các thuộc tính do lường mà telegraf đã thu thập, phân tích và lưu vào database telegraf
    
    
    USE telegraf;
    show measurements

Ta sẽ được output như sau
    
    
    name: measurements
    name
    ----
    cpu
    disk
    diskio
    kernel
    mem
    processes
    swap
    system

## **Cấu hình kapacitor**

Là công cụ tạo ra các cảnh báo hệ thống dựa trên một ngưỡng đã được đặt ra trước đó. Ở bài này mình chỉ nói về việc cấu hình ban đầu cho kapacitor để có thể hoạt động. Còn việc cấu hình các ngưỡng, gửi alert qua mail,… mình sẽ nói riêng ở các bài sau. Giờ ta bắt đầu chỉnh sửa file cấu hình kapicitor
    
    
    sudo vi /etc/kapacitor/kapacitor.conf

Ta tìm đoạn nội dung dưới đây, đặc biệt là mục [[influxdb]].
    
    
    ...
    # Multiple InfluxDB configurations can be defined.
    # Exactly one must be marked as the default.
    # Each one will be given a name and can be referenced in batch queries and InfluxDBOut nodes.
    [[influxdb]]
    # Connect to an InfluxDB cluster
    # Kapacitor can subscribe, query and write to this cluster.
    # Using InfluxDB is not required and can be disabled.
    enabled = true
    default = true
    name = "localhost"
    urls = ["http://192.168.100.22:8086"]
    username = "dangtgh"
    password = "password"
    ...

Tại đây ta điền urls là địa chỉ mà influxdb đang chạy username và password: là user và pass đã tạo của influxdb dùng để truy cập chứng thực qua http Ngoài ra có thể chỉnh lại địa chỉ và port mà Kapacitor sử dụng để lắng nghe
    
    
    [http]
    # HTTP API Server for Kapacitor
    # This server is always on,
    # it serves both as a write endpoint
    # and as the API endpoint for all other
    # Kapacitor calls.
    bind-address = "192.168.100.22:9092"

Ta cấu hình ở bind-address để điều chỉnh địa chỉ với port mà Kapacitor sẽ khởi chạy. Port mặc định của thằng này là 9092 Tiến hành reload daemon và start kapicitor lên
    
    
    sudo systemctl daemon-reload
    sudo systemctl start kapacitor

## **Chronograf**

Khởi chạy chronograf
    
    
    sudo systemctl start chronograf

Mặc định chronograf sẽ chạy trên port 8888, truy cập link sau để vào giao diện web chronograf
    
    
    http://192.168.100.22:8888

Ta sẽ được giao diện như hình dưới ![](https://cloudcraft.info/wp-content/uploads/2018/07/cai-dat-va-cau-hinh-tick-stack-1.png) Trong đó:

  * **Connection String** các bạn nhập địa chỉ và port mà influxdb đang chạy để kết nối tới
  * **Name** đặt tên cho connection này để tiện quản lý trên Chronograf
  * **Username** và **Password** là user và pass có quyền kết nối tới influxdb

Sau khi kết nối ta sẽ vào giao diện DashBoard như thế này hiển thị tình trạng sơ bộ của các node trong hệ thống ![](https://cloudcraft.info/wp-content/uploads/2018/07/cai-dat-va-cau-hinh-tick-stack-2.png) Click vào Host để coi chi tiết monitor của của từng node ![](https://cloudcraft.info/wp-content/uploads/2018/07/cai-dat-va-cau-hinh-tick-stack-3.png) Việc cài đặt và cấu hình cho TICK stack ban đầu tới đây là hết, ở loạt bài sau mình sẽ hướng dẫn cấu hình alert của kapacitor, sử dụng thêm plugin để phân tích log của telegraf,... 

### **Tham khảo**

<https://docs.influxdata.com/> <https://db-engines.com/en/system/InfluxDB#a32> <https://blog.codeship.com/infrastructure-monitoring-with-tick-stack/> <https://www.spectory.com/blog/System%20monitoring%20with%20InfluxDB%20vs%20Elasticsearch>
