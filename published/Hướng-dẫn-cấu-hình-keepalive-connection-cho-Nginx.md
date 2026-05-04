---
title: "Hướng dẫn cấu hình keepalive connection cho Nginx"
date: 2018-10-02 14:45:45
categories: [Linux]
---

Trong bài viết này, mình sẽ hướng dẫn các bạn cách thức cấu hình Nginx sử dụng keep-alive connection để tối ưu hóa kết nối giữa Nginx và các server backend. Việc cấu hình này có thể làm tăng tương đối hiệu năng của Nginx, đặc biệt là khi Nginx đóng vai trò reverse proxy. 

## Cơ chế của keepalive

HTTP là một giao thức stateless, tức không lưu lại trạng thái của kết nối. Khi client mở một kết nối TCP đến server, gởi request, nhận response, xong xuôi rồi thì server sẽ đóng kết nối này lại để tiết kiệm tài nguyên. Giờ ta giả sử client này gởi rất nhiều request tới server, với mỗi một request, client sẽ khởi tạo một kết nối, truyền dữ liệu, truyền xong thì server lại đóng kết nối. Nếu trang web đó có nhiều tài nguyên (hình, banner, hình động…) thì cách tiếp cận này không thật sự hiệu quả vì với mỗi một nội dung, trình duyệt sẽ phải mở một connection tương ứng. HTTP có chế độ keepalive, cho phép server giữ lại kết nối TCP ngay cả khi request đó đã hoàn thành. Nếu client cần gởi một request khác, client có thể dùng lại kết nối này thay vì phải tạo lại một kết nối TCP khác (overhead cao). Kết nối này có thể được hủy khi client cảm thấy không cần gởi thêm bất kỳ request nào trong tương lai nữa hoặc server nhận thấy kết nối này không có bất cứ hoạt động gì trong một khoản thời gian. Các trình duyệt hiện nay thường sử dụng nhiều kết nối keepalive để phục vụ người dùng. Ta có thể cấu hình cho Nginx sử dụng tính năng này khi tương tác với client hoặc với các upstream servers _![](https://cloudcraft.info/wp-content/uploads/2018/10/Huong-dan-cau-hinh-keepalive-connection-cho-Nginx-1.jpg)_

_So sánh giữa kết nối bình thường và kết nối dạng keepalive_

Một số lợi ích mà cơ chế keep alive này đem lại: 

  * Giảm lượng CPU và bộ nhớ tiêu thụ (vì có ít kết nối được mở hơn).
  * Giảm tắt nghẽn mạng trên đường truyền (ít kết nối TCP hơn).
  * Giảm độ trễ khi gởi tin (do dùng lại các kết nối cũ, giảm được số lần bắt tay 3 bước).



## Cấu hình keep-alive

Sau đây, mình sẽ giới thiệu về một số chỉ thị cấu hình keepalive trong Nginx 

### **keepalive_timeout**

Vì các kết nối dạng keepalive được mở trong thời gian nhất định, những kết nối này cũng tốn một lượng tài nguyên. Vì thế ta cần cấu hình thời gian timeout của các kết nối này một cách hợp lý dựa trên ứng dụng/website và traffic của ứng dụng. Tùy theo cấu hình mà có thể tăng/giảm hiệu năng của ứng dụng khi gặp tải cao. Mặc định trong nginx thì mỗi keepalive connection sẽ có timeout là 75s. Ta có thể dùng lệnh keepalive_timeout để thay đổi giá trị này theo nhu cầu thực tế. 
    
    
    http { keepalive_timeout 20s; }

Lệnh này còn có một thông số phụ thứ hai nằm trong header của gói tin trả kết quả về client **_Keep-Alive:timeout=time_**. Header này được nhiều trình duyệt chấp nhận như Mozilla, Konqueror… Ví dụ 
    
    
    http { keepalive_timeout 20s 18s; }

### **keepalive_requests**

Lệnh này dùng để cấu hình lượng requests tối đa được đi qua một keepalive connection, sau khi đã đủ số lượng request này, server sẽ đóng kết nối. Giá trị mặc định cho thông số này là 100 request cho mỗi một keepalive connection. 
    
    
    http{ keepalive_requests 20; }

### **keepalive_disable**

Lệnh này dùng để disable tính năng keepalive cho 1 số loại trình duyệt, mặc định trong nginx thì giá trị này là msie6 (Internet Explorer 6) 
    
    
    http { keepalive_disabled msie6 safari; }

### Mẫu cấu hình keepalive trên nginx

Với cấu hình này, Nginx sẽ giữ 32 kết nối tới mỗi backend server. Nếu có nhiều request được gởi đến và cần nhiều hơn 32 kết nối tới mỗi upstream server thì Nginx sẽ mở thêm một số kết nối mới để đáp ứng nhu cầu hiện tại. Sau đó khi lượng request đã trở về mức bình thường thì Nginx sẽ đóng bớt các kết nối ít được dùng nhất để giảm số lượng kết nối về lại con số 32 như ta đã quy định. 
    
    
    upstream backend_sv {
    
      server 123.123.123.123:8080;
      server 123.123.123.124:8080:
    
      keepalive 32;
      keepalive_timeout 20s;
      keepalive_requests 20;
      keepalive_disabled msie6 safari;
    }
