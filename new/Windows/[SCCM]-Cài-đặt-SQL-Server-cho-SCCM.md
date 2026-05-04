---
title: "[SCCM] Cài đặt SQL Server cho SCCM"
date: 2019-03-30 22:08:58
categories: [Windows]
---

Sau bài chuẩn bị ở kì trước, kì này ta đến với bài cài đặt SQL Server cho SCCM. Triển khai SQL Server cho CAS hay Primary Site là tương đối giống nhau & không mấy phức tạp. Ta có thể tóm gọn trong hai bước sau: 

  1. Cài đặt SQL Server với account sa là domain account **sccm.admin**
  2. Set **Computer Account** của SCCM Server sẽ sử dụng SQL Server này vào group Local Administrator trên SQL Server.

Sau đây là quá trình chi tiết. 

## Cài đặt SQL Server

Đầu tiên, ta mount file .iso chứa source SQL Server lên. Chọn **setup.exe** với quyền **Administrator.** Tùy vào mô hình lựa chọn, bạn có thể chọn cài đặt SQL Server stand-alone hoặc cài đặt SQL Fail-over Cluster để tăng cường khả năng HA của hệ thống. Ở đây Cloudcraft chọn **New SQL Server Standalone installation or add features to an existing installation** ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-01.png) Cloudcraft khuyến khích update SQL Server lên bản mới nhất ngay khi cài đặt. Bạn có thể chọn tick chọn **Use Microsoft Update to check for updates (recommend)** để download & cài đặt tự động từ Microsoft Update hoặc không tick và tải bản Service Patch rồi tự update sau. Ở đây Cloudcraft sẽ chọn phương án sau. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-02.png) Dù có là Bill Gate thì cũng cần phải trả hóa đơn tiền điện & thực phẩm nên Cloudcraft khuyến khích bạn xài đồ có bản quyền. Nếu để testing, bạn có thể chọn bản free & add key sau này. Ở đây, Cloudcraft CHOI LON. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-03.png) Tick chọn **Accept the license & Privacy Statement** như một thói quen. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-04.png) Tại mục lựa chọn Feature, bạn chọn: 

  * **Database Engine Services**
    * **SQL Server Replication** (để các SQL Server replication dữ liệu với nhau)
  * **Reporting Service – Native** (để sử dụng feature Reporting của SCCM)

![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-05.png) Tại mục cấu hình SQL Server Instance, bạn chọn **Named instanc** e và nhập tên instance (nên là duy nhất để dễ phân biệt trong toàn hệ thống). Ví dụ ở đây là **CAS_SQL** là SQL Server cho CAS. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-06.png) Điền **Service Account** sẽ sử dụng. Bạn có thể dùng luôn domain account **sccm.admin** ở đầu cho đơn giản hoặc tạo một Service Account riêng ví dụ như <tên domain>\**sccm.sql.admin**. Cloudcraft khuyến khích bạn nên có một **Service Account riêng** & giữ nguyên thiết lập **Collation** mặc định. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-07.png) Tại tab cấu hình **Authentication** , bạn chọn **Mixed Mode** , điền password cho account **SA** , đồng thời cho phép sccm.admin có quyền administrator trên SQL Server. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-08.png) Đổi qua tab **Data Directories** , bạn cấu hình nơi chứa **System Database** , **User Database** , **Database log** & các bản **backup**. Cloudcraft khuyến nghị tách biệt nơi chứa các đối tượng trên (ít nhất là các Partition khác nhau). Đồng thời, bạn nên có kế hoạch lưu trữ các bản Backup ở nơi khác (để phục hồi khi cần). ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-10.png) Bạn có thể tunning thêm các thông số tại **TempDB** & **FILESTREAM**. Cloudcraft khuyến nghị bạn tunning khi bạn thật sự hiểu bạn đang tunning cái gì, còn không thì nên giữ mặc định. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-11.png) Tại mục cấu hình Reporting Service, chọn **Install and configure**. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-12.png) Ra soát lại thông tin, back lại để chỉnh sửa nếu cần rồi nhấn **Install**. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-13.png) Ra làm li cafe trong lúc chờ. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-14.png) 99.99% là đều thành công. Nếu xui, bạn check log tại file **Summary_log** với đường dẫn bên dưới. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-15.png) Vì Cloudcraft không tin lắm vào Windows Update nên quyết định tải bản Service Patch & cài đặt sau. Sau khi update xong, bạn đã cài đặt xong SQL Server. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-16.png)

## Set Computer Account của SCCM Server vào group Local Administrator trên SQL Server.

Trên **SQL Server,** mở**Computer Management.** Chọn **System Tools** > **Local Users and Groups** > **Groups**. Chuột phải vào group **Amininistrators** , chọn **Properties**. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-17.png) Add thêm **Computer Account** của SCCM Server, **Domain Account** sccm.admin. Ví dụ ở đây SCCM Server sẽ là <tên domain>\**SCCM-CAS-01**. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-cai-dat-sql-server-18.png) Như vậy là đã xong bước cài đặt SQL Server cho SCCM. Kì tới sẽ là cài đặt SCCM Server. 

  * 

