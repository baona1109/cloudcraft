---
title: "[NFV] DPDK - Data Plane Development Kit"
date: 2018-07-12 16:04:28
categories: [NFV]
---

# Giới thiệu về DPDK

[**_Developer(s)_**](https://en.wikipedia.org/wiki/Software_developer) | [6WIND](https://en.wikipedia.org/wiki/6WIND), [Intel](https://en.wikipedia.org/wiki/Intel)  
---|---  
**_Vendors support_** | 6WIND, ALTEN Calsoft Labs, Advantech, Brocade, BigSwitch Networks, Radisys, Tieto, Wind River, Lanner  
[**_Stable release_**](https://en.wikipedia.org/wiki/Software_release_life_cycle) | 16.11 / 13 November 2016  
**_Development status_** | Active  
**_Written in_** | C  
[**_Operating system_**](https://en.wikipedia.org/wiki/Operating_system) | [FreeBSD](https://en.wikipedia.org/wiki/FreeBSD), [Linux](https://en.wikipedia.org/wiki/Linux)  
[**_Type_**](https://en.wikipedia.org/wiki/Software_categories#Broad_categories) | Packet Processing  
[**_License_**](https://en.wikipedia.org/wiki/Software_license) | [BSD](https://en.wikipedia.org/wiki/BSD)  
**_Website_** | [dpdk.org](http://dpdk.org)  
  **_DPDK_** là viết tắt của cụm từ **_Data Plane Development Kit_** là một tập thư viện và driver (cho các network interface controller). DPDK ban đầu do [Intel phát triển](http://dpdk.org/about) nhằm hỗ trợ việc tăng tốc độ xử lý gói tin trên các dòng chip Intel x86 (từ dòng Atom cho đến dòng Xeon) và hiện nay đã hỗ trợ nhiều dòng chip khác như IBM Power 8, EZchip TILE-Gx và ARM. DPDK ra đời nhằm giúp các dòng CPU đa nhiệm (general-purpose CPU) tăng tốc độ xử lý các gói tin. Intel đã đưa ra một vài thông số để chứng minh hiệu năng làm việc của DPDK như sau: với chip Xeon E5-2658 v4 thì tốc độ forward các gói tin ở Layer 3 (mỗi gói tin có chiều dài 64 byte) thì tốc độ xử lý có thể đạt đến 233Gbps [(1)](http://www.intel.com/content/www/us/en/communications/data-plane-development-kit.html).  Vậy thì DPDK giúp ích được gì cho NFV? Trong thực tế, các thiết bị mạng phổ biến hiện nay đa phần đều sử dụng các dòng chip riêng của từng hãng (custom ASIC, network processor, các dòng chip đã được tối ưu cho các thiết bị mạng). Những dòng chip này có thể hỗ trợ throughput lên đến hàng trăm gigabit per second. _Tốt quá rồi nhỉ, ỏng ẹo đòi DPDK vớ vẩn làm gì?_ Xin thưa, các dòng chip này (chip ASIC) đều là công nghệ riêng của từng hãng (Xít cô thì xài chip xít cô, thuật toán của xít cô, du ni pơ thì xài chip của du ni pơ) => mắc, độc quyền, không linh hoạt, vòng đời phát triển lâu, phụ thuộc quá nhiều vào 1 hãng phát triển. Chốt lại là dùng CPU thường để xử lý gói tin cho rẻ. Tuy nhiên những loại CPU thông dụng (core i, xeon) thì lại ko xử lý gói tin nhanh như các NPU và ASIC (vi mạch tích hợp dành riêng cho router). Trong thời thế hiện nay, việc phát triển các dòng CPU phổ thông và phần mềm tăng tốc (như DPDK) đang ngày càng nhanh hơn, rẻ hơn.  Việc này giúp các dòng CPU phổ thông có thể đảm trách được việc xử lý các chức năng mạng với hiệu suất tương đương hoặc thậm chí có phần nhanh hơn, linh hoạt hơn so với các dòng NPU, ASIC truyền thống. Thế nên Intel mới đẻ ra DPDK :D

## Vậy thì DPDK hiện đang được ứng dụng ở đâu?

Ở dưới đây nè, hì hì :D (Nguồn: Intel) **CPUs** Hiện rất nhiều loại kiến trúc CPU đang hỗ trợ DPDK: Intel x86_64, ia32, Power 7/8, Tilera (EZChip). **NICs**

  * R2.1: Intel, Cisco (VIC), Mellanox, Broadcom (Qlogic), Chelsio
  * R2.2: +NetFPGA,...

**OSes**

  * Ubuntu
  * Redhat, Fedora

**Hypervisors**

  * KVM
  * VMware
  * XEN



# Chức năng

Về lý thuyết, DPDK có thể tăng tốc độ xử lý gói tin trên CPU nhanh hơn** _25x lần_** so với tốc độ bình thường trên linux (số liệu do Intel đưa ra) ![](https://cloudcraft.info/wp-content/uploads/2018/07/dpdk.jpg)

**_Nguồn: Intel_**

DPDK có thể:

  * Gởi và nhận gói tin với ít chu kỳ CPU nhất (thường thì ít hơn 80 chu kỳ CPU)
  * Phát triển các thuật toán bắt gói tin nhanh (như tcpdump)
  * Chạy các ứng dụng mạng của bên thứ 3.
  * Tối ưu việc quản lý bộ đệm
  * Chuyển thao tác nhận gói tin từ push sang poll. Giúp giảm số lần interrupt, context switch và buffer copy nhằm tăng hiệu năng

Nhiều chức năng mạng sử dụng DPDK có thể xử lý tới hàng trăm triệu frame một giây, xử lý các gói tin với kích thước 64 bytes dùng card NIC PCIe*. Tuy nhiên DPDK cũng còn có nhiều khuyết kiểm, người quản trị sẽ phải dành ra kha khá nhân CPU chỉ để xử lý gói tin. Các CPU đắt tiền này sẽ loop liên tục với tốc độ hàng GHz chỉ để chờ gói tin tới (tức là bình thường sẽ không làm gì cả, idle state). Một số khái niệm, kỹ thuật liên quan (khi nào quởn mình sẽ đăng về những vấn đề này)

  * Huge Pages
  * NUMA
  * Intel DDIO (Data Direct I/O Technology)
  * Pthreads
  * Cache Alignment



## Ứng dụng

![](https://cloudcraft.info/wp-content/uploads/2018/07/dpdk-2.jpg)

_Hiện đã có hơn 30 ứng dụng được viết trên nền tảng của DPDK (Nguồn: Intel)_

Bài viết này chỉ giới thiêu tổng quan về DPDK thôi. Do đây là một vấn đề khá chuyên sâu, nếu bạn nào quan tâm thì có thể tìm hiểu thêm tại đây. Theo link hướng dẫn này của Intel, có khá đầy đủ hướng dẫn để các bạn vọc vạch thử nghiệm: [Link](https://software.intel.com/en-us/articles/data-plane-development-kit-dpdk-getting-started)
