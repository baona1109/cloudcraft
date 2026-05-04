---
title: "Containers - Một cách tiếp cận mới của ảo hóa"
date: 2017-12-01 11:19:44
categories: [Container, Virtualization]
---

Containers được nhắc đến như một công nghệ ảo hóa ở mức hệ điều hành (OS Virtualization). Trong công nghệ ảo hóa này, mỗi container đóng vai trò tương tự như vai trò của máy ảo (Virtual Machines) trong công nghệ ảo hóa trước đó là ảo hóa hạ tầng (Infrastucture Virtualization). Cụ thể, container sẽ chạy như một ứng dụng (application) trên hệ điều hành, và trong mỗi container sẽ chứa ứng dụng, các thư viện và gói cài đặt cần thiết cho ứng dụng đó.

![](https://cloudcraft.info/wp-content/uploads/2017/12/containers-mot-cach-tiep-can-moi-cua-ao-hoa-1.png)

Mô hình ảo hóa hệ điều hành sử dụng Container.

# **Mô tả ngắn lịch sử Container nói chung.**

**2000** Container được thêm vào FreeBSD với tên gọi **FreeBSD Jails** **2001** Project **LINUX VSERVER** ra đời. (Dự án thực hiện để bổ sung khả năng ảo hoá ở mức hệ điều hành vào nhân Linux - Linux kernel, phát triển và phân phối dưới dạng Open-Source) **2005** Khởi động project **OpenVZ** : một kỹ thuật ảo hoá mức hệ điều hành dành cho Linux. **2006** **PROCESS CONTAINERS** là một tính năng tập hợp các tiến trình xử lý - Collection of Processes - bên trong Linux Kernel (Được phát triển bởi kỹ sư của Google) **2007** Process Containers được đổi tên thành **CGROUPS** \- Control groups - và được trộn lẫn vào nhân Linux version 2.6.24. **2008** **LXC - Linux Containers** là một phương pháp ảo hoá hệ điều hành, phân cấp và thúc đẩy chức năng của Cgroups và Namespace trong nhân Linux. **2011** Sự ra đời của **CLOUD FOUNDRY** : PaaS Open-Source Cloud computing. Được phát triển bởi VMWare, giám sát vởi Cloud Foundry Foundation, mục đích của project là xây dựng mô hình Client-Server để quản lý một tập hợp các Containers trên nhiều “host” và bao gồm cả dịch vụ quản lý cgroups, namespaces và vòng đời tiến trình - process life circle. **2013 (March)** Khởi động project **Docker** : Open-Source project. Là một công cụ hay một nền tảng mở cho phép các ứng dụng - Applications có thể chạy trong các Software-Containers. **2013 (October)** Khởi động project **Lmctfy** : Có những tính năng tương tự như của Docker và LXC, dựa trên các tính năng của Cgroups bên trong nhân Linux (phát triển bởi Google) Khởi động project CoreOS: Open-Source lightweight OS. **2014** **CRIU** (Checkpoint Restore In UserSpace) cho Docker và LXC **2014 (December)** Khởi động project **RKT: ROCKET**. Ra đời trong hoàn cảnh Docker bị phát hiện với nhiều các lỗi bảo mật do đó RKT được thiết kế để tăng tính bảo mật, tương thích cũng như mở rộng hơn so với Docker.

# So sánh, sự khác biệt giữa Container và VMs.

Trong phần này sẽ tập trung vào việc chỉ ra sự khác biệt giữa ảo hóa theo dạng container và ảo hóa theo dạng bare-metal hypervisor - truyền thống (tạm gọi là hypervisor (VMs))

![](https://cloudcraft.info/wp-content/uploads/2017/12/containers-mot-cach-tiep-can-moi-cua-ao-hoa-2.png)

Mô hình ảo hóa dạng Container so với Hypervisor (VMs).

Bảng so sánh. **Containers** | **Hypervisor (VMs)**  
---|---  
Ảo hóa hệ điều hành, phân tách ứng dụng trong cùng một hệ điều hành. | Ảo hóa phần cứng, hạ tầng. Phân tách sử dụng tài nguyên trên cùng một phần cứng.  
Ứng dụng không phụ thuộc vào tương thích hệ điều hành | Hệ điều hành không phụ thuộc vào tương thích phần cứng.  
Sử dụng một bộ nhớ duy nhất cho các containers. | Mỗi instance sử dụng một bộ nhớ nhất định.  
Khởi chạy ứng dụng trong thời gian <500 mili giây và dễ dàng mở rộng. | Khởi chạy hệ điều hành thông thường trong 20 giây tùy thuộc vào tốc độ của thiết bị lưu trữ.  
Cung cấp API tiện dụng cho việc triển khai hệ thống điện toán đám mây. | Cung cấp API hạn chế hơn so với containers.  
  Với những thông tin về đặc tính kể trên của Containers và Hypervisor (VMs), trên thực tế cả 2 loại ảo hóa này không hề triệt tiêu lẫn nhau mà bổ sung lẫn nhau cùng phát triển. Nếu khi nhắc đến ảo hóa phần cứng có lợi điểm về sử dụng tốt tài nguyên, bảo mật, tính sẵn sàng cao, thì đối với ảo hóa hệ điều hành sử dụng Containers lại cung cấp khả năng co giãn, mở rộng cho các ứng dụng, khởi động nhanh, kiểm soát vòng đời cùng với đó là việc triển khai nhanh chóng các ứng dụng.

# Những lợi ích của Container và VMs.

_Lưu ý: VMs được đề cập ở đây là những máy ảo chạy trên công nghệ ảo hóa phần cứng trực tiếp (bare-metal) sử dụng hypervisor._

### Đi từ lợi ích của Virtual Machine

Dễ dàng hiểu được ảo hoá truyền thống bằng các VMs (với các công nghệ VMWare, Hyper-V, Parallel...) sẽ ảo hoá từ phần cứng, hạ tầng máy vật lý thông qua một lớp hypervisor. Theo đó, phương pháp ảo hóa phần cứng này cho phép ta phân chia (theo phương thức sử dụng phổ biến nhất) tài nguyên của một máy tính/máy chủ thật thành nhiều máy tính ảo với đầy đủ chức năng như một máy tính thực thụ (từ phần cứng đến các thiết bị ngoại vi cần thiết). Từ đó đem lại trải nghiệm sử dụng gần tương đương, nếu không muốn nói là thực sự, như một máy vật lý truyền thống. Một số lợi ích chung của VMs có thể nêu ra như sau:

  * **Tăng hiệu quả sử dụng tài nguyên** : bằng cách phân chia một tài nguyên lớn thành nhiều tài nguyên nhỏ tuỳ vào mục đích sử dụng của từng cá nhân, từng dịch vụ. Hoặc có thể thu hồi các tài nguyên từ những máy ảo một cách nhanh chóng để thực hiện cấp phát sử dụng cho những nhu cầu lớn hơn.
  * **Giảm sự phụ thuộc vào việc đầu tư hạ tầng ban đầu** : Một doanh nghiệp muốn mở rộng hay thu nhỏ phạm vi kinh doanh không cần lo lắng về thiếu hụt tài nguyên hệ thống hay dư thừa. Hoàn toàn có thể điều chỉnh ngay tức thời khi thuê các VMs (dịch vụ máy chủ cá nhân ảo - Virtual Private Server) từ các nhà cung cấp dịch vụ.
  * **Hợp nhất phần cứng** : Khi mà một phần cứng hay một thiết bị trở nên lỗi thời và một hay một vài thiết bị không thể có đủ khả năng đáp ứng công việc đòi hỏi hỗ trợ công nghệ/giao thức mới (các ứng dụng, phần mềm, hệ điều hành, mã điều khiển,... ngày một lớn hơn và cũng đòi hỏi sử dụng các giao thức mới hơn), khi đó hypervisor sẽ biến những công nghệ mới, tưởng chừng như không thể tương thích được với phần cứng, trở thành những dòng lệnh/đoạn mã dễ hiểu đối với phần cứng bên dưới. Hơn thế nữa, các ứng dụng chạy trên VMs hoàn toàn giao tiếp với phần cứng ảo mà không quan tâm đến việc tương thích nữa. Ví dụ: ở máy vật lý có các mạng thuộc 2 nhà sản xuất khác nhau, nhưng đối với các VMs chỉ cần quan tâm đến card mạng được cấp phát và sử dụng đúng driver cho các card mạng đó.
  * **Bảo trì (maintainent), sao lưu (backup), khắc phục sự cố (troubleshoot)** : Việc sử dụng các VMs sẽ giúp các công việc bảo trì, dự phòng và tìm cũng như sửa lỗi diễn ra nhanh chóng và đơn giản hơn. Khi sử dụng một máy vật lý thật, nếu có vấn đề xảy ra thì gần như chúng ta không thể làm gì khác ngoài đến trực tiếp và xem xét tìm lỗi. Nhưng với VMs thì ta có thể nhanh chóng tạo một bản sao lưu của máy ảo đó, di chuyển sang một vị trí khác (cả về mặt vật lý), kiểm tra trạng thái lỗi (với máy thật nếu bị hư hỏng hệ điều hành thì chúng ta không thể xem log hay xem trạng thái vì không có gì thao tác với chính máy đó). Ngoài ra việc tạo một máy ảo hoàn toàn mới cũng đơn giản hơn việc cài đặt lại trên máy thật với các chức năng tương tự (sử dụng các phương pháp như: tạo mẫu (template), tạo bản sao (clone), sử dụng image...).
  * **Bảo mật** : một trong những lợi điểm được đánh giá cao của phương pháp ảo hóa hypervisor (VMs) đó là bảo mật. Bỏ qua các phương thức lây truyền thông qua mạng, các VMs trên cùng một máy host trên lý thuyết sẽ không lây truyền các mối nguy hại (malware, harmful, adware, spyware…) cho nhau một cách trực tiếp mặc dù các VMs này nằm trên cùng một máy chủ vật lý. Điều này có nghĩa rằng, đối với một máy ảo nếu bị nhiễm virus, thì không có nghĩa rằng các máy ảo láng giềng cũng sẽ nhiễm virus; điều mà đối với hệ thống chưa được ảo hóa thường mắc phải. Và cũng có thể khẳng định, việc phân tách tài nguyên của một máy chủ vật lý thành các máy ảo nhỏ hơn như thế, cũng đồng nghĩa phân tách môi trường hoạt động của các mối nguy hại có thể làm hỏng hệ thống.
  * Ngoài ra còn đem lại những tiện ích khác như dễ dàng thử nghiệm các bản “kiểm thử” của hệ điều hành, phần mềm, chương trình trước khi xuất bản chính thức. Đa dạng hoá các dịch vụ, các ứng dụng vì có thể cài đặt nhiều hệ điều hành cũng như nền tảng một cách đơn giản chỉ trên 1 máy vật lý.



### Lợi ích của Container

Như đã phân tích ở trên, Container kế thừa những lợi ích trên của VMs, nhưng không chỉ thế, nó còn khắc phục những hạn chế tồn đọng của VMs về performance. Với VMs do việc ảo hoá từ phần cứng đến cả hệ điều hành, cho nên dễ hiểu rằng nó cần một lượng tài nguyên lớn cho việc ảo hoá đó, ảnh hưởng nhất là việc chiếm nhiều tài nguyên bộ nhớ (memory) cũng như đòi hỏi tốc độ đọc ổ đĩa phải tương đối cao để tránh hiện tượng trễ (delay). **_Nói một cách tóm lược, ảo hoá truyền thống tưởng chừng như giải quyết được vấn đề làm sao sử dụng hiệu quả tài nguyên nhưng chính bản thân nó cũng tiêu tốn một lượng tài nguyên, mặt khác ảo hóa sử dụng Hypervisor (VMs) lại tăng tính sẵn sàng cao (high availability), vượt lỗi (failover), scalablitiy (mở rộng). Việc ra đời của Container được xem như một bước phát triển mới trong công nghệ ảo hóa. Cũng là ảo hoá nhưng lại sử dụng tài nguyên cho việc ảo hoá ít hơn rất nhiều so với “Ảo hoá truyền thống VMs”, nhờ vậy, ảo hóa sử dụng Containers bổ sung (có thể là phát huy) những giới hạn về mặt công nghệ/tính năng mà ảo hóa hypervisor VMs gặp phải. Hay có thể nói rằng, hiệu năng là thế mạnh của Container so với VMs._**

# Những hạn chế của Container và VMs.

### Những hạn chế đối với sử dụng containers:

  * Bảo mật: Một trong những lợi thế của VMs đó là việc trừu tượng hóa phần cứng vật lý và thể hiện phần cứng này thành các phần cứng riêng biệt khác. Và cũng chính vì đặc tính này đã hạn chế được các nguy cơ tấn công phát xuất từ phía tầng trên của hypervisor. Trên lý thuyết, các mối nguy hại đến từ hệ điều hành ở các máy ảo sẽ không thể ảnh hưởng đến các máy ảo khác cho dù các máy ảo này có cùng nằm trên một phần cứng vật lý. Nhưng đối với containers thì không có gì có thể đảm bảo điều này, chính bởi vì các containers sử dụng chung một kernel và sử dụng quyền root, nên khi có bất kì lỗ hổng bảo mật nào xảy ra đối với kernel, thì có nghĩa tất cả các containers đều có khả năng bị ảnh hưởng.
  * Hỗ trợ: hiện tại Docker hiện vẫn chưa hỗ trợ tốt trên Windows và OS X. Trong khi đó VMs có thể cài bất kì hệ điều hành nào trên đó và các hệ điều hành khác nhau hoàn toàn độc lập với nhau.



### Những hạn chế đối với sử dụng VMs:

  * Cần tính toán, hoạch định cụ thể và cẩn thận tài nguyên sẽ cấp cho VMs. Nếu xảy ra thiếu hụt thì sẽ không đám ứng được yêu cầu của Dịch vụ - Server hoặc làm giảm hiệu năng cần thiết dẫn đến giảm hiệu quả công việc; ngược lại dư thừa tài nguyên so với mức cần thiết sẽ gây lãng phí.
  * Ảo hoá VMs đòi hỏi cần một lượng tài nguyên lớn cho chính việc ảo hoá đó (không phải tài nguyên cần thiết cho Service). Mặc dù, những lợi ích cho việc tiêu tốn tài nguyên này là không thể chối bỏ, nhưng cũng không thể phủ nhận lượng tài nguyên này là không nhỏ.
  * Ảo hoá VMs gây tổn thất về hiệu năng của phần cứng vật lý vì cần một phần không nhỏ tài nguyên để xử lý, duy trì các VMs. Hiệu năng tổng của các VMs luôn nhỏ hơn so với hiệu năng thực sự của máy tính thật bất kể giải pháp ảo hoá được cải tiến và cập nhật liên tục.
  * Hiệu năng của ứng dụng cũng như dịch vụ “có thể” giảm khi được ảo hoá (so sánh giữa các VMs ảo có cùng lượng tài nguyên với các máy tính thật chạy ứng dụng hoặc dịch vụ đó cho thấy khi thực hiện ảo hoá gây giảm hiệu năng ít nhiều)
  * Bảo mật có thể xem là ưu điểm nhưng cũng có thể là hạn chế lớn của ảo hoá bằng các VMs. Thông thường các VMs không liên quan gì đến nhau, chúng độc lập như các máy tính thật, khi bị lỗi hoặc nhiễm mã độc thì chúng không ảnh hưởng đến các VMs khác cũng như máy tính vật lý (giả sử các VMs không giao tiếp dữ liệu với nhau). Tuy nhiên nếu lỗi, lỗ hỏng hoặc sự cố nằm ở bản thân giải pháp ảo hoá. Nó có thể gây hỏng theo hiệu ứng Domino hoặc lỗi toàn bộ các VMs vì trên một hệ thống các VMs thường sử dụng chung một giải pháp ảo hoá. Ngoài ra, đối với các hệ thống ảo hóa có thiết lập phương thức High Availability, Fault Tolerance, hay thậm chí là backup, replication thì việc bảo mật này cũng không có quá nhiều ý nghĩa. Giả sử, một VM được HA, FT, backup và replication, sau đó VM này bị nhiễm malware, đồng nghĩa với việc các phiên bản của VM này đều bị nhiễm malware tương tự.


