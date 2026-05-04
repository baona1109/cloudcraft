---
title: "Elasticsearch lưu ý khi config production environment"
date: 2019-11-02 10:00:23
categories: [Database, Monitoring, ElasticSearch]
---

**[Elasticsearch] Những Lưu ý khi config production environment cho elasticsearch**

Elasticsearch là công cụ dùng để lưu trữ, phân tích, search cho hệ thống theo dõi và phân tích log. Khi cài đặt elasticsearch nếu chúng ta chỉ cần setup trên môi trường development để research em yêu khoa học thì không cần phải quan tâm quá đến config và ràng buộc của elasticsearch, chỉ việc install và sử dụng.  Tuy nhiên, khi chúng ta đưa ra môi trường production để tiến hành sử dụng thì ta không thể cài đặt theo default được mà có 1 số lưu ý về việc cấu hình lại elasticsearch. **Run elasticsearch với quyền user thường (not root)** Khi chạy môi trường production, để đảm bảo an toàn và bảo mật cho toàn hệ thống, mình không nên run elasticsearch với user root, mình cần tạo 1 user thường dành riêng cho nó và phân quyền vào các nơi cấu hình, data, log, pid,... 

**Cấu hình nơi chứa data và log**

Chúng ta cần quản lý nơi chứa data và log vì vậy cần cấu hình lại đường dẫn 2 nơi này để tiện quản lý sau này: **path.data** và **path.logs** ![](https://cloudcraft.info/wp-content/uploads/2019/11/elasticsearch-luu-y-khi-config-production-environment-2-300x150.jpg) **Cấu hình lại name của node và cluster** Trong trường hợp cần cấu hình chạy elasticsearch theo cluster thì ta cần khai báo **node.name**. Chúng ta cũng cần cấu hình **cluster.name** để các node được share cùng cluster name sẽ có thể join vào cluster.  **Cấu hình lại IP và port listen** Cấu hình IP mà elasticsearch sẽ dùng để listen nhận request, thường hệ thống production để tiết kiệm băng thông cũng như bảo mật, những service gọi nội bộ thường đi đường private và elasticsearch cũng vậy, để tăng tính bảo mật mình nên chỉ định IP mà elasticsearch sẽ listen, hạn chế listen all.

  * **network.host:** IP listen của dịch vụ.
  * **transport.host:** IP listen giao tiếp giữa các node trong cluster, thường thì giá trị này sẽ bằng với network.host, nếu có nhiều card mạng và cần phân biệt giữa listen dịch vụ và listen cluster thì có thể dùng

![](https://cloudcraft.info/wp-content/uploads/2019/11/elasticsearch-luu-y-khi-config-production-environment-3-300x246.png) **Quan tâm về tiêu chuẩn boostrap check** Nếu sử dụng **transport.host** hoặc **network.host** là loopback, localhost (default) thì elasticsearch sẽ không tiến hành bootstrap checks và sẽ run theo môi trường development. Nếu set transport_host là 1 IP khác loopback thì cần phần tuân thủ bootstrap check:

  * Heap size check
  * File descriptor check
  * Memory lock check
  * Maximum number of threads check
  * Max file size check
  * Maximum size virtual memory check
  * Maximum map count check
  * Client JVM check
  * Use serial collector check
  * System call filter check
  * OnError and OnOutOfMemoryError checks
  * Early-access check
  * G1GC check
  * All permission check
  * Discovery configuration check

Các bạn tham khảo chi tiết thêm tại: [Here](https://www.elastic.co/guide/en/elasticsearch/reference/current/bootstrap-checks.html) **Kiểm tra và cấu hình lại Discovery** Khi config lại network.host thì default elasticsearch sẽ hiểu chạy theo cluster, nếu bạn muốn sử dụng cluster thì cần cấu hình các seed host nếu không khi bootstrap check sẽ báo lỗi không cho start dịch vụ

  * **discovery.seed_hosts:** Khai báo các host tham gia vào cluster
  * **cluster.initial_master_nodes:** Khai báo các node đủ điều kiện tham gia vote bầu master trong lần đầu start cluster

Ngoài ra nếu muốn chạy standard-alone mình nên cấu hình **discovery.type: single-node**. Khi đó mình không cần quan tâm tới 2 biến trên trong quá trình setup Dưới đây là example một phần về config của elasticsearch 
    
    
    # ------------------------------------ Node ------------------------------------
    node.name: $NAME
    node.master: true
    node.data: true
    # ------------------------------------ Paths ------------------------------------
    path.data: $ES_HOME/data
    path.logs: $ES_HOME/logs
    # ------------------------------------ Network ------------------------------------
    transport.host: $ES_HOST
    #transport.tcp.port: 9300
    transport.profiles.default.port: 9300
    network.host: $ES_HOST
    http.port: 9200
    # ------------------------------------ Discovery ------------------------------------
    discovery.seed_hosts: ["$ES_HOST:9300"]
    #discovery.type: single-node
    cluster.initial_master_nodes:
    - ${NAME}
    # ------------------------------------ Security ------------------------------------
    # Option for security, authentication, if use it, should config additional TLS option for cluster, the other config discovery.type: single-node
    xpack.security.enabled: true

  **Tham khảo**<https://www.elastic.co/guide/en/elasticsearch/reference/current/index.html>
