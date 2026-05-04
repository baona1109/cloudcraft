---
title: "[NFV] Phần 1 - Giới thiệu về NFV"
date: 2018-04-17 20:58:20
categories: [NFV, Virtualization, Cloud Computing]
---

Trong loạt bài viết (khá là dài kỳ và nặng ký này), mình sẽ giới thiệu tới các bạn một công nghệ mới là NFV đang được các nhà mạng lớn trên toàn cầu nghiên cứu, triển khai và áp dụng. Ở VN thì hiện tại 2 ông lớn là Viettel và VNPT cũng đang rục rịch nghiên cứu triển khai thử cho hệ thống mạng core của họ.

Do loạt bài này được lấy từ luận văn tốt nghiệp của mình và bạn Công Trần nên về từ ngữ, cách sắp xếp câu chữ có chút hơi hàn lâm, các bạn thấy có phần nào khó hiểu hay chưa rõ thì cứ để lại cmt bên dưới, mình sẽ điều chỉnh lại cho dễ đọc hơn ;) vì mục đích chính cũng là chia sẻ kiến thức cho mọi người mà (ở VN thì mình thấy ít có ai đăng những bài dạng này).

Thôi dài dòng vậy là đủ :v, vô đề thôi :v

# Thực trạng hạ tầng mạng hiện nay

Trong thời đại hiện nay, chúng ta đang chứng kiến sự ra đời của hàng loạt các công nghệ mới như mạng di động 5G, Internet của vạn vật (Internet of Things), điện toán đám mây (Cloud Computing), thực tại ảo (Virtual Reality)… Bên cạnh đó, việc mở rộng hoạt động kinh doanh của các doanh nghiệp Internet và viễn thông dẫn đến nhu cầu về băng thông, chất lượng đường truyền và quản lý luồng dữ liệu tăng lên theo cấp số nhân. Sau đây là một số dự đoán về hiện trạng mạng toàn cầu trong giai đoạn 2015 - 2020: 

  * Mức độ sử dụng Internet sẽ tăng từ mức 10GB/người/tháng vào năm 2015 lên đến 25GB/người/tháng vào năm 2020. Kéo theo đó, lưu lượng Internet toàn cầu có thể sẽ bước qua mốc zettabyte (1 zettabyte = 1 tỷ terabyte) và có thể sẽ đạt đến mức 2.3 zetabyte vào năm 2020. 
  * Lưu lượng dữ liệu của các thiết bị không dây và điện thoại di động sẽ chiếm đến gần ⅔ tổng lưu lượng Internet toàn cầu vào năm 2020.
  * Tốc độ Internet băng thông rộng sẽ tăng gần như gấp đôi vào năm 2020 (từ mức 24.7 Mbps vào năm 2015 và đạt mức 47.7Mbps năm 2020).



_(Nguồn: Cisco Visual Networking Index 2015-2020)_

Tại Việt Nam, vào nửa đầu năm 2017, Viettel cũng đã bắt đầu triển khai rộng rãi hệ thống mạng 4G tại Việt Nam. Việc này đặt ra một bài toán phức tạp về việc xây dựng lại hạ tầng phần cứng mạng bên dưới để đáp ứng các nhu cầu mới.

Qua những số liệu trên, ta có thể thấy xu hướng phát triển vũ bão của Internet nhằm đón đầu thời đại Cách Mạng Công Nghiệp 4.0 đã và đang diễn ra. Nhu cầu phải luôn không ngừng cải thiện hạ tầng mạng (cả chất lẫn lượng) là một nhu cầu thiết yếu không chỉ ở thế giới mà còn ở Việt Nam. Điều này đặt ra cho các nhà cung cấp dịch vụ mạng (Network Service Provider) áp lực phải luôn không ngừng mở rộng qui mô cũng như nâng cao chất lượng dịch vụ truyền dẫn. Thế nhưng đây lại không phải là một vấn đề đơn giản, cách làm phổ biến hiện tại của các nhà cung cấp dịch vụ viễn thông hiện tại đa phần là mua sắm thêm các thiết bị phần cứng chuyên dụng cho mỗi một dịch vụ mạng mới. Cách tiếp cận này hiện đang bộc lộ nhiều bất cập.

Đi vào thực tế, ta có thể nhận thấy rằng đa phần những hệ thống mạng hiện tại đều sử dụng thiết bị chuyên dụng của các hãng như Cisco hay Juniper,... Tuy nhiên, những hệ thống này lại có các khuyết điểm như: giá thành thiết bị đắt đỏ, khó quản lý tập trung, kém tương thích với các hệ thống của hãng khác, tốc độ cập nhật phần mềm chậm, giấy phép sử dụng phần mềm thường ngắn... Một điểm cần lưu ý là với những thiết bị mạng truyền thống của các hãng này thì việc triển khai một dịch vụ mới, một chức năng mới tốn kém rất nhiều cả về thời gian lẫn tiền bạc. Ta có thể điểm sơ qua quy trình khởi tạo một dịch vụ mạng hiện nay, gồm những bước sau: xác định nhu cầu, thiết kế, lắp đặt thiết bị mạng, đấu nối dây, cấu hình dịch vụ, kiểm thử và cuối cùng mới là đưa vào vận hành. 

Thông thường, với mỗi một quy trình như vậy có thể phải cần tới vài ngày hay vài tuần để đưa hệ thống mới vào hoạt động. Trong khi đó, mỗi một dự án lại có các yêu cầu riêng, đòi hỏi những loại thiết bị chuyên dụng khác nhau. Với một quy trình dài dòng và nhiêu khê như vậy sẽ làm lãng phí rất nhiều thời gian và nhân lực cho mỗi dự án mới, khách hàng mới. Đặc biệt là có những dự án có thời gian sử dụng ngắn từ vài tháng đến chỉ vài ngày hoặc thậm chí là vài giờ thì việc triển khai dịch vụ theo mô hình truyền thống là vô cùng lãng phí và tốn thời gian.

  ![](https://cloudcraft.info/wp-content/uploads/2018/04/Screenshot-2018-04-17-20.45.10-e1523972822164.png)

_Khuyết điểm của hạ tầng mạng hiện nay_

Đây là những khuyết điểm không thể chấp nhận trong môi trường công nghệ thông tin hiện nay bởi nhu cầu của từng khách hàng hiện tại là rất đa dạng và đặc thù. Mỗi một giây chậm trễ đều lãng phí tiền bạc và nguồn lực của công ty mà quan trọng hơn là đánh mất sự tín nhiệm của người dùng. Với những vấn đề tồn đọng trên thì hạ tầng mạng hiện có được dự báo sẽ không thể đáp ứng kịp nhu cầu của thị trường cũng như đảm bảo lợi ích của các chủ thể bao gồm doanh nghiệp, nhà cung cấp dịch vụ và người dùng cuối.

  ![](https://cloudcraft.info/wp-content/uploads/2018/04/EnduserEnterpriseCarrier.jpg)

_Nhu cầu của các chủ thể._

  **_Vậy thì liệu có cách nào để giải quyết được bài toán trên hay không?_**

Câu trả lời là có. Giải pháp ở đây chính là ứng dụng công nghệ ảo hóa (Virtualization) vào hạ tầng mạng tại các trung tâm dữ liệu (Datacenter), các điểm chuyển mạch lớn (Network Node) trên đường truyền hoặc tại nhà của người dùng cuối bằng công nghệ Ảo hóa Chức năng Mạng (Network Function Virtualization - hay gọi tắt là NFV).

Công nghệ NFV cho phép ta tách biệt các hàm chức năng mạng (Network Function - NF) như: NAT, Firewall, Intrusion Detection, DNS, Caching,... khỏi các thiết bị vật lý chuyên biệt và triển khai các NF này dưới hình thức phần mềm có thể chạy trong môi trường ảo hóa - trên các thiết bị phần cứng phổ thông. Các thiết bị vật lý lúc này không còn là các phần cứng độc quyền của các hãng nữa, mà có thể là các máy chủ (servers), thiết bị chuyển mạch (switches) và thiết bị lưu trữ dữ liệu (storages)_được sản xuất hàng loạt theo các tiêu chuẩn công nghiệp chung_ (standard high volume hardware).

Việc này sẽ giúp ta giảm chi phí đầu tư và sự phụ thuộc vào các thiết bị phần cứng chuyên biệt của từng hãng như trước đây. Đồng thời, các nhà mạng có thể khởi tạo, điều phối và di dời các hàm chức năng mạng, các dịch vụ mạng một cách linh hoạt, từ đó tận dụng tốt hơn hạ tầng phần cứng đã đầu tư. Không chỉ chi phí đầu tư mà cả chi phí vận hành, bảo dưỡng và nâng cấp thiết bị sau này cũng sẽ được cắt giảm đáng kể.

_Một trong những nhà mạng lớn ở Mỹ hiện nay là AT &T đã tuyên bố rằng hãng sẽ ảo hóa 75% hạ tầng mạng của mình vào năm 2020 bằng cách ứng dụng công nghệ ảo hóa chức năng mạng (Network Function Virtualization - NFV) và công nghệ mạng máy tính được điều khiển bằng phần mềm (Software-defined Networking - SDN)._

# Giới thiệu công nghệ NFV

Công nghệ Ảo hóa Chức năng mạng (Network Function Virtualization - NFV) áp dụng công nghệ ảo hóa (Virtualization) và điện toán đám mây (Cloud Computing) vào các máy chủ, thiết bị chuyển mạch và thiết bị lưu trữ phổ thông (Commercial off the Shelf) nhằm tạo ra một môi trường để triển khai các hàm chức năng mạng ảo hóa (Virtualised Network Function - VNF) như: switching, firewall, routing, load balancing,... có chức năng tương tự như trên các thiết bị mạng chuyên trách truyền thống.

Với cách tiếp cận truyền thống của các nhà cung cấp dịch vụ mạng, ứng với mỗi dịch vụ, mỗi chức năng mạng sẽ phải có những thiết bị chuyên trách riêng đảm nhận. Do mỗi thiết bị chỉ đảm trách những nhiệm vụ riêng nên hiệu năng sẽ rất cao nhưng lại khiến việc triển khai, vận hành, bảo dưỡng hay mở rộng trở nên phức tạp.

![](https://cloudcraft.info/wp-content/uploads/2018/04/Capture.png)

_So sánh giữa cách tiếp cận sử dụng các thiết bị mạng chuyên biệt truyền thống và ảo hóa mạng (Nguồn ETSI white paper 2012)_

Hướng tiếp cận mới sử dụng NFV sẽ giúp nhà cung cấp dịch vụ mạng linh hoạt hơn trong hoạt động kinh doanh của mình. Tuy vậy, NFV cũng có những khuyết điểm nhất định cần được khắc phục. Chúng ta cùng điểm qua một vài tiêu chí so sánh giữa hai hướng tiếp cận này.

  **Tiêu chí so sánh** | **Hạ tầng mạng truyền thống** | **Ứng dụng NFV**  
---|---|---  
**_Chi phí phần cứng_** | Chi phí cao hơn do phải mua cả giải pháp của từng hãng phần cứng chuyên biệt. | Chi phí thấp hơn do chỉ sử dụng phần cứng phổ thông, đồng thời chủ động được về phần mềm.  
**_Khả năng tùy biến, mở rộng, thay thế phần cứng._** | Khó khăn do phụ thuộc hoàn toàn vào hãng phần cứng. Khi cần thay thế thì đa phần phải thay thế toàn bộ. | Dễ dàng do chỉ sử dụng các thiết bị phần cứng phổ thông.  
**_Khả năng tùy biến, quản trị, thay thế, nâng cấp phần mềm_** | Thấp hơn do phần mềm trên các thiết bị phần cứng chuyên biệt (firmware) phụ thuộc vào tài nguyên thiết bị và hãng sản xuất. | Cao, do cơ chế nguồn mở và có nhiều hãng cung cấp phần mềm điều khiển.  
**_Khả năng điều khiển luồng traffic_** | Khó, vì phụ thuộc vào hãng sản xuất và sẽ rất phức tạp nếu sử dụng giải pháp từ nhiều hãng phần cứng khác nhau. | Dễ dàng, linh động hơn, đặc biệt là nếu được kết hợp với công nghệ Software-defined Network.  
**_Hệ sinh thái_** | Nhỏ, bó buộc theo giải pháp của từng hãng. | Rộng, dễ dàng tương tác với các thành phần của các hãng khác thông qua các chuẩn giao tiếp chung được ETSI đặt ra.  
**_Hiệu năng, độ ổn định của dịch vụ._** | Cao do sử dụng các thiết bị được thiết kế chuyên biệt cho từng chức năng mạng đặc thù. | Thấp hơn do sử dụng các thiết bị phần cứng phổ thông. Tuy nhiên, về lâu dài, hiệu năng sẽ dần được cải thiện.  
**_Khả năng nhận được trợ giúp, hỗ trợ._** | Cao do giấy phép sử dụng của giải pháp đều đi kèm với gói hỗ trợ chính hãng. | Khá thấp nếu sử dụng các giải pháp nguồn mở. Nếu sử dụng các giải pháp thương mại thì vẫn có nguy cơ nhất định do thị trường NFV còn khá non trẻ.  
**_Đào tạo nhân sự_** | Nhân sự cần phải tham gia các khóa học của riêng từng hãng phần cứng và lệ thuộc vào hãng phần cứng đó. | Nhân sự dễ dàng tiếp cận tài liệu, mã nguồn và khóa học về các thành phần của hệ thống do cơ chế mở.  
  
_Bảng so sánh giữa NFV và hạ tầng mạng truyền thống_

  (...còn típ...)  

# Tham khảo

  * NFV Whitepaper 2012, 2013, 2014 - ETSI
  * Bài phỏng vấn của Openstack.org với đại diện của SK Telecom 2/2016 
    * [http://superuser.openstack.org/articles/how-openstack-is-helping-sk-telecom-roll-out-the-next-5g-lte-network](http://superuser.openstack.org/articles/how-openstack-is-helping-sk-telecom-roll-out-the-next-5g-lte-network)
  * Viettel đang gấp rút triển khai mạng 5G trên cả nước 
    * [http://vneconomy.vn/cuoc-song-so/viettel-dang-gap-rut-trien-khai-mang-4g-tren-ca-nuoc-20170224024715453.htm](http://vneconomy.vn/cuoc-song-so/viettel-dang-gap-rut-trien-khai-mang-4g-tren-ca-nuoc-20170224024715453.htm)
  * A network build in software - AT&T
    * <http://about.att.com/innovation/sdn>


  * NFV and SDN spend set to hit $157B by 2020 
    * [http://www.rcrwireless.com/20150917/telecom-software/nfv-and-sdn-spend-set-to-hit-157b-by-2020-tag2](http://www.rcrwireless.com/20150917/telecom-software/nfv-and-sdn-spend-set-to-hit-157b-by-2020-tag2)

 
