---
title: "Tổng quan về Hyper-V"
date: 2017-12-29 08:42:13
categories: [Virtualization, Hyper-V, Windows]
---

Được ra mắt kể từ phiên bản Windows server 2008, Hyper-V là công nghệ ảo hóa thế hệ mới dựa trên nền tảng hypervisor của Microsoft. ![](https://cloudcraft.info/wp-content/uploads/2017/12/tong-quan-ve-hyper-v-1.png)

# Tổng quan

Microsoft cung cấp Hyper-V thông qua 2 dạng: 

  * **Hyper-V server** : được xem là một native hypervisor, hay còn gọi là hypervisor loại I (hypervisor chạy trực tiếp trên phần cứng vật lý)
  * **Một thành phần của Windows** : Hyper-V có thể được cài đặt dưới dạng một role trên các bản Windows server hay một feature trên các bản Windows 8, Windows 8.1 và Windows 10.

Công nghệ Hyper-V mang đến cho người dùng (chủ yếu là doanh nghiệp) một nền tảng ảo hóa mạnh và linh hoạt, có khả năng mở rộng, độ tin cậy và sẵn sàng cao. Đặc biệt, Hyper-V giúp đáp ứng nhu cầu ảo hóa mọi cấp độ cho môi trường doanh nghiệp. Ngoài ra, người dùng không cần phải mua thêm bất cứ phần mềm nào khi muốn nâng cấp hoặc khai thác các tính năng ảo hóa của server. 

# Cấu trúc

Có một điều sẽ khiến nhiều người dùng cảm thấy bối rối khi nhắc tới Hyper-V đó là: Hyper-V không chạy trên Windows. Đúng là bạn có thể cài đặt Windows trước, rồi sau đó bật tính năng Hyper-V. Tuy nhiên, Hyper-V sẽ “trượt” xuống bên dưới Windows, lúc này Windows sẽ đóng vai trò là hệ điều hành quản lý (management OS) và chạy tại partition gốc (root partition) ở phía trên của Hyper-V. Hyper-V phân chia mỗi máy ảo thành một partition. Một partition là một đơn vị cách ly về mặt logic và có thể chứa một hệ điều hành làm việc trong đó. Thường có ít nhất 1 partition gốc chứa hệ điều hành chủ (host OS - ví dụ: Windows Server 2008) và ngăn ảo hóa, có quyền truy cập trực tiếp các thiết bị phần cứng. Tiếp theo đó, partition gốc có thể sinh các partition con (được gọi là máy ảo) để chạy hệ điều hành khách (guest OS). Một partition con cũng có thể sinh tiếp các partition con của mình. Một partition con không có quyền truy cập trực tiếp tài nguyên vật lý, mà chỉ “nhìn thấy” chúng với danh nghĩa là thiết bị ảo (virtual device). Mọi yêu cầu đến thiết bị ảo sẽ được chuyển qua VMBus đến thiết bị ở partition cha. Thông tin hồi đáp cũng được chuyển hướng thông qua VMBus. Nếu thiết bị ở partition cha cũng là thiết bị ảo, nó sẽ được chuyển hướng tiếp tục cho đến khi gặp thiết bị thực ở partition gốc. Toàn bộ tiến trình trong suốt đối với HĐH khách. ![](https://cloudcraft.info/wp-content/uploads/2017/12/tong-quan-ve-hyper-v-2.png) Một số thành phần cần chú ý trong cấu trúc của Hyper-V: 

  * **Hypervisor:** một lớp phần mềm nằm giữa phần cứng vật lý và một hoặc nhiều hệ điều hành. Nhiệm vụ chính của nó là cung cấp môi trường thực thi riêng biệt gọi là các partition. Hypervisor thực hiện điều khiển và phân luồng truy cập đến phần cứng vật lý nằm bên dưới
  * **Intergration Component (IC):** thành phần cho phép các partition con giao tiếp với các partition khác và với hypervisor
  * **Driver:** chỉ management OS mới có khả năng kết nối trực tiếp tới phần cứng vật lý. Điều đó có nghĩa là driver dành cho phần cứng vật lý chỉ được cài đặt trên management OS, nơi chúng chạy ở chế độ kernel mode
  * **Virtual Machine Worker Process (VMWP):** thực hiện các công việc giám sát và quản lý các máy ảo. Sẽ có một tiến trình nhỏ tên là VMWP.exe chạy ở chế độ user mode trên management OS với mỗi máy ảo (partition con) đang hoạt động. Và VMWP sẽ tham gia vào các tiến trình như di dời trực tiếp (live migration) và chuyển tiếp trạng thái (state transition)
  * **Virtual Machine Management Service (VMMS):** là một dịch vụ Windows chạy ở chế độ user mode trên management OS. Đúng với tên gọi của nó, dịch vụ này thực hiện giám sát trạng thái của tất cả máy ảo và quản lý Hyper-V
  * **Windows Management Instrumentation (WMI):** là một giao diện mà tại đó các công cụ như PowerShell, Hyper-V Manager, và Failover Cluster Manager tương tác với Hyper-V



# Những điều cần lưu ý trước khi cài đặt

1) **Hệ điều hành chủ (host OS)** : Hyper-V có thể được triển khai trên các nền tảng Windows sau: 

  * Windows Desktop:

_ Windows 8 (hay Windows 8.1) phiên bản Professional hay Enterprise _ Windows 10 phiên bản Professional, Enterprise hay Education ![](https://cloudcraft.info/wp-content/uploads/2017/12/tong-quan-ve-hyper-v-3.png)

  * Windows Server:

_ Windows server 2008 và 2008 R2 phiên bản Standard, Enterprise hay Datacenter _ Windows server 2012 và 2012 R2 phiên bản Standard hay Datacenter ![](https://cloudcraft.info/wp-content/uploads/2017/12/tong-quan-ve-hyper-v-4.png) **Processor** : 

  * Hyper-V chỉ có thể được triển khai trên máy chủ sử dụng processor 64bit (x86-64) có hỗ trợ ảo hóa (Intel VT hay AMD-V).
  * Mặc dù không phải bắt buộc nhưng Windows server 2008 R2 được khuyến cáo nên sử dụng CPU có hỗ trợ SLAT (Second-Level Address Translation). Và tính năng này là bắt buộc phải có đối với Windows 8

**Memory** : 

  * Tối thiểu 4GB

**Hệ điều hành khách (guest OS)** : 

  * Hyper-V của Windows server 2008 và 2008 R2 hỗ trợ các máy ảo với tối đa 4 processor mỗi máy, và hỗ trợ lên tới 384 máy ảo trên mỗi hệ thống
  * Hyper-V của Windows server 2012 và 2012 R2 hỗ trợ các máy ảo với tối đa 64 processor mỗi máy, và hỗ trợ lên tới 1024 máy ảo trên mỗi hệ thống
  * Hyper-V hỗ trợ cả máy ảo khách (guest VM) 32bit và 64bit

2) **Windows Hyper-V Server** : là bản Hyper-V độc lập (stand-alone) có thể được cài đặt trực tiếp trên phần cứng vật lý mà không cần host OS. 

  * Sử dụng giao diện dòng lệnh (command line interface) và không bao gồm bất kì role nào của Windows server
  * Hỗ trợ lên tới 64 máy ảo trên mỗi hệ thống

![](https://cloudcraft.info/wp-content/uploads/2017/12/tong-quan-ve-hyper-v-5.png) Để tìm hiểu sâu hơn về Hyper-V, các bạn có thể tham khảo thêm tại các đường dẫn sau: <https://technet.microsoft.com/en-us/library/mt169373(v=ws.11).aspx> <https://msdn.microsoft.com/en-us/library/cc768520(v=bts.10).aspx>
