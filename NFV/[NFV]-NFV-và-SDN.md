---
title: "[NFV] NFV và SDN"
date: 2018-07-12 14:20:06
categories: [NFV]
---

Nếu như khái niệm NFV xuất phát từ nhu cầu của các nhà cung cấp dịch vụ muốn giảm bớt chi phí đầu tư các thiết bị phần cứng bằng cách ảo hóa các thiết bị mạng để có thể triển khai các dịch vụ mạng trên phần cứng phổ thông, thì SDN lại xuất phát từ các trường đại học, viện nghiên cứu, data center muốn tách bạch việc điều khiểu mạng khỏi các thiết bị vật lý, để dễ dàng cấu hình, quản lý tập trung một lượng lớn các thiết bị này. 

Về bản chất, hai công nghệ này là độc lập với nhau. Bên này có thể áp dụng được vào thực tiễn mà không cần phụ thuộc vào bên kia. Thật khó để nói được SDN hay NFV, công nghệ nào tốt hơn. Vì như đã so sánh rõ ở trên, hai công nghệ này phục vụ cho 2 mục đích hoàn toàn khác nhau.

**Tiêu chí so sánh** | **SDN** | **NFV**  
---|---|---  
**Mục đích** | Phân tách giữa control plane và data plane, quản lý tập trung, cấu hình mạng bằng cách lập trình | Chuyển dời các chức năng mạng từ phần cứng chuyên dụng sang các thiết bị phổ thông.  
**Đối tượng phục vụ** | Các viện nghiên cứu, trung tâm dữ liệu | Các nhà cung cấp dịch vụ mạng.  
**Thiết bị** | Máy chủ, thiết bị chuyển mạch phổ thông | Máy chủ, thiết bị chuyển mạch và lưu trữ phổ thông  
**Ứng dụng** | Điều phối mạng. Quản lý luồng traffic đi qua các thiết bị. | Ảo hóa các thiết bị mạng như: router, firewall, CDN,… Khởi tạo và triển khai hàng loạt các thiết bị ảo.  
**Tổ chức chuẩn hóa** | Open Networking Forum | ETSI NFV Working Group  
  
_Vài nét so sánh giữa SDN và NFV_

Mục tiêu chung của cả SDN và NFV là điều khiển hạ tầng mạng dễ dàng hơn, tiết kiệm chi phí và hạn chế việc tương tác trực tiếp với các thiết bị phần cứng. Như vậy, ta có thể thấy rằng hai công nghệ này không hề đối chọi với nhau mà còn lại bổ sung, hoàn thiện lẫn nhau, tạo nên 1 giải pháp hoàn chỉnh cho ngành viễn thông. 

Việc quản lý tập trung của SDN kết hợp với khả năng ảo hóa các thiết bị mạng của NFV sẽ đem lại những lợi ích vô cùng lớn với hạ tầng viễn thông. Đặc biệt là việc chuẩn bị cho công nghệ 5G sắp tới thì việc ứng dụng 2 công nghệ này tạo nên nền tảng cho 5G (theo như AT&T).

## Kết hợp SDN vào NFV

Mục tiêu chính của công nghệ NFV chính là khởi tạo và cấu hình các thiết bị mạng một cách nhanh chóng. Tuy công nghệ NFV có thể điều chỉnh luồng dữ liệu khi khởi tạo các thiết bị mạng nhưng khó có thể điều chỉnh lại các luồng dữ liệu đã được thiết lập này.

Chưa kể đến một số tính năng nâng cao như lọc gói tin, header, QoS thì các giải pháp NFV hiện nay vẫn còn thiếu rất nhiều tính năng và không thể so sánh được với công nghệ SDN, vốn chuyên dùng để điều chỉnh các luồng dữ liệu.

Chính vì vậy, kết hợp SDN vào hạ tầng NFV chính là lời giải đáp hoàn chỉnh cho vấn đề này.

  ![](https://cloudcraft.info/wp-content/uploads/2018/04/nfv-sdn-1.png)

_Mô hình kiến trúc tổng quan 1 hạ tầng NFV + SDN do Verizon đề ra (Coi bảng chú thích bên dưới)_

Trong mô hình này việc điều chỉnh luồng dữ liệu sẽ được chia làm 2 giai đoạn:

  * **Giai đoạn 1:** là giai đoạn khởi tạo ban đầu do khối MANO của NFV đảm nhận. Cụ thể, thông tin về đường mạng, các liên kết giữa các VNF/VNFC cũng như việc kết nối chúng lại thành VNFFG rồi từ đó trở thành NS hoàn chỉnh sẽ được thể hiện trong các tập tin đặc tả. Khối MANO sẽ đọc cái tập tin này, cấp phát tài nguyên rồi khởi tạo thành dịch vụ mạng hoàn chỉnh.
  * **Giai đoạn 2:** là giai đoạn sau khởi tạo. Sau khi hệ thống đã được triển khai hoàn chỉnh, việc điều chỉnh luồng lúc này sẽ được giao cho SDN Controller phụ trách. Khi xuất hiện luồng dữ liệu thỏa các tập rule đã định nghĩa từ trước, SDN Controller sẽ điều chỉnh luồng dữ liệu đi qua các node VNF theo một lộ trình đã được định sẵn trong các VNFFG. Người điều khiển hoàn toàn có thể lập trình sẵn hoặc điều chỉnh lại các tập rule để điều chỉnh luồng cho thích hợp với yêu cầu dịch vụ.

**Bảng chú thích 1** **STT** | **Mô tả**  
---|---  
**_1_** | Network Engineers khởi tạo một dịch vụ hoàn chỉnh thông qua portal (giao diện đồ họa hoàn chỉnh). Portal này sẽ có sẵn danh sách các dịch vụ mà hệ thống hỗ trợ (NAT, VPN, Load Balance…) cùng các thông số đi kèm.  
**_2_** | Các thông tin, thông số của dịch vụ được End-to-End Orchestator (EEO) tiếp nhận.  
**_3_** | EEO lấy thông tin mô tả dịch vụ (End-to-End Service Descriptor) từ các file template được định nghĩa trước. Việc này nhằm xác định lượng tài nguyên cần thiết và vị trí cài đặt các VNF mới.  
**_4_** | NFVO (lúc này là một thành phần trong EEO) và VNFM liên lạc với nhau để thực hiện các bước chuẩn bị để khởi tạo VNF như: kiểm tra lượng tài nguyên khả dụng, cấp phép cấp phát tài nguyên,..  
**_5a_** | VNFM liên hệ với VIM để yêu cầu tạo các _máy ảo_ cần thiết để cho VNF chạy lên. _(VNFM driven_).  
**_5b_** | NFVO liên hệ với VIM để yêu cầu tạo các _máy ảo_ cần thiết để cho VNF chạy lên. _(NFVO driven_).  
**_6_** | VIM liên hệ với NFVI để yêu cầu tài nguyên phần cứng cần thiết nhằm khởi tạo VM.  
**_7_** | VNFM tiến hành cài đặt VNF lên các VM vừa được tạo.  
**_8_** | Đối với những VNFs nào gồm nhiều VM, VNFM sẽ yêu cầu SDN Controller để tạo kết nối giữa các VM trong cùng một VNF.  
**_9_** | EEO yêu cầu SDN Controller để tạo kết nối giữa các VNF lại thành một chuỗi dịch vụ hoàn chỉnh.  
**_10_** | EEO cung cấp các thông tin đặc thù của khách hàng xuống cho các hàm dịch vụ mạng. Ví dụ như tập rule đối với Firewall, IDS,..  
  **Bảng chú thích 2** **STT** | **Mô tả**  
---|---  
**_a_** | OSS/BSS liên hệ với EEO để yêu cầu dịch vụ  
**_b_** | EEO thông qua EMS để quản lý các thiết bị mạng vật lý (_PNFs_)  
**_c_** | OSS/BSS quản lý trực tiếp các _PNFs_ bên dưới  
**_d_** | EMS quản lý PNFs  
**_e_** | EEO quản lý trực tiếp _PNFs_ bên dưới  
**_f_** | SDN Controller tạo kết nối giữa PNFs - PNFs hoặc giữa PNFs - VNFs. Tận dụng lại hạ tầng mạng sẵn có.  
**_g_** | Phần cứng bên dưới của NFVI gồm các switch, server có hiệu năng cao, ko phụ thuộc vào công nghệ của các hãng (high volume servers, switches).  
**_h_** | Service Assurance (SA) thu thập thông tin alarm, monitor hệ thống. Những thông tin này có thể được dùng để sửa lỗi, phân tích tình hình…
