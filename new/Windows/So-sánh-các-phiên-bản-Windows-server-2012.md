---
title: "So sánh các phiên bản Windows server 2012"
date: 2017-12-29 09:56:39
categories: [Windows]
---

Vào ngày 04/09/2012, Microsoft đã chính thức giới thiệu Windows Server 2012 dành cho các máy chủ với 4 phiên bản khác nhau: Datacenter, Standard, Essentials, và Foundation. Windows Server 2012, có tên mã "Windows Server 8", là phiên bản phát hành thứ sáu của họ hệ điều hành Windows Server được phát triển đồng thời với Windows 8 ![](https://cloudcraft.info/wp-content/uploads/2017/12/so-sanh-cac-phien-ban-windows-server-2012-1.png) Windows Server 2012 hoạt động trên các vi xử lý kiểu Intel và bản 2012 này đã chấm dứt việc hỗ trợ cho dòng vi xử lý Itanium do HP sản xuất. Về cơ bản, hai phiên bản Datacenter và Standard đều có tính năng giống nhau, chỉ khác nhau ở license. Dưới đây là bảng so sánh cụ thể sự khác nhau giữa các phiên bản  **** | **Datacenter** | **Standard** | **Essentials** | **Foundation**  
---|---|---|---|---  
**Mục đích sử dụng** | Môi trường ảo hóa private và hybrid cloud mức cao | Môi trường ảo hóa mức thấp hoặc không ảo hóa | Doanh nghiệp nhỏ | Doanh nghiệp nhỏ muốn triển khai một máy chủ đa chức năng  
**Tính năng** | _ Đầy đủ tính năng   | _ Đầy đủ tính năng   | _ Giao diện đơn giản, cấu hình sẵn cho các dịch vụ nền tảng cloud _ Không hỗ trợ Failover Clustering, Server Core, Remote Desktop Services | _ Hạn chế khá nhiều role _ Không hỗ trợ các tính năng giống Essentials _ Không thể join domain  
**Giới hạn chip xử lý** | 64 | 64 | 2 | 1  
**Giới hạn bộ nhớ** | 4TB RAM | 4TB RAM | 64GB RAM | 32GB RAM  
**Giới hạn user** | Không giới hạn | Không giới hạn | 25 user | 15 user  
**Giới hạn ảo hóa** | Không giới hạn | 2 máy ảo | 1 máy ảo và 1 chủ vật lý (cả 2 không thể hoạt động cùng lúc) | Không hỗ trợ ảo hóa  
(*) CAL - Client Access License: license cho phép người dùng hoặc thiết bị truy cập trực tiếp / gián tiếp vào server **Tài liệu tham khảo:** <https://www.microsoft.com/en-us/licensing/product-licensing/windows-server-2012-r2.aspx> <https://en.wikipedia.org/wiki/Windows_Server_2012>
