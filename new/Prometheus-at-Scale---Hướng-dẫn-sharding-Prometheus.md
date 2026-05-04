---
title: "Prometheus at Scale - Hướng dẫn sharding Prometheus"
date: 2020-01-16 14:41:29
categories: [TSDB, Monitoring, Prometheus, Database, High Availability, Load Balancing]
---

# Prometheus at Scale Hướng dẫn sharding Prometheus

## Giới thiệu

Chao xìn mọi người, trong bài viết này, mình sẽ hướng dẫn các bạn cách sharding Prometheus để tăng cường hiệu năng và đảm bảo tính High Availability cho hệ thống monitor của các bạn.

## Bài toán scaling Prometheus

Nếu bạn nào thường xuyên làm việc với database thì hẳn sẽ không xa lạ gì với thuật ngữ sharding này. Đây là cách chia data thành nhiều mảnh nhỏ (shards) và lưu trên nhiều instance khác nhau để tối ưu performance cho hệ thống, bài viết này sẽ hướng dẫn các bạn dùng

Ở đây, mình sẽ đặt ra 1 tình huống giả sử. Công ty của bạn có 1 group server gồm 600 instance cần monitor. Trong thời gian đầu, bạn setup 1 server [Prometheus](https://cloudcraft.info/prometheus-at-scale-tong-quan-ve-prometheus/) để monitor 600 instance này và mọi thứ đều hoạt động ổn thoả, tuy nhiên, theo thời gian, lượng data thu thập và lượng người truy xuất data từ Prometheus ngày một nhiều, hệ thống bắt đầu có dấu hiệu quá tải như hình sau:

![](https://cloudcraft.info/wp-content/uploads/2020/01/Screenshot-2020-01-16-at-2.38.24-PM.png)_Prometheus slow query, hơn 5s cho 1 query_

Đây chính là một trong những dấu hiệu cho thấy server Prometheus của bạn bắt đầu quá tải. Cách đơn giản nhất để xử lý tình huống này ở đây là scale up cho Prometheus instance của bạn, cụ thể là tăng thêm CPU, RAM, Disk.

## Hướng giải quyết

Tuy nhiên đây chỉ là giải pháp tình thế, sức người có hạn, kẻ thù thì quá đông và nguy hiểm. Về lâu dài bạn cần phải add thêm nhiều server Prometheus khác để xử lý lượng data ngày một tăng cũng như đảm bảo rằng bạn sẽ không bị mất monitor data nếu chẳng may con Prometheus chính của bạn ngã ngựa giữa đường.

Cụ thể hơn, mình đề xuất bạn scale cluster Prometheus của bạn lên 3 nodes. Nhưng tại sao lại là 3 nodes? Vì 3 node chính là con số cơ bản nhất để có thể vừa tăng performance, vừa đảm bảo cluster vẫn hoạt động tốt nếu có 1 node tèo.

Ta cùng phân tích, nếu bạn chỉ có 1 node, performance của bạn vừa thấp lại vừa không đảm bảo độ an toàn. Node đó chết, là chết ráo. Tăng thêm 1 node, giờ bạn có 2 node cùng scrape toàn bộ target.

Lúc này, nếu 1 node die, bạn vẫn còn 1 node, data của bạn vẫn được bảo toàn. Tuy nhiên ta chưa thể thực hiện sharding được nếu chỉ có 2 node, lúc này 2 node của bạn sẽ là full-backup của nhau, mỗi node vẫn phải chịu load nặng như nhau => performance vẫn chậm => chưa hoàn toàn ổn.

Giờ ta tăng thêm 1 node nữa, lúc này ta đã có thể thực hiện sharding được rồi. Cụ thể là chia tổng số lượng target thành 3 phần (tương đối đều nhau), lần lượt gọi là: **Shard 1** , **Shard 2** và **Shard 3**. Cụ thể như hình vẽ sau

![prometheus-at-scale-sharding-prometheus-before](https://cloudcraft.info/wp-content/uploads/2020/01/prometheus-at-scale-sharding-prometheus-before-1024x1024.png)_Hệ thống 3 node chạy full-backup, mỗi node chứa 3 shard_

Tiếp theo, ta sẽ rải 3 shard này ra đều trên 3 node, mỗi node giờ sẽ chứa 2 shard như hình dưới:

![prometheus-at-scale-sharding-prometheus-after](https://cloudcraft.info/wp-content/uploads/2020/01/prometheus-at-scale-sharding-prometheus-after-1024x1024.png)

_3 node Prometheus, mỗi node chứa 2 shard_

Ở hình này, ta thấy mỗi node sẽ chứa 2 shard, tức là lượng data trên mỗi node sẽ giảm được 33%. Đồng thời, nếu 1 node ngẫu nhiên gặp sự cố, 2 node còn lại vẫn sẽ tiếp tục hoạt động bình thường, đảm bảo không xảy ra mất mát dữ liệu.

Như vậy, với cách thiết kế này, ta vừa đảm bảo tối ưu được performance hơn 33%, vừa đảm bảo tính high availability của hệ thống. Các bạn có thể add thêm nhiều node vào cluster, và thử nghiệm các cách phân phối shard khác nhau và bình luận ở dưới phần comment nhé ^^. Nhưng điều kiện tối thiểu là hệ thống phải có ít nhất 3 node (giống như cách hoạt động của RAID 5 vậy).

## Cách thực hiện sharding

### Sharding Prometheus

Như vậy, mình đã phân tích xong bài toán sharding Prometheus và cách giải quyết nó, giờ ta sẽ cùng nhau thực hiện.

**Chú ý** : Cách sharding mà mình nói trong bài viết này là _**Horizontal Sharding**_ , ngoài ra còn có 1 cách khác nữa là _**Vertical Sharding**_ mà nếu có thời gian mình sẽ nói thêm ở những bài viết sau.

Ta sẽ thực hiện sharding bằng cách _**hashmod**_ và _**relabelling**_ các target. Cụ thể là chia hashmod targets ra làm 3 phần, các bạn sửa file _**prometheus.yml**_ trên mỗi node thành như sau
    
    
    global:
      external_labels:
        replica: 'A'
    scrape_configs:
     - job_name: cloudcraft_servers
       # Service discovery o day
       consul_sd_configs: 
       - server: 'consul.cloudcraft.info:8500'
       relabel_configs:
        - source_labels: [__address__]
          modulus:       3
          target_label:  __tmp_hash
          action:        hashmod
        - source_labels: [__tmp_hash]
          regex:         0|1 # Lay shard thu 1 va 2
          action:        keep

Ở Cloudcraft, mình dùng Consul làm **Service Discovery** , tuỳ môi trường khác nhau mà các bạn có thể chọn loại Service Discovery phù hợp với môi trường của mình như EC2, GCE, K8S, Openstack, file-based...

Cấu hình tương tự trên node 2
    
    
    global:
      external_labels:
        replica: 'B'
    scrape_configs:
     - job_name: cloudcraft_servers
       # Service discovery o day
       consul_sd_configs: 
       - server: 'consul.cloudcraft.info:8500'
       relabel_configs:
        - source_labels: [__address__]
          modulus:       3
          target_label:  __tmp_hash
          action:        hashmod
        - source_labels: [__tmp_hash]
          regex:         1|2 # Lay shard thu 2 va 3
          action:        keep

Và node 3 
    
    
    global:
      external_labels:
        replica: 'C'
    scrape_configs:
     - job_name: cloudcraft_servers
       # Service discovery o day
       consul_sd_configs: 
       - server: 'consul.cloudcraft.info:8500'
       relabel_configs:
        - source_labels: [__address__]
          modulus:       3
          target_label:  __tmp_hash
          action:        hashmod
        - source_labels: [__tmp_hash]
          regex:         0|2 # Lay shard thu 1 va 3
          action:        keep

### Cấu hình Thanos query Prometheus

Thanos là một [project](https://thanos.io/) dùng để query và load balancing nhiều Prometheus instance khác nhau (Không phải là gã Titan điên màu tím thích búng tay xoá sổ vũ trụ đâu nhé).

Mình sẽ nói kỹ hơn về cách cài đặt và cấu hình Thanos ở bài viết sau. Còn ở đây, ta sẽ cấu hình Thanos query 3 Prometheus instance như sơ đồ dưới

![prometheus-at-scale-sharding-prometheus-thanos](https://cloudcraft.info/wp-content/uploads/2020/01/prometheus-at-scale-sharding-prometheus-thanos.png)

_Thanos query 3 Prometheus instance, merge và deduplicate data_

Thanos có một tính năng đặc biệt là có thể query nhiều Prometheus instance cùng lúc, sau đó merge data lại và hiển thị ra 1 kết quả duy nhất trên Grafana.

Đây chính là lý do mình cấu hình thêm 1 _**external_label** _khác nhau cho mỗi node ở trên. Label đó giúp cho Thanos có thể phân biệt được data của từng node.

![](https://cloudcraft.info/wp-content/uploads/2020/01/thanos-inevitable-1024x576.jpg) _Sử dụng Thanos trong một hệ thống Prometheus là...không thể tránh khỏi ^^_

Do bài viết đã khá dài, mình xin tạm dừng bài viết ở đây. Ở bài viết sau, mình sẽ hướng dẫn các bạn cách thức cài đặt và cấu hình _**Thanos** cho một hệ thống Prometheus_. Hẹn gặp lại các bạn ở bài viết sau nhé^^.
