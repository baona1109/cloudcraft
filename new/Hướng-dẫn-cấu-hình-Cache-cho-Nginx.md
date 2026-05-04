---
title: "Hướng dẫn cấu hình Cache cho Nginx"
date: 2018-09-28 07:00:21
categories: [Linux]
---

Caching là một trong những kỹ thuật đơn giản mà hiệu quả để tăng tốc độ tải website, đặc biệt là khi Nginx đóng vai trò Reverse Proxy. Sử dụng cache có thể làm giảm đáng kể tài nguyên tiêu thụ của hệ thống, đồng thời làm tăng trải nghiệm cho người dùng. Trong bài viết này, mình sẽ hướng dẫn các bạn cách cấu hình lưu cache trên Nginx. 

### Cơ chế của caching trên Nginx

Khi bật cơ chế caching, Nginx sẽ lưu kết quả trả về từ upstream server lại trên disk (hoặc RAM nếu ta cấu hình lưu cache trên shared memory **/dev/shm**) và dùng kết quả này để trả lời cho client mà không cần phải chuyển tiếp request cho upstream. Một số lợi ích mà caching đem lại cho cả hệ thống và người dùng: 

  * **Tăng hiệu năng** của ứng dụng/trang web: cache lại kết quả của những request tương tự nhau, không cần phải xử lý các request này nhiều lần.
  * **Tăng khả năng chịu tải** của các upstream server.
  * **Tăng tính sẵn sàng** cho dịch vụ: nginx sẽ trả kết quả cache cho người dùng nếu như các upstream server gặp sự cố không truy cập được.

_![](https://cloudcraft.info/wp-content/uploads/2018/07/word-image-1.png)_

_Cơ chế caching của Nginx_

Cơ chế caching cụ thể của Nginx như sau: 

  * _1a_ : Client A gởi một request tới nginx
  * _1b_ : Trong cache key hiện tại không có key tương ứng với request của client A. Nginx sẽ chuyển request này cho các upstream server ở phía sau.
  * _1c_ : Upstream server phản hồi và gởi lại kết quả cho Nginx, Nginx lưu lại kết quả này trong cache.
  * _1d_ : Nginx gởi lại kết quả cho client.
  * _2a_ : Client B gởi một request mới tương tự như request lúc nãy của client A.
  * _2b_ : Nginx lấy lại kết quả phản hồi của client A lúc nãy trong cache và gởi cho client B mà không cần phải liên hệ với upstream server.



### Quản lý cache

Có 2 tiến trình của Nginx quản lý bộ nhớ cache: 

  * **Cache manager** : được kích hoạt định kỳ để kiểm tra trạng thái của cache. Nếu kích thước của cache vượt quá giá trị **_max_size_ **thì cache manager sẽ xóa bỏ phần dữ liệu ít được truy cập nhất.
  * **Cache loader** : tiến trình này chỉ hoạt động duy nhất một lần ngay khi Nginx khởi động. Nó có nhiệm vụ nạp **metadata** của dữ liệu được cache trước đó vào bộ nhớ chung. Nạp lại toàn bộ cache khi nginx khởi động sẽ làm chậm Nginx, để giảm thiểu việc này, ta có thể cấu hình load cache tuần tự theo thời gian, mỗi lần sẽ load một phần cache lên.

Cấu hình load cache tuần tự trong nginx: 
  * **loader_threshold** – Thời gian mỗi lần nạp cache (miliseconds)
  * **loader_files** – Số lượng file tối đa nginx nạp trong mỗi lần (mặc định là 100).
  * **loader_sleeps** – Thời gian giữa mỗi lần nạp cache (miliseconds), mặc định là 50ms.

Trong ví dụ sau, nginx sẽ load tối đa **200** file trong cache tại folder **/data/nginx/cache** trong khoảng thời gian là**300ms** , mỗi lần cách nhau **50ms** (giá trị mặc định). 
    
    
    proxy_cache_path /data/nginx/cache keys_zone=one:10m loader_threshold=300 loader_files=200;

Bạn cũng có thể thay đường dẫn **/data/nginx/cache** => **/dev/shm** để cache trên RAM nếu muốn (ai cứng tay thì hãy thử ;) ) 

### Cấu hình Caching

#### Cache path

Để bật cơ chế caching, ta cần phải thêm chỉ thị **_proxy_cache_path_** trong block ** _http { }_**
    
    
    http {
    
    ...
    
    proxy_cache_path /data/nginx/cache keys_zone=one:10m;
    
    }

#### Set-Cookie

Nginx sẽ không cache các request có header Set-Cookie. Nhiều ứng dụng phía server chạy trên nền PHP, Java sẽ tạo ra cookie cho mỗi request và Nginx không thể cache được các request có dùng cookie. Để xử lý tình huống này, ta có thể cấu hình Nginx bỏ qua Set-cookie header như sau: 
    
    
    # Bỏ qua, không xử lý header Set-Cookie
    proxy_ignore_headers Set-Cookie;
    
    # Ta đẩy request lên upstream mỗi khi gặp hải header X-No-Cache từ client
    proxy_no_cache $http_x_no_cache;

### Caching directives

Một số lệnh cấu hình caching cơ bản của nginx:  **Caching module** | **Công dụng**  
---|---  
**_proxy_cache_** | Lệnh này dùng để chọn một shared memory zone dùng cho caching.  
**_proxy_cache_path_** | Lệnh này xác định vị trí lưu cache và shared memory zone (lưu active key và metadata). Một số option phụ gồm: 

  * **_“keys_zone=name:size”:_** Name là tên của key_zone chứa các cache key, dùng để mapping giữa nội dung được cache và request của người dùng. Size là kích thước bộ nhớ chứa các cache_key (khác với size chứa nội dung được cache). Mỗi key chiếm khoản 0.125 kB bộ nhớ nên với 10MB, ta có thể lưu được 80,000 cache key.
  * **_levels_** : thông số này quyết định độ sâu của cây thư mục được lưu trên cache. Giá trị này tối đa là 3.
  * proxy_cache_path /data/nginx/cache levels=1:2 keys_zone=one:10m;
  * **_inactive_** : thời hạn lưu trữ tối đa của cache, nếu quá thời gian này mà vẫn không có ai request thì cache sẽ bị xóa.
  * proxy_cache_path /data/nginx/cache keys_zone=one:10m inactive=60m;
  * **_max_size_** : giới hạn dung lượng bộ nhớ tối đa để được lưu trên cache. Khi nội dung lưu cache vượt quá giới hạn này thì cache manager sẽ xóa nội dung ít được truy cập nhất.
  * proxy_cache_path /data/nginx/cache keys_zone=one:10m max_size=200m;

  
**_proxy_cache_bypass_** | Khi điều kiện đi kèm được thỏa (chuỗi điều kiện khác 0, khác rỗng) thì lệnh này sẽ by-pass request tới upstream server mà không dùng cache. proxy_cache_bypass $cookie_nocache $arg_nocache$arg_comment; proxy_cache_bypass $http_pragma $http_authorization;  
**_proxy_cache_key_** | Lệnh này dùng để list cụ thể một string dùng để cache các giá trị. proxy_cache_key "$host$request_uri $cookie_user"; proxy_cache_key $scheme$proxy_host$request_uri;  
**_proxy_cache_lock_** | Khi bật tính năng này, nếu người dùng không tìm thấy một nội dung có sẵn trên cache, nginx sẽ chặn không cho toàn bộ request được chuyển về upstream server mà chỉ cho phép 1 request tới upstream server. Các request còn lại sẽ phải chờ cho request đầu tiên trở về và tạo ra nội dung tương ứng trong cache. Lock này có hiệu lực trên mỗi worker process. proxy_cache_lock on;  
**_proxy_cache_lock_timeout_** | Lệnh này quy định thời gian một request phải chờ cho nội dung cache được cập nhật, đây cũng là thời gian chờ cho **_proxy_cache_lock_** hết hạn, request sẽ được chuyển tới upstream server.  
**_proxy_cache_min_uses_** | Ta cũng có thể cho Nginx biết một nội dung nào là cần cache, chỉ cache khi nội dung được request tối thiểu 5 lần. Tính năng này rất hữu dụng khi có nhiều nội dung trên web nhưng ta chỉ muốn cache những nội dung nào được load nhiều nhất. proxy_cache_min_uses 5;  
**_proxy_cache_background_update_** | Update cache ở dạng background, tạm thời phục vụ cache cũ cho người dùng. Cần bật tính năng **_proxy_cache_use_stale_**  
**_proxy_cache_use_stale_** | Lệnh này dùng để quyết định khi nào thì nginx có thể phục vụ nội dung cache đã cũ cho client (error, timeout, cache đang update, header bị sai…) proxy_cache_use_stale error timeout http_500 http_502 http_503 http_504; Ví dụ dưới đây dùng cho trường hợp khi nginx đang load cache mới từ backend, nginx vẫn có thể phục vụ tiếp nội dung cache cũ cho client. proxy_cache_lock on; proxy_cache_use_stale updating;  
**_proxy_cache_valid_** | Cấu hình thời gian caching dựa trên loại kết quả trả về từ upstream server. Ví dụ: proxy_cache_valid 200 302 10m; proxy_cache_valid 404 5m; proxy_cache_valid any 5m;  
**_proxy_cache_method_** | Mặc định thì nginx chỉ cache các request GET và HEAD, ta có thể cache những request khác với lệnh **_proxy_cache_methods_** proxy_cache_methods GET HEAD POST;  
**_proxy_cache_revalidate_** | Bật tính năng refresh cache từ upstream server nếu trong header của gói tin có những trường If-Modified-Since và If-None-Match headers. proxy_cache_revalidate on;  
**_proxy_ignore_headers_** | Bỏ qua, không xử lý những HTTP Header sau: 

  * X-Accel-Expires, Expires, Cache-Control, Set-Cookie, và Vary: các header này quy định thời gian caching nội dung.
  * X-Accel-Redirect: nội dung tới một URI khác.
  * X-Accel-Limit-Rate: giới hạn tốc độ gởi trả kết quả về cho client.
  * X-Accel-Buffering: bật/tắt tính năng buffer cho response.
  * X-Accel-Charset: quy định bảng mã của kết quả trả về cho client (Ví dụ : UTF-8).

<https://en.wikipedia.org/wiki/List_of_HTTP_header_fields>  
  
# Tham khảo

<https://www.thuysys.com/toi-uu/tim-hieu-caching-va-cach-tang-toc-website-tren-nginx.html> <https://www.digitalocean.com/community/tutorials/understanding-nginx-http-proxying-load-balancing-buffering-and-caching> <https://www.nginx.com/blog/nginx-caching-guide/> <http://nginx.org/en/docs/http/ngx_http_proxy_module.html> <https://www.nginx.com/blog/tuning-nginx/> [z.com/2015/03/30/nginx-caching-tutorial/](http://czerasz.com/2015/03/30/nginx-caching-tutorial/)
