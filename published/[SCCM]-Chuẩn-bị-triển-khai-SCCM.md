---
title: "[SCCM] Chuẩn bị triển khai SCCM"
date: 2019-03-23 18:00:57
categories: [Windows]
---

Ở [bài trước](https://cloudcraft.info/gioi-thieu-sccm/), Cloudcraft đã giới thiệu sơ bộ về SCCM, các chức năng, cách thức hoạt động và mô hình triển khai. Ở bài này, Cloudcraft sẽ giới thiệu chi tiết hơn các bước chuẩn bị trước khi triển khai SCCM. 

## Server - Network:

Với SCCM, tương ứng với mỗi Server Site, cần một database SQL Server đi kèm. Trừ Secondary Site có thể sử dụng **SQL Server Express** cài chung trên một Server với SCCM, còn đối với CAS/Primary Site, Cloudcraft khuyến khích cài đặt SCCM & SQL Server tách biệt. Điều này sẽ dễ dàng cho việc scale về sau, cũng như có thể cân nhắc triển khai SQL Server mô hình **Always On Failover Clustering** hoặc **Always On Availability Groups** để tăng cường khả năng HA của hệ thống. **#Lạm bàn** : thật tế thì nếu triển khai mô hình có CAS, hệ thống đã có khả năng HA cơ bản chứ không cần phải triển khai thêm các giải pháp về database mới có. Về bản chất, các SQL Server của CAS, Primary Site & Secondary Site sẽ luôn luôn synchronize data với nhau theo mô hình replication. Bất kể Site nào down (kể cả CAS), hệ thống vẫn sẽ tiếp tục hoạt động. Nếu CAS down, các Primary Site vẫn tiếp tục hoạt động bình thường vì vốn dĩ CAS không trực tiếp tương tác end-device. Nếu một Primary Site down, thông qua CAS, admin có thể chọn Primary Site khác gần đó để tiếp tục các task quản lý với nhóm end-device "mồ côi" kia. Khi Site bị down được khôi phục lại (thông qua bản backup hoặc cài lại mới), Site đó sẽ tự đồng bộ lại dữ liệu hệ thống. Nếu là Primary Site, nó sẽ đồng bộ với CAS. Nếu là CAS, nó sẽ lựa chọn Primary Site có dữ liệu latest hoặc do chỉ định bởi admin. Luyên thuyên một hồi, cấu hình tham khảo sẽ là: 

  * **Primary Site** : CPU: 8 core, RAM: 16 GB , HDD: 500 GB (bao gồm cả các Software,Patch Update cho end-devices), Windows Server 2012 Standard trở lên. Port: 80, 443.
  * **SQL Server cho Primary Site** : CPU: 16 core, RAM: 72 GB, HDD: 500 GB (SQL Server, Log, Backup), SQL Server SP3 2012 Standard trở lên. Port: 4022, 1433 (SCCM Servers only).
  * **CAS** : CPU: 8 core, RAM: 16 GB, Windows Server 2012 Standard trở lên. Port: 80, 443.
  * **SQL Server cho CAS** : CPU: 16 core, RAM: 96 GB, SQL Server SP3 2012 Standard trở lên. Port: 4022, 1433. (SCCM Servers only).
  * **End-Device** : OS: Windows 7, Windows Server 2018 SP2 trở lên. Network: **File and Printer Sharing (Inbound/Outbound), WMI (Inbound),** 2701 (Remote Control),...

Bạn tham khảo thêm tại trang hướng dẫn của Microsoft: ([Hardware](https://docs.microsoft.com/en-us/sccm/core/plan-design/configs/recommended-hardware)), ([OS](https://docs.microsoft.com/en-us/sccm/core/plan-design/configs/supported-operating-systems-for-site-system-servers)), ([Network](https://docs.microsoft.com/en-us/sccm/core/plan-design/hierarchy/ports#BKMK_CommunicationPorts)) & ([Network for SCCM Client Installation](https://docs.microsoft.com/en-us/sccm/core/clients/deploy/windows-firewall-and-port-settings-for-clients#ports-used-during-configuration-manager-client-deployment)). 

## Active Directory

Ta cần khởi tạo một account **sccm.admin** có quyền **Domain Admin** để sử dụng cho dịch vụ SCCM. Đồng thời trên AD sẽ cài đặt SCCM, ta cần chỉnh sửa một chút để có thể triển khai SCCM. Các chỉnh sửa sau chỉ cần thực hiện một lần duy nhất trên mỗi _**AD**_ sẽ triển khai SCCM. Nếu triển khai trên toàn Forest thì chỉnh sửa một lần duy nhất tại Root Domain & sccm.admin có thể là **Enterprise Admin**. 

### Extend Schema:

Dùng một account có quyền **Schema Admins** , login vào Domain Controller. Mount bộ cài đặt SCCM (có dạng .iso) vào. Chạy tool **Extadsch.exe (**SMSSETUP\BIN\X64\\)**** với quyền administrator. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-chuan-bi-cai-dat-1-e1553336328900.png) Sau khi chạy xong, check log (**C:\ExtADSch.log)** để kiểm tra. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-chuan-bi-cai-dat-2.png)

### Tạo container "System Management".

Dùng một account có quyền **Create All Child Objects** trên container **System** mở **ADSI Edit (adsiedit.msc)** , tìm đến **Domain** sẽ tạo. Chuột phải vào **CN=System** , chọn **New > Object.** Tại **Create Object** , chọn **Container** rồi **Next.** Tại **Value** , nhập **System Management** rồi **Next**. Sau khi tạo thành công, chuột phải **CN=System Management** , chọn **Properties**. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-chuan-bi-cai-dat-3.png) Chuyển sang tab **Security** , chọn **Add**. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-chuan-bi-cai-dat-4-e1553337276582.png) Thêm vào account **sccm.admin** cũng như **Computer Account** của Server sẽ chạy SCCM. Chọn **Ok** để hoàn tất. ![](https://cloudcraft.info/wp-content/uploads/2019/03/sccm-chuan-bi-cai-dat-5.png) Sau khi hoàn tất các bước chuẩn bị, các bài tiếp theo Cloudcraft sẽ giới thiệu các bước setup trên SQL Server & SCCM Server.
