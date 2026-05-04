---
title: "HƯỚNG DẪN CÀI ĐẶT PORTAINER ĐỂ QUẢN LÝ DOCKER HOST"
date: 2021-11-03 14:56:58
categories: [Container, Docker]
---

  1. **Giới thiệu**

Portainer là công cụ quản lý Docker Containter miễn phí với kích thước gọn nhẹ và giao diện quản lý trực quan, đơn giản để triển khai cũng như sử dụng, cho phép người dùng dễ dàng quản lý Docker host hoặc Swarm cluster. Công cụ này hoạt động trên một container được triển khai trên Docker Engine (tương thích với phiên bản 1.9 trở lên, hỗ trợ trên cả Linux và Windows). Bạn đọc có thể tham khảo thêm thông tin của Portainer tại trang chủ của Portainer hoặc repository của Portainer trên Github. ![](https://cloudcraft.info/wp-content/uploads/2021/11/portainer_6-300x96.png)

  2. **Hướng dẫn cài đặt Portainer trên Linux**

Bước 1: Cài đặt Docker. 
  * Các bạn có thể tham khảo bài viết sau nếu server chưa được cài đặt dịch vụ Docker nhé.

https://cloudcraft.info/huong-dan-cai-dat-phien-ban-docker-bat-ki-tren-linux/ Bước 2: Cài đặt Portainer. 
  * Tạo Docker Volume để lưu trữ thông tin.


    
    
    # docker volume create portainer_data

  * Chạy Container từ image Portainer.


    
    
    docker run -d -p 8000:8000 -p 9443:9443 --name portainer \
    --restart=always \
    -v /var/run/docker.sock:/var/run/docker.sock \
    -v portainer_data:/data \
    portainer/portainer-ce:latest

  * Sử dụng lệnh docker ps để kiểm tra các container đang chạy. Như vậy là dịch vụ Portainer đã được cài đặt thành công.


    
    
    # docker ps

![](https://cloudcraft.info/wp-content/uploads/2021/11/portainer_1-300x45.png)

  * Các bạn truy cập trang Portainer bằng đường dẫn sau.

[https://<IP Address>:9443](https://localhost:9443)

  * Ở lần truy cập đầu tiên, Portainer sẽ yêu cầu người dùng thiết lập mật khẩu của tài khoản admin.

![](https://cloudcraft.info/wp-content/uploads/2021/11/portainer_2-300x257.png)

  3. **Hướng dẫn thêm một docker host vào trang Portainer.**

Bước 1: Cài đặt Portainer Agent trên docker host mà bạn muốn add vào. 
  * Chạy Container từ image Portainer/Agent.


    
    
    # docker run -d -p 9001:9001 --name portainer_agent --restart=always -v /var/run/docker.sock:/var/run/docker.sock -v /var/lib/docker/volumes:/var/lib/docker/volumes portainer/agent:latest

Bước 2: Các bạn truy cập vào trang Portainer chọn mục **Enviroment** \--> **Add environment**. ![](https://cloudcraft.info/wp-content/uploads/2021/11/portainer_3-300x129.png)

  * Ở tab **Agent** , các bạn điền thông tin IP và port chạy Portainer Agent vào. Sau đó chọn**Add environment**.

![](https://cloudcraft.info/wp-content/uploads/2021/11/portainer_4-300x228.png)

  * Như vậy là các bạn đã add thêm được 1 host vào trang Portainer.

![](https://cloudcraft.info/wp-content/uploads/2021/11/portainer_5-300x171.png) Chúc các bạn cài đặt thành công!
