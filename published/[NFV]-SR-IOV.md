---
title: "[NFV] SR-IOV"
date: 2018-07-25 08:00:42
categories: [NFV]
---

Trong bài viết này, mình sẽ giới thiệu về công nghệ SR-IOV (Single Root - I/O Virtualization). Nhưng trước khi đi vào chủ đề chính, thì mình sẽ giới thiệu sơ qua về các loại I/O Virtualization cái đã.

# Các loại I/O virtualization

Nếu so sánh với các lĩnh vực ảo hóa khác như về ảo hóa CPU, OS, Memory thì đã tương đối ổn định. Nhưng còn về phần ảo hóa I/O thì vẫn còn nhiều điều phải bàn. Nhất là đối với công nghệ NFV vì các card mạng/PCIe devices là nhân tố chính trong việc xử lý, forward các gói tin trên các dòng server phổ thông (general-purpose server). 

Hiện có nhiều hướng để ảo hóa I/O, xin được liệt kê **_3 hướng chính_** sau đây (luộc lại từ [Intel](http://www.intel.com/content/dam/doc/application-note/pci-sig-sr-iov-primer-sr-iov-technology-paper.pdf)): ![](https://cloudcraft.info/wp-content/uploads/2018/07/sr-iov-1.jpg)

  * Software-based Sharing


  *     * Giới thiệu: Kỹ thuật này sẽ giả lập một con switch ảo (emulator) nằm ở VMM (Virtual Machine Manager), làm trung gian chuyển traffic giữa VMs và phần cứng thật bê dưới.
    * Ưu: chia sẻ được thiết bị I/O
    * Khuyết: 
      * I/O overhead: gói tin phải đi qua 2 lần I/O stack, 1 lần ở VM và 1 lần ở VMM
      * CPU overhead tại VMM do cần dùng CPU để xử lý gói tin tại switch ảo, sao đó mới chuyển xuống NIC thật bên dưới
        * Hiệu suất chỉ còn lại **_45~65%_** so với tổng throughput của thiết bị thật (số liệu của Intel, ko chém đâu, link bên dưới).

![](https://cloudcraft.info/wp-content/uploads/2018/07/sr-iov-2.jpg)

  * Direct Assignment


  *     *       * Giới thiệu: Dùng công nghệ PCI-Passthrough (Intel: VT-d, AMD: AMD-Vi) cho phép VM **_xài_** trực tiếp 1 card mạng thật (card này đang được VMM quản lý)
      * Ưu: Giảm I/O overhead, đảm bảo toàn bộ throughput của 1 NIC cho 1 VM
      * Khuyết: Xài hàng thật thì hao tiền thật thôi :v, VM nào cũng đòi 1 port NIC hết thì tiền đâu mà mua :v => Hao tiền + khó scale + ko share hàng giữa các VM dc 
  * **_SR-IOV (nhân vật chính nên sẽ nói kỹ ở phần dưới)_**



# SR-IOV

Kỹ thuật **_SR-IOV (Single Root - I/O Virtualization)_** được PCI-SIG (PCI-Special Interest Group) định nghĩa được ra đời gần đây giúp giải quyết vấn đề này, hỗ trợ việc chia sẻ các card mạng/PCIe device này cho các VM cùng sử dụng (đã mua mấy card 10/100 Gbps rồi thì phải xài vắt hết cho đáng :v) SR-IOV cho phép tạo ra nhiều VF (Virtual Function) trên một thiết bị vật lý. Theo lý thuyết, 1 thiết bị có thể hỗ trợ lên đến _256 VF trên một thiết bị PCIe vật lý._ Một số ví dụ:

  * Một card mạng 4 port có hỗ trợ SR-IOV, mỗi port coi như 1 thiết bị vật lý. _**Theo lý thuyết**_ , ta có thể có tổng cộng 1 * 4 * 256 VF trên card mạng này, tổng là 1024 VF.
  * Tương tự như vậy 1 host bus adapter có 2 port, hỗ trợ SR-IOV thì ta lại có 1 * 2 * 256 = 512 VF.



Dĩ nhiên 2 ví dụ kể trên chỉ là mặt _**lý thuyết**_ vì mỗi VF đều cần có tài nguyên vật lý, nếu nhiều VF quá thì sẽ dẫn đến tình trạng tranh chấp tài nguyên. Vì thế nên giới hạn thật sự chỉ đạt ở mức 64 VF cho đại đa số các thiết bị hiện nay.

Đối với các VM hoặc hypervisor, mỗi card mạng ảo (virtual NIC/VF) đều độc lập với nhau như các thiết bị thật riêng lẻ, thiết bị vật lý bên dưới sẽ không biết đến sự hiện diện của các VF.

SR-IOV cũng có một số giới hạn của mình. Ví dụ như các VF phải cùng dạng/loại với thiết bị vật lý thật bên dưới. Ngoài ra, các VF cũng ko thể được dùng để cấu hình thiết bị vật lý thật bên dưới.

Ngoài ra còn có cơ chế MR-IOV (Multi-Root IOV), cho phép nhiều hệ thống cùng chia sẻ 1 VF. Cái này hơi ngoài lề nên mình sẽ không nói tới trong bài

# Cơ chế

SR-IOV có 2 loại functions:

  * Physical Functions (PFs): là các functions với đầy đủ các chức năng của 1 card mạng/thiết bị PCIe thật và kèm theo các tính năng của SR-IOV, giúp cấu hình và quản lý chức năng của SR-IOV.
  * Virtual Functions (VFs): là các functions “gọn nhẹ” (lightweight) bao gồm những tài nguyên tối cần thiết để di chuyển dữ liệu.

![](https://cloudcraft.info/wp-content/uploads/2018/07/sr-iov-3.jpg)

So sánh giữa VF và PF (Nguồn: Intel)

Với mỗi VF sẽ có 1 vùng dữ liệu/BAR tương ứng (Base Address Register). VMM (Virtual Machine Manager) sẽ mapping giữa vùng nhớ của VF với vùng nhớ mà VMM đưa cho VM.

## Cơ chế mapping của SR-IOV

Những thiết bị hỗ trợ SR-IOV có thể hỗ trợ nhiều VF độc lập với nhau, mỗi VF bao gồm một PCI Configuration space (một vùng nhớ cấu hình riêng). VMM sẽ gán 1 hoặc nhiều VF cho 1 VM. Nhờ có các công nghệ Memory Translation như Intel® VT-x và Intel® VT-d giúp cho phép mapping trực tiếp dữ liệu tới VM mà không cần phải đi qua switch ảo trong VMM. 

![](https://cloudcraft.info/wp-content/uploads/2018/07/sr-iov-4.jpg)

_Cách làm này là kết hợp giữa cách 1 (software emulator) và cách 2 (DMA) = > Ta có được SR-IOV :D_

## Đường đi của gói tin (giữa thật và ảo)

Sau đây là một dí vụ về cách thức gói tin Ethernet đi với VM thông qua một VF nằm trên card mạng của Intel. (Xin phép để nguyên ko dịch cho trọn nghĩa….ko phải vì mình lười đâu nhé:3)

  1. The Ethernet packet arrives at the Intel® Ethernet NIC.
  2. The packet is sent to the Layer 2 sorter/switch/classifier.
     1. This Layer 2 sorter is configured by the Master Driver. When either the MD or the VF Driver configure a MAC address or VLAN, this Layer 2 sorter is configured.
  3. After being sorted by the Layer 2 Switch, the packet is placed into a receive queue dedicated to the target VF.
  4. The DMA operation is initiated. The target memory address for the DMA operation is defined within the descriptors in the VF, which have been configured by the VF driver within the VM.
  5. The DMA Operation has reached the chipset. Intel® VT-d, which has been configured by the VMM then remaps the target DMA address from a virtual host address to a physical host address. The DMA operation is completed; the Ethernet packet is now in the memory space of the VM.
  6. The Intel® Ethernet NIC fires an interrupt, indicating a packet has arrived. This interrupt is handled by the VMM.
  7. The VMM fires a virtual interrupt to the VM, so that it is informed that the packet has arrived.

**Các ưu điểm của SR-IOV**

  * Số lượng ngắt (interrupt) tham gia trong qúa trình truyền dữ liệu ít đi.
  * Cho phép network traffice bypass qua lớp software của hypervisor, luồng dữ liệu sẽ đi thẳng từ VF lên VM mà ko cần qua hypervisor => Giảm overhead/
  * DMA (Direct Memory Access) tới không gian bộ nhớ của máy ảo. Giảm I/O overhead.
  * Tốc độ gần như 1 card mạng thật mà không phải “cúng” riêng 1 con NIC vật lý cho 1 máy ảo.



## Yêu cầu hệ thống

Mấy card/switch PCIe đời mới mới có tính năng này. Mấy card đời cũ nào mà ko có tính năng này thì VMM sẽ **_ko bật_** 2 tính năng Direct Assignment hoặc SR-IOV.

# Tham khảo

**What is SR-IOV?** [http://blog.scottlowe.org/2009/12/02/what-is-sr-iov/](http://blog.scottlowe.org/2009/12/02/what-is-sr-iov/) **PCI-SIG SR-IOV Primer: An Introduction to SR-IOV Technology (tài liệu chính, còn nhiều khái niệm khác như Master Driver, ACS...nhưng khi đi chuyên sâu vào sẽ bàn tiếp :3)** [http://www.intel.com/content/dam/doc/application-note/pci-sig-sr-iov-primer-sr-iov-technology-paper.pdf](http://www.intel.com/content/dam/doc/application-note/pci-sig-sr-iov-primer-sr-iov-technology-paper.pdf) **Wikipedia** [https://en.wikipedia.org/wiki/Single-root_input/output_virtualization](https://en.wikipedia.org/wiki/Single-root_input/output_virtualization) **Các kỹ thuật về ảo hóa trong cloud** [https://vietstack.wordpress.com/2015/06/01/486/](https://vietstack.wordpress.com/2015/06/01/486/) **The evolution of IO Virtualization and DPDK-OVS implementation in Linux** [https://vietstack.wordpress.com/2016/01/24/the-evolution-of-io-virtualization-and-dpdk-ovs-implementation-in-linux/](https://vietstack.wordpress.com/2016/01/24/the-evolution-of-io-virtualization-and-dpdk-ovs-implementation-in-linux/)
