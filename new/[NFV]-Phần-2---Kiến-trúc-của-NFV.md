---
title: "[NFV] Phần 2 - Kiến trúc của NFV"
date: 2018-07-11 14:11:57
categories: [NFV, Cloud Computing, Virtualization]
---

# Kiến trúc NFV

## Tổng quan kiến trúc NFV

Để tìm hiểu về kiến trúc của một hệ thống NFV, ta cần phải tiếp cận theo mức high-level do ETSI đề ra và được số đông cộng đồng phát triển chấp nhận và tuân theo. ![](https://cloudcraft.info/wp-content/uploads/2018/07/nfv-architecture-1.png)

_Kiến trúc tham chiếu của NFV - Theo ETSI_

Theo ETSI, một nền tảng NFV sẽ gồm có ba khối chính là:

  * Các hàm chức năng mạng đã được ảo hóa (Virtualised Network Function - VNF): là các phần mềm đảm nhiệm các chức năng mạng (Network Function) như switching, routing, load balancing,... đã được ảo hóa. Điểm khác biệt cơ bản của VNF so với các thiết bị mạng vật lý truyền thống (Physical Network Function - PNF): VNF chính là phần mềm và không cần yêu cầu phần cứng chuyên dụng bên dưới. VNF chạy trên hạ tầng mạng được ảo hóa (NFVI), được quản lý bởi khối điều phối và quản lý (MANO) cũng như hệ thống quản lý các thực thể (Element Management System - EMS) bên trong các VNF.
  * Khối hạ tầng ảo hóa chức năng mạng (Network Functions Virtualisation Infrastructure - NFVI): là tổng thể các thành phần (cả phần cứng lẫn phần mềm) cung cấp tài nguyên cần thiết cho các VNF hoạt động. Tầng này bao gồm các thành phần phần cứng phổ thông COTS (Commercial-Off-The-Shelf Hardware) và một lớp phần mềm ảo hóa abstract giữa VNF và tài nguyên phần cứng. NFVI sẽ thông qua lớp ảo hóa để cung cấp tài nguyên lên cho các VNF bên trên. NFVI được quản lý bởi khối MANO và có thể chạy trên nhiều node (high-volume server, switch, storage vật lý) cũng như nhiều vị trí địa lý khác nhau tùy theo kịch bản riêng của từng dịch vụ. NFVI bao gồm hai khối con là :
    * Hardware Resource: tài nguyên tính toán, lưu trữ và mạng vật lý.
    * Virtualisation Layer: lớp ảo hóa tạo ra các tài nguyên tính toán, lưu trữ và kết nối mạng ảo.


  * Khối điều phối và quản lý (NFV Manage and Orchestrate - NFV M&O) hay thường gọi tắt là MANO: đảm nhiệm việc điều phối và quản lý vòng đời của các tài nguyên vật lý, quản lý các phần mềm hỗ trợ ảo hóa, quản lý vòng đời của các VNF. NFV MANO có thể tương tác với nhiều hệ thống NFVI khác nhau do các interface giao tiếp đã được ETSI thống nhất. Điều này giúp tăng tính linh hoạt cho giải pháp NFV. Các nhà phát triển hệ thống NFV giờ đây không cần phải tập trung xây dựng một giải pháp NFV đầy đủ bao gồm cả khối NFVI, MANO và các VNF mà chỉ cần tập trung vào một thành phần. Trong khối MANO, ta có các khối con:
    * NFV Orchestrator: Quản lý dịch vụ mạng (Network Services) hay có thể hiểu là quản lý chức năng của VNF và các gói VNF, quản lý vòng đời của dịch vụ mạng, tài nguyên toàn hệ thống, chứng thực, cấp quyền sử dụng tài nguyên cho NFVI (Network Functions Virtualization Infrastructure).
    * VNF Manager: Quản lý vòng đời của các thực thể VNF (VNF Instances) hay có thể hiểu là quản lý cho từng VNF, cũng như điều phối, tùy chỉnh cấu hình, cung cấp thông tin liên lạc giữa NFVI và E/NMS. 
    * Virtualized Infrastructure Manager (VIM): Quản lý và điều phối các tài nguyên về compute, storage và network của NFVI hay có thể hiểu là quản lý NFVI.

Ngoài ra, theo mô hình, ta còn có các thành phần khác như

  * OSS/BSS: Operation/Bussiness Support System là hệ thống quản lý việc vận hành hệ thống, tương tác với người vận hành, khách hàng.
  * Service, VNF & Infrastructure Description: chính là các tập tin đặc tả, template để khởi tạo các dịch vụ mạng, các VNF hay kết nối với các hạ tầng ảo hóa một cách nhanh chóng. Tuy có thể tách biệt nhưng thành phần này thường được các nhà phát triển khối MANO bao gồm cả vào trong sản phẩm của mình. Khi được lưu trữ trong hệ thống, các tập tin này thường được lưu lại dưới dạng catalog bao gồm nhiều các đối tượng cùng loại.

Sau đây, ta sẽ lần lượt đi vào chi tiết của ba khối chính ở trên.

## VNF

### Tổng quan

Virtualised Network Function (VNF) là một trong ba thành phần cơ bản trong kiến trúc NFV. Khác với các hàm mạng vật lý (Physical Network Function - PNF) truyền thống vốn đòi hỏi phần cứng riêng biệt, một VNF là một hàm đảm trách chức năng mạng (Network Function) được triển khai trên môi trường ảo hóa. Điều này giúp việc triển khai, quản lý và điều phối các VNF trở nên linh hoạt và dễ dàng hơn.  Giống như các PNF, các VNF sẽ đảm trách một chức năng mạng cụ thể nào đó như: routing, switching, firewall... Nhưng dù là ảo hóa, các VNF vẫn phải tuân thủ các chuẩn thiết kết chung của các tổ chức như 3GPP hay IETF,.. Vậy nên, dù mỗi nhà phát triển sẽ có những công thức riêng cho mình nhưng các VNF dù cùng hay khác nhà phát triển cũng vẫn sẽ tương tác được với nhau và thậm chí là với các thiết bị PNF thông qua các Interface tiêu chuẩn chung để có thể tạo thành một chuỗi các hàm chức năng mạng (VNF Forwarding Graph).  Một ví dụ cơ bản về VNF:

  * Customer Premies Equipement (CPE) là một thiết bị cho phép người dùng/doanh nghiệp kết nối tới mạng Internet của nhà cung cấp dịch vụ. Phổ biến trên thị trường là các loại router/modem của nhà mạng như FPT, VNPT, Viettel. Theo như cách tiếp cận truyền thống, đây sẽ là một thiết bị phần cứng được cài đặt sẵn các chức năng như routing, switching, PPoE, QoS… và thường thì số lượng tính năng này sẽ bị giới hạn bởi thiết bị vật lý.
  * Tuy nhiên, với cách tiếp cận của công nghệ NFV thì thiết bị này sẽ được ảo hóa thành 1 thiết bị ảo (vCPE). Một vCPE có thể chứa nhiều VNF con trong đó, mỗi VNF sẽ đảm nhiệm một nhiệm vụ riêng như DHCP, DNS… Các VNF nhỏ này sẽ xâu chuỗi lại thành 1 dịch vụ hoàn chỉnh và nằm trong 1 VNF lớn là vCPE. Nhờ cách tiếp cận theo dạng module này, việc nâng cấp phần mềm, thêm bớt dịch vụ, quản lý các CPE trở nên đơn giản hơn rất nhiều.

Trong kiến trúc của hệ thống NFV, các VNF chạy trong các máy ảo (VM hay Deployment Unit) được tạo ra trên hạ tầng NFVI và được điều khiển bởi khối quản lí và điều phối MANO. Bên trong mỗi VNF là các hệ thống quản lý thực thể (Element Management System - EMS). EMS sẽ thu thập các thông tin của VNF và truyền về cho khối MANO cũng như nhận lệnh từ MANO để thực hiện các tác vụ quản lý trên VNF. ![](https://cloudcraft.info/wp-content/uploads/2018/07/nfv-architecture-2.png)

_Mối liên hệ giữa NS, VNF và VM_

Mỗi một VNF sẽ có những thông tin cấu hình cũng như cách thức hoạt động, chức năng cụ thể. Các thông tin này của từng VNF sẽ được mô tả trong các tập tin gọi là Virtualized Network Function Descriptor (VNFD). VNFD bao gồm các mô tả về cấu hình của một VNF như: số vcpu, memory, số port, thông tin về các kết nối giữa các thành phần trong nội bộ VNF với nhau,... Khi khởi tạo các VNF, khối MANO sẽ dựa trên những tập tin này để yêu cầu NFVI cung cấp tài nguyên cho hợp lí. Tuy nhiên, việc lựa chọn tài nguyên này đôi khi còn phụ thuộc vào nhiều quy định, yêu cầu khác chứ không nhất thiết phải hoàn toàn theo VNFD (Ví dụ như khả năng đáp ứng của hạ tầng lúc đó, các chính sách bảo mật ở mức người dùng,...). Các VNFD có thể được lưu trữ trong VNF Catalog Repository của khối MANO. Chi tiết về các VNFD và VNF Catalog sẽ trình bày ở phần MANO. Một cách tổng quan, quá trình phát triển một VNF sẽ bao gồm các bước sau:

  1. Định nghĩa tập tin VNFD theo yêu cầu người dùng.
  2. Định nghĩa các đoạn script quản lý vòng đời của VNF. Các đoạn script này sẽ tạo ra các sự kiện trong vòng đời của một VNF như: khởi tạo máy ảo (Instantiate), cấu hình các thông tin ban đầu (Configure), khởi chạy dịch vụ (Start), hủy dịch vụ (Terminate), mở rộng dịch vụ (Scale Out),... Các đoạn mã này sẽ do VNF Manager quản lý và được gửi đến EMS khi cần được thực thi.
  3. Phát triển các tập tin image dùng để cài đặt hệ điều hành cho các máy ảo sẽ chạy các VNF. 
  4. Tải VNFD lên kho chứa VNF Catalogue. Khi được tải lên, hệ thống sẽ kiểm tra thử tính tương thích của VNFD đó với các thông tin tài nguyên khả dụng của hệ thống. Nếu khả thi, hệ thống mới chấp nhận lưu lại VNFD đó.
  5. Triển khai thử dịch vụ. Hệ thống sẽ cấp phát tài nguyên và cài đặt VNF. 
  6. Chạy thử dịch vụ. Nếu cài đặt thành công, hệ thống mới chạy thử nghiệm dịch vụ từ đó có thể đánh giá nghiệm thu VNF.



### Kiến trúc của VNF

Mỗi VNF là một phần mềm được cấu thành bởi một hoặc nhiều thành phần gọi là VNF Components (VNFC). Các VNFC sẽ được cài đặt trên các máy ảo (Virtual Machine) trên hạ tầng ảo hóa do khối NFVI cung cấp. Việc cấu thành một VNF từ một bộ các VNFC như thế nào phụ thuộc vào nhiều yếu tố như: thứ tự ưu tiên về hiệu năng, khả năng mở rộng, độ tin cậy, bảo mật, các mục tiêu phi chức năng khác,... ![](https://cloudcraft.info/wp-content/uploads/2018/07/nfv-architecture-3.png) Một cách tổng quan, mỗi VNF có các interface để giao tiếp với các khối khác trong kiến trúc NFV như sau:

  * SWA-1: giao tiếp với các VNF hay PNF khác.
  * SWA-2: giao tiếp giữa các VNFC trong cùng VNF với nhau.
  * SWA-3: giao tiếp với VNF Manager trong khối MANO.
  * SWA-4: giao tiếp với EM (Element Management) riêng của VNF đó.
  * SWA-5: giao tiếp với khối NFVI bên dưới.

Quá trình giao tiếp cụ thể qua các interface này đều được các tổ chức qui chuẩn như ETSI, 3GPP định nghĩa sẵn. Luận văn này xin không trình bày quá sâu vào chi tiết của các tiêu chuẩn này.

## NFVI

NFVI là tập hợp các phần cứng và phần mềm dùng để khởi tạo môi trường cho các VNF hoạt động bên trên. Về phần cứng, NFVI bao gồm các tài nguyên tính toán, lưu trữ, các thiết bị định tuyến, chuyển mạch mạng. Về phần mềm bao gồm lớp ảo hóa hypervisor, các trình điều khiển driver tương tác với các thiết bị vật lý, các trình điều khiển thiết bị mạng (OpenFlow, firmware). ![](https://cloudcraft.info/wp-content/uploads/2018/07/nfv-architecture-4-1.png)

_Các domain của NFVI_

Do đây là một kiến trúc mở đã được định nghĩa và tiêu chuẩn hóa bởi ETSI nên ta có thể lựa chọn nhiều công nghệ khác nhau để đảm trách vai trò NFVI mà không phụ thuộc vào bất kỳ một hãng nào cả. Một mô hình triển khai NFV có thể gồm nhiều công nghệ NFVI nhưng vẫn tương tác được với nhau. Một số giải pháp NFVI phổ biến là OpenStack, CloudStack,.. ETSI tiếp tục chia NFVI thành 3 miền (domain) chính là:

  * Miền tính toán (Compute Domain)
  * Miền nhân ảo hóa (Hypervisor Domain)
  * Miền hạ tầng mạng (Infrastructure Network Domain)



### Miền tính toán (Compute Domain)

Miền tính toán là các thành phần tài nguyên phần cứng tính toán và lưu trữ vật lý bên dưới. Miền nhân ảo hóa ở trên sẽ dựa trên miền tính toán để tạo ra môi trường cho các VNF hoạt động. Ở đây, ta cần phân biệt giữa miền tính toán và miền hạ tầng mạng. Miền tính toán chỉ bao gồm phần cứng tính toán, phần cứng lưu trữ và các interface nhập/xuất (I/O interface) trên các thiết bị, tức là bao hàm các máy chủ tính toán và hệ thống lưu trữ. Cụ thể là:

  * Vi xử lý tính toán & các thành phần tối ưu hiệu năng (Processor & Accelerator)
    * Vi xử lý có thể là các dòng chip phổ thông như ARM và Intel x86.
    * Các công nghệ, các thuật toán nén, mã hóa tối ưu hơn để phục vụ bảo mật và tăng cường khả năng xử lý gói tin.
  * Network Interfaces
    * Chính là các card mạng (Network Interface Card - NIC) được nối với vi xử lý qua các cổng PCIe. 
    * Các công nghệ tối ưu khả năng xử lý nhập/xuất dữ liệu như SR-IOV và DPDK.
  * Lưu trữ
    * Có thể là ổ cứng HDD truyền thống hoặc ổ cứng thể rắn SSD tiên tiến hơn.



### Miền ảo hóa (Hypervisor Domain)

![](https://cloudcraft.info/wp-content/uploads/2018/07/nfv-architecture-5.png)

_Các thành phần của Hypervisor Domain (Nguồn: Verizon)_

Miền ảo hóa (Hypervisor Domain) là một trong 3 miền chính của NFVI. Nhiệm vụ của miền ảo hóa là cung cấp môi trường thực thi cho các VNF. Để thực hiện việc đó, miền ảo hóa sẽ tạo ra một lớp tài nguyên (tính toán, lưu trữ) ảo hóa, phân tách giữa phần cứng bên dưới và các ứng dụng bên trên. Khi nhận lệnh từ khối điều phối và quản lý, các máy ảo (Virtual Machine - VM) sẽ được tạo ra để chạy các VNF. Đồng thời, thông qua các API/Interface của miền này, người quản trị có thể điều khiển được các máy ảo đã tạo. Trong một số trường hợp cần tăng tốc độ cũng như đảm bảo băng thông cho VNF thì lớp ảo hóa có thể cho phép các VM kết nối trực tiếp tới phần cứng bên dưới thông qua các kỹ thuật như: CPU pinning, PCI Passthrough,... Các kỹ thuật này sẽ được trình bày kĩ hơn ở mục dưới. Về mặt quản lý tài nguyên tính toán, đại diện cho miền này trên nền tảng Linux là bộ đôi _**KVM/Libvirt**_. Ngoài ra còn có thể kể đến các giải pháp khác như: _**Xen, VMWare, Hyper-V**_. Còn về phần quản lý tài nguyên lưu trữ thì có các giải pháp như _**LVM, Ceph**_ trên Linux...

### Miền hạ tầng mạng (Infrastructure Network Domain)

Miền hạ tầng mạng (Infrastructure Network Domain) có nhiệm vụ quản lý các tài nguyên mạng hỗ trợ chuyển mạch và định tuyến của hệ thống như: Top of Rack Switch, router, cáp kết nối giữa các tài nguyên tính toán và lưu trữ khác trong NFVI. Từ đó, miền này cung cấp hạ tầng mạng ảo cho các VNF hoạt động và tương tác với nhau. Cụ thể, miền hạ tầng mạng sẽ: 

  * Tạo ra các mạng ảo để các VNF liên lạc với nhau.
  * Cung cấp không gian địa chỉ và quản lý địa chỉ trong các mạng ảo.
  * Cho phép phân tách luồng traffic độc lập giữa các mạng ảo.

Theo hãng công nghệ Verizon, miền này còn có thể được chia thành hai miền con bao gồm:

  *     * **NFVI-PoP Network:** Mạng này dùng để liên giữa các tài nguyên tính toán và lưu trữ trong cùng một hạ tầng NFVI. Nó cũng bao gồm các thiết bị chuyển mạch và định tuyến cho phép kết nối từ bên trong hạ tầng NFVI cục bộ ra mạng bên ngoài.
    * **Transport Network: Mạng này dùng để liên lạc giữa các hạ tầng NFVI khác biệt về địa lý hoặc để liên lạc tới các thiết bị mạng, thiết bị đầu cuối nằm ngoài các hạ tầng NFVI của hệ thống.**

Đại diện cho phần này là các thiết bị mạng vật lý cũng như các phần mềm ảo hóa mạng như _**linux bridge, openvswitch...**_

## NFV MANO

### Tổng quan

Môi trường NFV trong thực tế là một môi trường đặc biệt bao gồm rất nhiều các thành phần phức tạp liên kết với nhau. Từ các hệ thống ảo hóa chạy bên dưới (VMware vSphere, KVM), các thiết bị mạng vật lý, cho đến các máy ảo chứa các VNF ở bên trên và cả những liên kết giữa chúng. Tất cả các thành phần này sẽ liên kết lại với nhau nhằm tạo ra sản phẩm cuối là dịch vụ mạng (Network Service) cho người dùng. Bài toán được đặt ra là làm thế nào để quản lý tất cả những thành phần này một cách tập trung và thống nhất. Hệ thống quản lý này cần có khả năng ổn định và tự động hóa cao, giảm bớt sự can thiệp của con người. Kiến trúc NFV MANO chính là lời giải cho bài toán bên trên với khả năng quản lý tập trung, tương thích được với nhiều loại phần cứng lẫn phần mềm và quan trọng nhất là khả năng điều phối chặt chẽ giữa các thành phần trong một hệ thống NFV. Chức năng chính của NFV MANO là quản lý NFVI và vòng đời của các VNF. Công việc cụ thể của NFV MANO như sau:

  * Cấp phát và thu hồi tài nguyên của NFVI (tài nguyên xử lý, bộ nhớ,lưu trữ, kết nối…)
  * Quản lý việc kết nối giữa các VM và VNF.
  * Khởi tạo, mở rộng, phục hồi, nâng cấp hoặc xóa các VNF
  * Theo dõi hiệu năng và các vấn đề khác liên quan đến NFVI

Một số giải pháp MANO tuân theo các quy chuẩn của ETSI là: 

  * Tacker: Một project thuộc OpenStack
  * OpenSourceMANO: được phát triển bởi chính ETSI
  * OpenBaton: đại học Fraunhofer FOKUS (Đức)
  * Cloudidy: công ty GigaSpaces



### Kiến trúc của MANO

![](https://cloudcraft.info/wp-content/uploads/2018/07/nfv-architecture-6.png)

_Sơ đồ khối MANO và các interfaces_

Về mặt kiến trúc, MANO gồm 3 khối con chính là NFVO, VNFM và VIM. Bên cạnh đó còn có các khối lưu trữ dữ liệu phục vụ cho các khối chính.

#### NFV Orchestrator

Chức năng cụ thể của NFV Orchestrator (NFVO) bao gồm khởi tạo, chỉnh sửa các Network Services (NS), VNF-FG và các gói VNF Packages. Quản lý tài nguyên toàn cục, chứng thực và cấp quyền khởi tạo tài nguyên của NFVI. 

#### VNF Manager

Quản lý vòng đời của các thực thể VNF (VNF instances). Cụ thể, VNF Manager sẽ điều phối, tùy chỉnh cấu hình, cung cấp thông tin liên lạc giữa NFVO, VIM và EMS. Các tác vụ của một VNF Manager có thể là:

  1. Quản lý vòng đời của VNF ( khởi tạo/hủy, bật/tắt, thay đổi thông tin cấu hình, nâng cấp phần mềm, phục hồi khi có sự cố).
  2. Mở rộng (scale up) hay thu hẹp (scale down ) VNF khi cần.
  3. Thu thập, giám sát các thông tin về hiệu suất hoạt động, các thông báo lỗi (nếu có).
  4. Làm cầu nối giữa trình quản lý thực thể (EMS) bên trong các VM (đang chạy VNF) và NFVO cũng như VIM.

Việc xây dựng VNF Manager như thế nào hoàn toàn phụ thuộc vào các nhà phát triển. Các nhà phát triển có hai lựa chọn là:

  * Xây dựng một VNF Manager phổ thông (Generic VNF Manger) dùng chung cho mọi loại VNF. 
  * Xây dựng VNF Manager đặc biệt cho một số VNF nhất định, ví dụ như VoLTE (voice over LTE).

Thông thường, các VNF Manager đặc biệt được cung cấp bởi chính các nhà phát triển VNF đặc biệt đó. Còn lại đa phần, chiến lược phổ biến vẫn là tạo nên một trình quản lý VNF “phổ thông” (Generic VNF Manager) sử dụng được cho nhiều loại VNF khác nhau. Chiến lược này đòi hỏi cần có một hệ thống quản lý thực thể “phổ thông” (Generic EMS) tương ứng được cài đặt bên trong từng máy ảo (được dùng để chạy VNF.) 

#### Virtualized Infrastructure Manager (VIM)

Nhiệm vụ của VIM là quản lý và điều phối các tài nguyên về compute, storage và network của NFVI. Các chức năng chính của VIM bao gồm:

  * Quản lý việc phân phối, nâng cấp, thu hồi tài nguyên của NFVI và mối liên hệ giữa tài nguyên (đã được ảo hóa) và tài nguyên vật lý thật bên dưới (compute, storage, network).
  * Hỗ trợ việc quản lý các VNF Forwarding Graphs bằng cách tạo các virtual link, virtual network, subnet, port mạng cũng như security policy nhằm quản lý lượng traffic dễ dàng hơn.
  * Quản lý các thông tin liên quan đến phần cứng và phần mềm của NFVI.
  * Quản lý dung lượng các tài nguyên ảo hóa và chuyển tiếp các thông tin về vệc sử dụng tài nguyên của NFVI.
  * Quản lý các software image cần dùng cho các ứng dụng khác của MANO (ví dụ như dự án glance của OpenStack).
  * Thu thập các thông tin về hiệu năng và lỗi của phần cứng, phần mềm và tài nguyên ảo hóa.
  * Quản lý danh mục các tài nguyên ảo hóa để cung cấp cho NFVI.

Hiện tại thì OpenStack là VIM lý tưởng nhất, do đã có quá trình phát triển lâu dài. Ngoài ra, trên thị trường thương mại, ta không thể không nhắc tới VMware vSphere, hiện đang được rất nhiều dự án về MANO hỗ trợ.   (còn típ...)
