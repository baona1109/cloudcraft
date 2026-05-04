---
title: "Một lần đi lên mây - Dạo một vòng AWS Networking"
date: 2017-12-27 16:03:55
categories: [Cloud Computing, AWS]
---

Điều đầu tiên khi tiếp cận AWS không phải là máy ảo, cũng không phải là dịch vụ storage, mà chính là network trên AWS. Bằng một lần tôi nằm mơ về AWS, tôi đã được Jeff dắt đi dạo một vòng trên mây. Chúng ta bắt đầu từ cánh cổng AWS Networking hay còn có cái tên mà chủ nhà AWS đặt cho nó là VPC Networking. Trên cánh cổng được khắc trổ đầy đủ các thành phần thật đẹp và chi tiết, như: 

  * Network Interface
  * Route Tables
  * Internet Gateways
  * Egress-only Internet Gateway
  * DHCP Option Sets
  * DNS
  * Elastic IP Address
  * VPC Endpoints
  * NAT
  * VPC Peering
  * ClassicLink

Trông tất cả mọi thứ như một bức tranh "Sơn Hà Xã Tắc", Jeff đã mở cánh cổng, chậm rãi dắt tôi đi vào khuôn viên của AWS và bắt đầu giới thiệu một cách say sưa. Ngay tại tiền hoa viên của gian thứ nhất, tôi thấy một bàn cờ bằng ngọc, không sai đâu đó là bàn cờ mà ở đó VPC được khắc họa thật tinh sảo. Cùng tôi tìm lại trí nhớ về bàn cờ VPC đó nhé! 

### AWS Virtual Private Cloud - Bàn cờ "ngũ" căn

Ngay từ cái tên, tôi đã bắt gặp anh chàng "ấn tượng" thật điển trai. Đây có thể gọi là cloud ảo trên nền cloud AWS, thật thú vị là trong cloud lại có cloud. VPC thuộc về riêng một tài khoản AWS, và không lẫn lộn giữa các tài khoản. Còn private, Jeff đã cẩn thận để nói rằng, cloud luôn đề cao tính private cho người sử dụng. Chúng ta vừa được đặt để vào một môi trường vừa tách biệt, vừa riêng tư. Ngũ "căn" trong bàn cờ AWS VPC được bày ra như sau: 

  * IP Address Range
  * Subnets
  * Route Tables
  * Network Gateways
  * Security Settings.

Sau khi hỏi tôi đã nhớ được các "căn" trên bàn cờ hay chưa, Jeff tiếp tục dẫn tôi đến ngự hoa viên, nơi mà Jeff có thể thoải mái mời tôi tách trà và dạy cho tôi cách thưởng "hoa". Vừa vào đến ngự hoa viên, quả là một khung cảnh kỳ vĩ, với nhiều hoa, nhiều chủng loại, với tách trà trên tay, tôi cũng chỉ "cưỡi ngựa xem hoa" vì cũng chẳng có nhiều thời gian cho giấc mơ. ![](https://cloudcraft.info/wp-content/uploads/2017/12/mot-lan-di-len-may-dao-mot-vong-aws-networking-1.png)

#### IP Address Range & Subnet - Mảnh đất hoa viên và chậu hoa

Trên mỗi VPC, sẽ có nhiều address range được định nghĩa, và sẽ tồn tại xuyên suốt hệ thống của chúng ta. Address range sẽ định nghĩa nên dãy IP rộng thường sẽ là /16 hoặc /8. Trên mỗi Address Range lại được chia ra thành các khu vực khác nhau, gọi là subnet, mỗi subnet được chia một khoảng không gian IP trong Address range. Cũng giống như mảnh đất hoa viên được chia ra thành các khu vực, mỗi khu vực là một loại cây, loại hoa khác nhau, cách bố trí chậu hoa cũng khác. Có đến 2 loại subnet: 

  * Private subnet: dành cho các resource chỉ giao tiếp trong nội bộ VPC.
  * Public subnet: dành cho các resource giao tiếp ra ngoài Internet.



#### Route Table - Bảng chỉ dẫn trong hoa viên rộng lớn

Mỗi subnet sẽ có một bảng định tuyến riêng biệt, tại một thời điểm subnet chỉ có 1 bảng định tuyến, nhưng ngược lại, bảng định tuyến có thể được áp dụng cho nhiều subnet. Tôi đã bước vào hoa viên và tự tin rằng mình sẽ không bị lạc, nhưng tôi đã sai, với phong cảnh và độ rộng lớn của hoa viên AWS, tôi không thể không để ý đến bảng chỉ dẫn. Cũng giống như vậy, các traffic trên AWS cần được định tuyến đúng, và thậm chí là có thể định tuyến theo ý của người dùng, để điều lượng traffic đi từ subnet đi đến nới mà chúng ta mong muốn như router, internet gateway, hoặc một virtual appliance định sẵn. 

#### Security settings - Những người bảo vệ trong hoa viên

Jeff thật giàu có, và cũng thật yêu cây cỏ. Ông đã thuê mướn hẳn một đội bảo vệ để canh giữ cho hoa viên của mình để không bị những tên hacker trộm lẻ hay những kẻ hái hoa bắt bướm có thể lọt vào nơi mà ông không mong muốn, hoặc quấy rầy những vị khách của ông mời đến giống như tôi. Để bảo vệ các resources trong subnet: 

  * Sử dụng security groups.
  * Sử dụng Network access control list (ACL).

Cả 2 cách này đều làm chung một nhiệm vụ đó chính là "chặn" và "cho phép" như bình thường chúng ta vẫn hay làm trên các hệ thống khác. Việc chặn này áp dụng đến layer 4, có nghĩa rằng nó có thể chặn ở mức IP, Port, TCP/UDP. 

#### VPC Endpoint - Cửa vào kho vật liệu trang trí hoa viên

VPC Endpoint đóng vai trò là cầu nối, và là kết nối riêng biệt để các instance trong VPC có thể giao tiếp với các dịch vụ khác trên AWS mà không cần đi qua Internet, NAT, VPN, hay AWS Direct Connect. VPC Endpoint sẽ có một ID định danh để có thể điều hướng traffic từ VPC đến service. Tất cả các traffic đi từ VPC đến AWS Service đều không đi ra khỏi AWS network. ![](https://cloudcraft.info/wp-content/uploads/2017/12/mot-lan-di-len-may-dao-mot-vong-aws-networking-1.png)

#### Network Interfaces

Hay còn có tên gọi đầy đủ là Elastic Network Interface --> interfaces ảo. Bao gồm các thành phần: 

  * Primary private IPv4
  * 1 hoặc nhiều Secondary private IPv4
  * 1 Elastic IP trên mỗi private IPv4 (cả primary & secondary)
  * 1 Public IPv4
  * 1 hoặc nhiều IPv6
  * Được gán với 1 hoặc nhiều security groups khác nhau
  * 1 MAC address
  * 1 cờ đánh dấu là source hoặc destination.
  * Phần mô tả mở rộng về interface.



#### Internet Gateway - Cửa hàng bán hoa của Jeff

Thành phần ảo được AWS tạo ra để kiểm soát truy cập ra Internet Bao gồm 2 chức năng chính: 

  * Gateway đi ra Internet được trai kháo trong routing table của subnet.
  * Đóng vai trò là NAT để mapping private IPv4 sang public IPv4.

![](https://cloudcraft.info/wp-content/uploads/2017/12/mot-lan-di-len-may-dao-mot-vong-aws-networking-2.png)

#### Egress-only Internet Gateways

Chỉ sử dụng với IPv6. Egress-only có nghĩa rằng chỉ có instance bên trong VPC mới có thể khởi tạo kết nối ra bên ngoài Internet, và ngăn chặn bên ngoài Internet khởi tạo kết nối vào vên trong. Traffic đi từ instance bên trong VPC sẽ được route table forward đến Egress-only Internet GW, và response ngược trở lại instances. Có nghĩa rằng, phiên giao tiếp giữa trong và ngoài chỉ được tạo ra khi và chỉ khi instance bên trong khởi tạo. _Lưu ý:_

  * _Egress-only Internet GW không được gắn với bất kì resource group nào._
  * _Có thể gắn resource group cho instance trong private subnet để kiểm soát traffic ra vào._
  * _Có thể sử dụng ACL để kiểm soát traffic toàn subnet._



#### Network Address Translation (NAT)

Chỉ sử dụng với IPv4. Với IPv6 sẽ sử dụng egress-only Internet Gateway. Có 2 loại NAT devices: 

  * NAT gateway 
    * Hỗ trợ lên đến 10Gbps. Nếu có nhu cầu sử dụng nhiều hơn 10Gbps thì phân tải ra nhiều subnet, mỗi subnet tạo 1 NAT gateway.
    * Chỉ có đúng 1 elastic IP được gán với NAT gateway. Và không thể tháo elastic IP đó ra khỏi NAT sau khi NAT gateway được tạo, muốn sử dụng elastic IP khác thì phải tạo NAT gateway mới, chỉnh lại routing table và delete NAT gateway cũ.
    * Các protocol hỗ trợ: TCP, UDP, ICMP
    * Không thể gán security group cho NAT gateway. Thay vào đó sử dụng security group & ACL để control traffic.
    * Khi NAT gateway được tạo ra thì mặc định NAT gateway được gán 1 private IP.
    * Tham khảo: 
      * ![](https://cloudcraft.info/wp-content/uploads/2017/12/mot-lan-di-len-may-dao-mot-vong-aws-networking-3.png) Theo như trên hình, các servers ở private subnet chuyển traffic đi ra internet đi qua NAT gateway.
      * NAT gateway forward traffic từ private ra internet thông qua Elastic IP, thay thế source IP của packet bằng Elastic IP.
  * NAT instance 
    * Tương tự NAT Gateway

AWS recommend sử dụng gateway vì bandwidth và độ sẵn sàng cao hơn. Nhưng NAT instance thì có thể sử dụng trong một số trường hợp đặc biệt. Bảng so sánh NAT gateway & NAT instance: 

Attribute | NAT gateway | NAT instance  
---|---|---  
Availability | Highly available. NAT gateways in each Availability Zone are implemented with redundancy. Create a NAT gateway in each Availability Zone to ensure zone-independent architecture. | Use a script to manage failover between instances.  
Bandwidth | Supports bursts of up to 10Gbps. | Depends on the bandwidth of the instance type.  
Maintenance | Managed by AWS.You do not need to perform any maintenance. | Managed by you, for example, by installing software updates or operating system patches on the instance.  
Performance | Software is optimized for handling NAT traffic. | A generic Amazon Linux AMI that's configured to perform NAT.  
Cost | Charged depending on the number of NAT gateways you use, duration of usage, and amount of data that you send through the NAT gateways. | Charged depending on the number of NAT instances that you use, duration of usage, and instance type and size.  
Type and size | Uniform offering; you don’t need to decide on the type or size. | Choose a suitable instance type and size, according to your predicted workload.  
Public IP addresses | Choose the Elastic IP address to associate with a NAT gateway at creation. | Use an Elastic IP address or a public IP address with a NAT instance. You can change the public IP address at any time by associating a new Elastic IP address with the instance.  
Private IP addresses | Automatically selected from the subnet's IP address range when you create the gateway. | Assign a specific private IP address from the subnet's IP address range when you launch the instance.  
Security groups | Cannot be associated with a NAT gateway. You can associate security groups with your resources behind the NAT gateway to control inbound and outbound traffic. | Associate with your NAT instance and the resources behind your NAT instance to control inbound and outbound traffic.  
Network ACLs | Use a network ACL to control the traffic to and from the subnet in which your NAT gateway resides. | Use a network ACL to control the traffic to and from the subnet in which your NAT instance resides.  
Flow logs | Use flow logs to capture the traffic. | Use flow logs to capture the traffic.  
Port forwarding | Not supported. | Manually customize the configuration to support port forwarding.  
Bastion servers | Not supported. | Use as a bastion server.  
Traffic metrics | Not supported. | View CloudWatch metrics.  
Timeout behavior | When a connection times out, a NAT gateway returns an RST packet to any resources behind the NAT gateway that attempt to continue the connection (it does not send a FIN packet). | When a connection times out, a NAT instance sends a FIN packet to resources behind the NAT instance to close the connection.  
IP fragmentation | Supports forwarding of IP fragmented packets for the UDP protocol.Does not support fragmentation for the TCP and ICMP protocols. Fragmented packets for these protocols will get dropped. | Supports reassembly of IP fragmented packets for the UDP, TCP, and ICMP protocols.  
  
Chúng ta vừa dạo một vòng AWS Networking. Bạn có học hỏi được gì từ hoa viên của Jeff không? Hãy trang trí cho hoa viên của mình theo cách này để có một hoa viên thật đẹp nhé!
