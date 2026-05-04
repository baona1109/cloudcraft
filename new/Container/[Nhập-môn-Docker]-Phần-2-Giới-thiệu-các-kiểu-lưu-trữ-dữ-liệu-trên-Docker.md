---
title: "[Nhập môn Docker] Phần 2: Giới thiệu các kiểu lưu trữ dữ liệu trên Docker"
date: 2018-02-07 14:50:13
categories: [Container, Docker, Linux]
---

Hiện nay có nhiều cách để lưu trữ dữ liệu trên container. Một trong những cách đơn giản nhất là lưu trữ dữ liệu trực tiếp lên container (container layer - mình sẽ nói kỹ hơn về phần này trong những bài sau). Tuy nhiên, cách lưu trữ trực tiếp này có rất nhiều khuyết điểm như: 
  * Dữ liệu sẽ mất khi container đó không còn chạy nữa, khó truy xuất dữ liệu nếu một container khác hoặc process khác cần dữ liệu này.
  * Container layer được gắn chặt với máy host khi container đang chạy. Khó di chuyển dữ liệu được lưu trên container layer.
  * Ghi dữ liệu trên container layer cần phải đi qua 1 lớp storage driver => overhead => giảm hiệu năng ghi dữ liệu so với data volume (ghi trực tiếp lên host filesystem).

Ta có 3 cách để lưu trữ dữ liệu của Docker container là: volume, bind mount và tmpfs volume. Mặc định thì volume thường là lựa chọn tốt nhất. _![](https://cloudcraft.info/wp-content/uploads/2018/01/gioi-thieu-cac-kieu-luu-tru-tren-docker-1.png)_

_Hình minh hoạt 3 cách mount dữ liệu trên container._

Chi tiết 3 kiểu lưu trữ dữ liệu này: 

  * Dữ liệu trên **volume** được lưu trên filesystem của Docker host (_**/var/lib/docker/volumes/**_) và được quản lý bởi Docker deamon. Các process không liên quan đến Docker sẽ không đụng đến phần dữ liệu này. Volumes là cách lưu trữ dữ liệu tốt nhất trên Docker.
  * Bind mount có thể được lưu ở bất cứ đâu trên máy host. Các process không liên quan đến Docker hoặc một container khác có thể chỉnh sửa các file này bất kỳ lúc nào.
  * Tmpfs mound được lưu trên bộ nhớ (RAM) của máy host và không ghi lên trên host filesystem (không ghi lên disk).



## Docker Storage

Thông tin chi tiết về các loại mount: **Volume** Được khởi tạo và quản lý bởi Docker. Ta có thể khởi tạo một volume bằng cách sử dụng lệnh **_docker volume create_** hoặc tạo volume khi khởi tạo container/service. Khi ta khởi tạo một volume, volume này sẽ nằm trong một thư mục trên Docker host. Khi ta mount volume này lên container tức là ta mount thư mục đó lên container. Cách thức hoạt động của volume tương tự như bind mount, ngoại trừ việc volume được quản lý bởi Docker và được phân tách (isolate) khỏi máy host. ![](https://cloudcraft.info/wp-content/uploads/2018/01/gioi-thieu-cac-kieu-luu-tru-tren-docker-2.png)

_Dữ liệu trong volume được lưu trữ bên ngoài các container, lưu trên Docker host_

Một volume có thể được mount lên nhiều container cùng một lúc. Khi không có container nào sử dụng volume đó, volume này vẫn tồn tại và được quản lý bởi Docker mà không bị xóa đi như container layer. Ta có thể xóa các volume không sử dụng bằng cách dùng lệnh **_docker volume prune_**. Ta có thể đặt tên cho một volume, nếu ta không đặt tên thì Docker sẽ tự động đặt tên cho volume đó và tên này là duy nhất trên một Docker host. Volume trên Docker hỗ trợ dùng nhiều loại volume drivers, cho phép lưu trữ dữ liệu trên remote host hoặc trên hạ tầng lưu trữ của các nhà cung cấp dịch vụ cloud. **Bind mounts** Có từ thời xửa thời xưa khi Docker mới ra đời. So với volume thì bind mount có ít chức năng hơn. Khi ta dùng bind mount, ta có thể mount 1 file hoặc một thư mục lên container. File hoặc thư mục này được truy cập theo đường dẫn tuyệt đối trên máy host. Bind mount có hiệu năng truy xuất rất cao, nhưng phụ thuộc vào file system của máy host. Chú ý: khi dùng bind mount, các process trong container có thể thay đổi filesystem của máy host (tạo file, thêm xóa sửa các dữ liệu hoặc thư mục quan trọng của hệ thống). Tính năng này tuy mạnh nhưng có thể tạo ra nhiều nguy cơ về bảo mật, gây ảnh hưởng tới các process khác trên máy host. **tmpfs mounts** tmpfs mount không được lưu trên đĩa cứng. tmpfs mount thường được dùng để lưu dữ liệu khi container đang chạy (dữ liệu này không cần được lưu trữ lâu dài). **Khi nào nên dùng volume** Volume là cách thường được dùng khi cần lưu trữ dữ liệu lâu dài trong container và services. Một số ứng dụng của volumes gồm: 

  * Chia sẻ dữ liệu giữa nhiều container đang chạy cùng lúc. Nhiều container có thể đồng thời mount cùng 1 volume. (read-write hoặc read only). Volume không tự động xóa đi, ta cần phải gõ lệnh nếu muốn xóa volume.
  * Khi ta muốn lưu trữ dữ liệu của container trên remote host hoặc trên cloud provider (AWS, GCE, Azure…).
  * Hoạt động được trên cả Linux và Windows container.
  * Dữ liệu được lưu trên volume sẽ không làm tăng kích thước của container.
  * Khi ta cần backup, phục hồi hoặc di chuyển dữ liệu từ mọt Docker host này sang một host khác, volume là sự lựa chọn lý tưởng trong những trường hợp này. Ta có thể tạm dừng một container, backup volume của container này (thường nằm trong **_/var/lib/docker/volumes/ <volume-name>_**)

**Khi nào nên dùng bind mount** Bind mount thích hợp trong những trường hợp sau: 

  * Chia sẻ các file cấu hình từ máy host sang container. Ví dụ: bind mount file **_/etc/resolv.conf_** từ máy host lên mỗi container.
  * Chia sẻ source code từ Docker host sang container.
  * Khi file container cần file và thư mục phải đồng bộ với Docker host.

**Khi nào nên dùng tmpfs mount** tmpfs mount được dùng khi ta không muốn lưu dữ liệu lâu dài trên cả máy host hoặc trong container vì lý do an ninh. Hoặc do ta muốn đảm bảo hiệu năng của container khi cần xử lý một lượng lớn dữ liệu tạm thời. **Một số chú ý khi dùng bind mount hoặc volume** Khi dùng bind mount hoặc volume thì cần chú ý những điều sau: 

  * Nếu ta mount một volume trống từ lên container, và trong thư mục tương ứng trên container đã có sẵn dữ liệu thì các file trên đó sẽ được copy vào volume. Tương tự vậy, nếu ta khởi động một container và yêu cầu một volume (volume này chưa tồn tại), Docker engine sẽ tạo ra một volume trống cho ta.
  * Nếu ta mount một bind mount hoặc một volume đẫ có dữ liệu vào một thư mục trên container và thư mục này cũng đã có dữ liệu, dữ liệu trên container sẽ bị **_“tạm thời thay thế”_** bởi dữ liệu mới mount. Khi ta unmount thì dữ liệu này sẽ hiện ra như cũ.
  * Khi ta dùng cờ _**-v**_ hoặc _**\--volume**_ để _**bind-mount**_ một file hay thư mục chưa tồn tại trên Docker host thì Docker sẽ tạo ra một thư mục mới.
  * Nếu ta dùng cờ _**\--mount**_ để _**bind-mount**_ một file hay thư mục chưa tồn tại trên Docker host thì Docker không tự động tạo thư mục mới mà sẽ thông báo lỗi.

Bài viết này chỉ tập trung vào việc giới thiệu 3 loại lưu trữ dữ liệu trên Docker. Chi tiết các câu lệnh cho từng phần thì các bạn có thể xem tại đây, trên [GitHub](https://github.com/nduytg/System-Engineer-Cheat-Sheets/blob/master/Docker/%5BStorage%5D%20Docker-Guide-Volume.txt) của mình. 

## Tham khảo

https://docs.docker.com/storage/volumes/ http://training.play-with-docker.com/docker-volumes/
