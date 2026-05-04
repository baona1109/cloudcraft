---
title: "[SCCM] Giới thiệu SCCM"
date: 2018-08-04 20:14:32
categories: [Windows]
---

Trong môi trường Linux, chúng ta có rất nhiều các công cụ monitoring và automation như Ansible, Puppet, Zabbix,… để quản lý tập trung một hệ thống gồm nhiều node. Nhưng còn trong môi trường Microsoft Active Directory (thường gọi nôm na là AD hay Domain) gồm rất rất nhiều các client computers thì sao ? Dù các built-in tools của AD đã có thể giải quyết được phần lớn các tác vụ cần thiết tuy nhiên không phải là không có các hạn chế. Bài viết này sẽ giới thiệu một hệ thống chính chủ Microsoft (nhưng không nằm sẵn trong Windows Server) và có lịch sử phát triển lâu đời là SCCM hay ConfigMgr.

# **Vậy SCCM là cái vẹo gì ?**

SCCM (tên cúng cơm là **System Center Configuration Manager** nhưng thường được gọi tắt là **SCCM** hay **ConfigMgr**) là một hệ thống **_quản trị thiết bị của end-user_**(Windows Server/Client, Linux, macOS). Quản trị ở đây bao gồm giám sát & xuất report (thông tin hardware/software inventory của từng máy, tần suất sử dụng software,...), tự động triển khai hàng loạt application, batch script hay thậm chí là cả OS. Ngoài ra, SCCM còn có thể tích hợp với các role/feature có sẵn khác trên Windows Server để: hỗ trợ end-user (thông qua Remote Desktop), cài đặt hàng loạt các gói Windows Update (thông qua WSUS),... SCCM có thể quản trị tại LAN, WAN hoặc thậm chí là qua... Internet (cấu hình tương đối phức tạp). Best Practice, SCCM quản lý hiệu quả nhất các máy chạy Windows đã join vào AD. Tuy vậy, SCCM vẫn có thể quản lý các máy Workgroup hoặc hệ điều hành khác miễn là có quyền Local Administrators hoặc Root trên các máy này.

![](https://cloudcraft.info/wp-content/uploads/2018/08/sccm-2012-logo-e1533401294810.jpg)

SCCM có tuổi đời phát triển tương đối "lâu", "dài" và được sử dụng rộng rãi trong... các doanh nghiệp nước ngoài. Tại đây, "SCCM luôn nằm trong JD của vị trí SysAdmin hệ thống AD và thực sự có những người đã dành cả sự nghiệp với nó" - trích lời một chuyên gia của... VMWare. Ở VN, SCCM ít phổ biến hơn đơn giản vì quá mắc và không có crack (ngoài tiền License cho SCCM Server, Microsoft còn tính tiền theo số client/computer sẽ quản trị :surrender:). Tuy vậy, người viết cũng đã có dịp chứng kiến 2 doanh nghiệp thuộc Top 5 doanh nghiệp CNTT hàng đầu VN năm 2018 chịu đầu tư thứ bùa yêu này vào hệ thống của họ.

SCCM tiền thân là **System Management Server** (SMS) có lần debut từ tận... 1994. (nên bạn đừng thấy lạ nếu trong quá trình vận hành hệ thống SCCM và bắt gặp cụm viết tắt SMS everywhere :) ). Sau đó, trải qua các phiên bản: 2003, 2007, 2012,... thì đến nay được gọi tên theo thời gian phát hành theo format YY/MM (gần tương tự mã các phiên bản Windows 10) như: 1710, 1802, 1806,…. Phiên bản mới nhất và ổn định nhất sẽ kí hiệu là CB (Current Branch). Bạn nên lựa chọn phiên bản này khi cài hệ thống mới.

Bên cạnh đó, Microsoft còn có **Intune** là một sản phẩm tương tự SCCM nhưng non trẻ hơn và chủ yếu chỉ nhắm vào các thiết bị di động (Windows 10, Android, iOS, macOS). Intune là một dạng Software as a Service chạy trên nền tảng đám mây Microsoft Azure (quản trị qua Portal Azure). Người Admin hoàn toàn có thể tích hợp hệ thống SCCM sẵn có với Intune để quản trị cả Desktop, Laptop và Mobile. Không rõ sau này Microsoft có bỏ hẳn SCCM và chỉ tập trung phát triển Intune không, nhưng với xu thế người người nhà nhà lên mây như hiện nay thì các bạn làm SysAdmin hệ thống AD nên tìm hiểu thử Intune. Ở thời điểm hiện tại, cá nhân người viết cảm thấy Intune chưa thật sự tốt và đang bị cạnh tranh gay gắt bởi một đối thủ sừng sỏ là **Airwatch** của VMWare. Tuy nhiên, Intune sẽ được đề cập đến trong một bài viết khác. Ở đây người viết sẽ chỉ tập trung vào SCCM.

![](https://configmgrblog.com/wp-content/uploads/2014/11/BLOG-1000070.png)

# **Câu hỏi tiếp theo hẳn sẽ là Why SCCM ?**

Ai cũng biết Microsoft có giải pháp AD dùng để quản trị định danh, tài nguyên,... của computer/user trong hệ thống IT doanh nghiệp. Đây là một trong những cần câu cơm chính của Microsoft nên họ làm khá là ổn và trên thật tế là rất rất được ưa chuộng. Rất ít doanh nghiệp nào kể cả là doanh nghiệp CNTT chịu đầu tư giải pháp OpenSource cho mục này.

Tuy nhiên, AD cũng không phải là không có thiếu sót. Một trong số đó là vấn đề **_quản trị thiết bị của end-user hay cụ thể hơn việc cài đặt phần mềm trên máy của người dùng cuối_** (một dạng quản lý tài sản). Admin có thể biết các computer trong AD tên gì hay IP bao nhiêu nhưng không hề biết dưới các máy ấy đang cài đặt những phần mềm gì, phần mềm ấy có phù hợp với chính sách cty không ? Sếp hỏi thì biết trình bày thế nào khi mà hệ thống có cả chục, cả trăm, cả ngàn máy chưa kể các máy có thể không chỉ ở một site mà có thể là rất nhiều site khác vị trí địa lý ? Không chỉ dừng lại là chuyện báo cáo cho mấy thằng sếp mà vấn đề này được đưa hẳn vào checklist để đạt các thể loại chứng nhận hồ sơ năng lực doanh nghiệp. Nếu doanh nghiệp muốn vươn tầm "làm ăn lớn" với các nền văn minh nhân loại (Châu Âu, Bắc Mẽo, Nhật bổn,...) thì đây là việc cần làm. Cá nhân người viết từng có dịp chứng kiến một công ty phần mềm Nhật bắt buộc đối tác Outsourcing Việt Nam phải triển khai SCCM trong môi trường dev/test để họ có thể đảm bảo các thỏa ước với khách hàng của họ.

Cách làm thường thấy là IT man sẽ chỉ cài sẵn một số phần mềm thông dụng và để mặc người dùng cuối tự do muốn cài gì cài. Điều này nghe thì có vẻ hợp lí vì không thể nào cài sẵn được tất cả các phần mềm thỏa hết nhu cầu sử dụng của mọi client. Tuy vậy, điều này tạo một kẽ hở về bảo mật cho phép người dùng "vô tình" hay "cố ý" cài đặt các phần mềm không rõ nguồn gốc, độc hại vào máy của mình, từ đó có thể lan rộng ra cả hệ thống. Ngược lại, nếu siết policy chỉ IT man mới cài đặt được phần mềm thì lại thêm việc cho IT man bởi sẽ lòi ra những ticket hỗ trợ "vớ vẩn" kiểu: cài Chrome, Unikey, Visual Studio... Cần một phương pháp để vừa siết chặt quyền cài đặt phần mềm, nguồn gốc phần mềm nhưng cũng cho phép client tự phục vụ theo nhu cầu riêng, đồng thời giúp IT man có thể monitoring quá trình sử dụng của client . Và thế là SCCM ra đời.

# **Vậy cách làm việc của SCCM là như thế nào ?**

  1. Đầu tiên, SCCM sẽ tìm cách discover toàn bộ hệ thống (một dạng network scan) thông qua một trong các nguồn: thông tin chứng thực trong AD, DCHP Server (Microsoft DHCP), ARP cache của Router, SNMP device

![](https://cloudcraft.info/wp-content/uploads/2018/08/080418_1206_SCCMGiithi2.png)

  2. Dựa trên thông tin "khám" được, SCCM sẽ gom nhóm các client tìm được vào các **Boundaries** (theo AD Site, IP Range, IP Subnet hoặc IPv6 prefix). Nhóm các Boundaries (Boundary Group) sẽ được mapping cho một **Site** (một cụm các server SCCM) để quản lý.
  3. Sau khi "phân loại", admin có thể chỉ định Site Server phụ trách cài đặt hàng loạt SCCM Client một cách tự động (yêu cầu quyền Local Administrator trên client computer) lên tất cả client computer mà nó phụ trách. Nếu cách làm này thất bại, hoàn toàn có thể dùng Group Policy của AD để đẩy bán tự động SCCM Client xuống cho Client. Nếu tất cả đều thất bại ? Well… cắm usb cài tay vậy.
  4. Nếu mọi chuyện êm đẹp, trên client computer sẽ xuất hiện một application mới là **Software Center**. Tại đây, user có thể chọn để Install/Uninstall những application,gói update,… phù hợp với mình (dĩ nhiên là trừ những Application được đẩy xuống dạng Required thì không tự sửa được) cũng như tùy chỉnh một số chính sách như: thời gian làm việc, thời gian chạy update, thời điểm tắt máy thích hợp,… Các application, gói update này đã được người quản trị thử nghiệm source cài đặt và make available cho người dùng.



![](https://cloudcraft.info/wp-content/uploads/2018/08/080418_1206_SCCMGiithi3.jpg)

(Hình chôm trên mạng – Credit: University of Exeter).

Client có nhu cầu cao ? Không thích các phần mềm được đẩy tự động cho mình ? Client có thể tạo request trên **Application Catalog**. (Một Web Portal để client tương tác).

![](https://cloudcraft.info/wp-content/uploads/2018/08/080418_1206_SCCMGiithi4.jpg)

(Hình chôm trên mạng – Credit: Prajwal Desai – a SCCM legend :))

Trong Control Panel, client có thể thấy mục **Configuration Manager** với các tùy biến sâu hơn như: SCCM Site, SCCM Management Point,…

![](https://cloudcraft.info/wp-content/uploads/2018/08/080418_1206_SCCMGiithi5.png)

(Hình chôm trên mạng – Credit: windows-noob.com – another SCCM legend :))

Về mặt bản chất, SCMM client này sẽ sử dụng lại dịch vụ WMI (Windows Management Instrumentation) trên máy của client. Vậy nên, đây sẽ là đầu mối để trace lỗi trên client đầu tiên nếu có sự cố xảy ra.

Trên server, Admin sẽ thấy được thông tin của từng client computer. Chuột phải vào từng computer, admin có thể deploy application/batch script, check thông tin hardware/software inventory, mở case support thông qua Remote Desktop,…

![](https://cloudcraft.info/wp-content/uploads/2018/08/080418_1206_SCCMGiithi6.png)

(Hình ảnh chỉ mang tính minh họa và đã được censored)

Để quản trị "dễ dàng" hơn, SCCM đưa ra khái niệm **Device Collection** và **User Collection**. Device collection áp dụng lên từng computer không quan tâm user nào login vào sử dụng, user collection áp dụng lên từng user không quan tâm user đó login vào computer nào (Cá nhân người viết khuyến khích sử dụng Device Collection hơn). Với Device Collection, Admin có thể gom nhóm các computer thành các Collection như: Collection các máy chạy Windows 10 thuộc OU phòng ban A, Collection các máy có Computer Name có format "ITxxx",… Khi cần deploy gì đó, Admin có thể deploy thẳng vào Collection thay vì click từng máy một. Collection của SCCM chia làm hai dạng: Direct (fixed) và Query (dynamic). Dạng đầu là tạo cố định một danh sách và sẽ không thay đổi sau này, dạng sau có thể lập lịch update lại danh sách thành viên. Query càng phức tạp càng đòi hỏi trình độ viết câu SQL Query cao (Tham khảo:[ https://goo.gl/jdT6X9](https://goo.gl/jdT6X9)). VD hình bên dưới là một **Device Collection** mang tên **HN - IT Department**.

![](https://cloudcraft.info/wp-content/uploads/2018/08/080418_1206_SCCMGiithi7.png)

(Hình ảnh chỉ mang tính minh họa và đã được censored)

Ngoài ra, Admin có thể dễ dàng xuất report của một loạt các máy. SCCM cung cấp một bộ built-in report khá tốt nhưng vẫn có thể custom một report riêng (đòi hỏi cài thêm Report Builder và đương nhiên là... khả năng viết SQL Query rồi. Tham khảo <https://goo.gl/6fQ3pL>). Ảnh bên dưới minh họa một Report thể hiện trạng thái Security Update của các Server trong hệ thống, server nào còn các bản Required Update chưa cài đặt, server nào đã được cài đặt đầy đủ.

![](https://cloudcraft.info/wp-content/uploads/2018/08/080418_1206_SCCMGiithi8.png)

(Ảnh chôm của chính Microsoft – Credit: technet.microsoft.com)

Và cuối cùng, đỉnh kout nhất của SCCM chính là **Task Sequence**. Tính năng giúp tự động thực hiện một loạt các tasks liên tiếp như: deploy OS, Drivers, apply Network/Windows Settings, deploy application,… (Tham khảo danh sách task: <https://goo.gl/MWNCXw>). Chắc bạn cũng đã mường tượng được một viễn cảnh sáng lạng như chủ nghĩa Má… à mà thôi. Với Task Sequence, Admin có thể tự động cài đặt hàng loạt OS xuống client (thông qua PXE). Sau khi cài đặt OS xong, Drive Package tương ứng được tự động cài đặt. Tiếp đó, chạy các script cấu hình Network/Windows setting để cấu hình IP, join domain, cài đặt SCCM Client… Cuối cùng, các application phù hợp sẽ được tự động triển khai xuống => Done qui trình cấp máy mới cho nhân viên. Nếu triển khai cái này xong, MCSE Mobility là một trò muỗi đối với bạn :sure: Tự tin đi apply SCCM Admin cho công ty nước ngoài thôi.

Ngoài những tính năng liệt kê ở trên, SCCM còn một loạt các tính năng "râu ria" khác như: quản lý nguồn điện cho thiết bị Laptop (Power Management), Endpoint Protection (một thể loại antivirus, chống thất thoát dữ liệu doanh nghiệp base trên Windows Defender), triển khai Uptade Patching hàng loạt (kết hợp với WSUS)... Cá nhân người viết chưa có điều kiện dùng đến nơi đến chốn hết nên xin ko dẫn lời ở đây. 

# **Kiến trúc hệ thống**

Như đã đề cập ở trên, SCCM sử dụng khái niệm Site để phân cấp quản lý. Có 3 loại site: 

  * **Primary Site** : gồm các role Management Point để thu thập thông tin hardware/software inventory, truyền/gửi thông tin quản trị, Distribution Point để phân phối Application, Batch Script, Update,… đến client, Site Database chứa toàn bộ dữ liệu về các client được quản lý trong site,… Các role này có thể cài đặt all-in-one vào cùng một Server hoặc có thể được cài riêng lẻ lên nhiều Server để phục vụ HA cũng như giảm tải cho các Server chính. Đây là loại site quan trọng nhất, trực tiếp quản trị client. Bất kì hệ thống SCCM nào cũng cần tối thiếu một Primary Site (mô hình tối thiểu Stand-alone Primary Site).
  * **Secondary Site** : là một bản lightweight của Primary Site và chỉ đóng vai trò forwarder giữa client và Primary site. Ở các chi nhánh xa xôi, có kết nối trực tiếp không tốt đến Primary Site tuy nhiên số lượng client/IT man ở đó lại quá nhỏ không đủ dựng Primary Site mới, Admin có thể cân nhắc dựng Secondary Site thay vì chỉ là một hai Server đóng một vài vai trò riêng lẻ như Management Point, Distribution Point.
  * **Central Administration Site (CAS)** : quản lý tập trung nhiều Primary Site. Khi công ty phát triển và mở rộng, đòi hỏi phải tăng thêm lượng computer. Tùy vào số lượng client cũng như nhu cầu quản trị tại chỗ mà có thể mô hình Secondary Site là không phù hợp và bắt buộc phải dựng một Primary Site mới. Để quản trị tập trung các Primary Site này, cần có CAS. Lưu ý: CAS chỉ quản trị các Site mà không trực tiếp giao tiếp với client. Hệ thống SCCM có CAS sẽ unlock thêm một phương án để backup/recovery khi xảy ra sự cố rụng Primary Site.

![](https://cloudcraft.info/wp-content/uploads/2018/08/sccm-hierarchy.png)

# **Triển khai**

SCCM hiểu rất chính xác các khái niệm Forest, OU, Computer Group, User Group, AD Site,... cũng như các attribute của từng Object trong AD. Vậy nên best practice cho một hệ thống SCCM hẳn sẽ là trên nền một hệ thống AD có sẵn. Nếu sử dụng mô hình WorkGroup, bạn sẽ dễ dàng lọt hố ở nhiều bước, chẳng hạn như Deploy SCCM Client vì không thể collect được hết thông tin Local Administrator của tất cả các máy. Dĩ nhiên vẫn có chống chế bằng cách cấu hình tất cả các Local Adminisrator chung một password hoặc… chép USB cài từng máy nhưng như vậy thì quá thủ công và sẽ rất khổ sở khi sau này cần troubleshoot, update, upgrade,… Nếu không có AD và cũng không cần đến những tính năng kout cấp của SCCM, có thể cân nhắc sử dụng một số giải pháp khác, chẳng hạn như: _**Quản trị Windows bằng Ansible**_ (_Cloudcraft có một seri về mục này tại[Link](https://cloudcraft.info/ansible-quan-tri-windows-server-2012/)_).

Sau khi quán triệt tư tưởng và chấp nhận chi tiền, tiếp theo là chọn mô hình triển khai. Dựa vào số lượng client, nhu cầu quản trị mà có thể lựa chọn mô hình:

  * **Stand-alone Primary Site** : chỉ một Primary Site, ở các chi nhánh là các Secondary Site hoặc một số Server giữ một số Point riêng lẻ.
  * **Multi child Primary Site, có CAS** : do số lượng client quá lớn hoặc có nhu cầu quản trị client tại chỗ mà bắt buộc cần dựng Primary Site mới. Dù nhà ai nấy sáng, client ai nấy quản nhưng vẫn có sếp to để quản lý tập trung.
  * **Multi Primary Site, no CAS (một dạng trick không khuyến khích)** : vẫn là nhà ai nấy sáng, client ai nấy quản nhưng không có chuyện quản lý tập trung. Mô hình này cần **_cân nhắc rất kĩ_**. Vì nếu cần CAS thì **_bắt buộc_** cài CAS trễ nhất là ngay trước khi cài Primary Site thứ hai. Nếu đã chạy Stand-alone Primary Site từ trước, bạn có option expand Primary Site đó để có thêm CAS. Từ Primary Site thứ hai, bắt buộc phải trỏ về CAS trong quá trình cài đặt Site (không có option edit, cài sai là cài lại hết).



Sau khi chọn được mô hình, đến chọn hardware cho Server. Tùy vào số lượng client, nhu cầu quản trị mà có thể sizing CPU, RAM, DISK, OS tương ứng. Bên cạnh đó là việc chạy SQL trên Server riêng hay chạy all-in-one. Người viết khuyến khích chạy SQL Server riêng để có lỡ tạch còn có cái mà recovery.

Để sizing chính xác, ngoài đọc kĩ link tham khảo của Microsoft như bên dưới thì bạn nên dựng Lab và trải nghiệm: 

  * [Client Numbers for Site & Hierarchy.](https://goo.gl/EyuaZr)
  * [Supported OS](https://goo.gl/Gr1oQW)
  * [Recommended Hardware](https://goo.gl/AR2hd2)

**#Update 01:** Theo kinh nghiệm bản thân, Cloudcraft khuyến khích ban đầu bạn nên triển khai mô hình Stand-Alone Primary Site trước. Khi cần có nhu cầu scale to lên, ta thêm các Secondary Site. Đến khi Primary Site đã đến ngưỡng, ta cải tạo sang mô hình có CAS bằng cách expand Stand-alone Primary Site ban đầu trở thành CAS và thêm các Child Primary Site tiếp theo. 

Phù…vậy là xong bài SCCM 101 và có thể lấy tiền nhuận bút rồi. Tất cả dựa trên kinh nghiệm thiết kế & triển khai thực tế của mình với các hệ thống dưới 18k user thôi nên nếu có gì sai rất mong nhận được góp ý. Nếu không có gì bất thường và không… lười, mình sẽ viết tiếp về vấn đề cài đặt và một số thứ hay ho khác ở các bài tới. :D
