---
title: "Prometheus at Scale - Tổng quan về Prometheus"
date: 2020-01-14 15:00:43
categories: [Linux, Monitoring, Grafana, Prometheus]
---

# Prometheus at Scale Phần 1 - Tổng quan về Prometheus

Chao xìn mọi người, mình xin giới thiệu với mọi người loạt bài về Prometheus và ứng dụng Prometheus để monitor các hệ thống lớn. Loạt bài này sẽ gồm nhiều chủ đề phụ liên quan như: 

  * [Cài đặt Prometheus](https://cloudcraft.info/huong-dan-setup-prometheus-grafana-de-monitor-dich-vu/)
  * Long-term storage cho Prometheus
  * [Sharding Prometheus](https://cloudcraft.info/prometheus-at-scale-huong-dan-sharding-prometheus/)
  * Load balancing cho Prometheus
  * Custom Service Discovery
  * Monitor kubernetes/mesos

Tựu chung là những vấn đề thường gặp khi xây dựng và maintain một hệ thống monitor lớn. Hy vọng nhận được sự quan tâm và góp ý của mọi người. Đây là bài viết đầu tiên của loạt bài này, cung cấp một cái nhìn tổng quan về Prometheus và hệ sinh thái quây quanh nó. TL;DR: Bạn nào muốn mỳ ăn liền thì có thể coi lại [bài cũ](https://cloudcraft.info/huong-dan-setup-prometheus-grafana-de-monitor-dich-vu/) này của mình, với những hệ thống nhỏ thì cách deploy all-in-one như vậy là tương đối ổn, không cần cấu hình phức tạp. 

## Giới thiệu về Prometheus

Prometheus là 1 giải pháp monitor mã nguồn mở được phát triển ở SoundCloud vào năm 2015 và có nguyên lý hoạt động tương đối giống với BorgMon của Google. Nói sơ về cách thức hoạt động của Prometheus: 

  * **Scrape endpoints:** đọc data từ endpoints. Prometheus sẽ đọc data từ các endpoints được monitor dưới dạng **pull-mode** , khác với Zabbix hoặc InfluxDB hoạt động theo dạng **push-mode**
  * Store metrics data: data được lưu dưới dạng time series vào TSDB (time series database)
  * **API:** Cho phép truy xuất dữ liệu monitor qua API
  * **Alerting:** Check alert rule định kỳ và gởi alert

Vậy Prometheus có những ưu điểm gì nổi bật hơn các giải pháp monitor open-source khác trên thị trường 

## Kiến trúc của Prometheus

![](https://cloudcraft.info/wp-content/uploads/2019/10/Prometheus.png)

_Kiến trúc của Prometheus và hệ sinh thái đi kèm_

  * **Prometheus** : pull data từ services/targets và lưu dữ liệu xuống local storage
  * **Pushgateway** : làm điểm trung gian nhận push metrics từ các tác vụ xử lý không yêu cầu real-time monitoring (Như xử lý dữ liệu offline, các task backup...)
  * **Alertmanager** : quản lý alert và gởi alert tới các kênh thông tin như slack, chat, email
  * **Grafana** : vẽ đồ thị và quản lý dashboard, kết nối với Prometheus bằng PromQL thông qua API.
  * **Service discovery** : Prometheus mặc định hỗ trợ khá nhiều loại service discovery như: k8s, marathon, EC2, GCE, DNS, Consul, Openstack... Bạn có thể tự viết 1 module service discovery để tích hợp vào Prometheus, mình sẽ nói kỹ vấn đề này trong 1 bài sau.
  * **Exporters** : client trên các server để expose các metrics, sau đó Prometheus sẽ scrape các thông tin này định kỳ (mặc định là 15s/lần). List các exporter hiện đang có sẵn: [link](https://prometheus.io/docs/instrumenting/exporters/). Nhưng bạn cũng có thể tự viết exporter của riêng mình, mình cũng sẽ hướng dẫn cách viết trong 1 bài viết sau này.



## Cài đặt Prometheus

Các bạn có thể tham khảo một trong 2 cách cài đặt sau: **Cách 1:** Cài đặt manual, step by step tại bài viết này: [Hướng dẫn cài đặt và cấu hình Prometheus và Grafana để monitor dịch vụ](https://cloudcraft.info/huong-dan-setup-prometheus-grafana-de-monitor-dich-vu/) **Cách 2:** Hoặc các bạn có thể sử dụng Ansible playbook để deploy Prometheus: <https://github.com/nduytg/ansible_roles/tree/master/roles/prometheus> Tham khảo: [Cách sử dụng Ansible Role để deloy service](https://cloudcraft.info/ansible-huong-dan-su-dung-va-quan-ly-role-trong-ansible/)

## Tham khảo

https://prometheus.io/docs/introduction/overview/
