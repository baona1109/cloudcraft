---
title: "Giới thiệu về LVM - Logical Volume Management"
date: 2018-09-26 14:43:54
categories: [Linux]
---

# Giới thiệu về LVM

LVM (Logical Volume Management) là một công nghệ giúp quản lý các thiết bị lưu trữ dữ liệu trên các hệ điều hành Linux. Công nghệ này cho phép người dùng gom nhóm các ổ cứng vật lý lại và phân tách chúng thành những phân vùng nhỏ hơn, dễ dàng mở rộng các phân vùng này khi cần thiết.

Các bạn có thể tham khảo thêm về cách quản lý đĩa cơ bản trên Linux tại bài viết này: [Hướng dẫn toàn tập về partition trên Linux](https://cloudcraft.info/huong-dan-toan-tap-ve-partition-tren-linux/)

Một số ứng dụng của LVM:

  * Quản lý một lượng lớn ổ đĩa một cách dễ dàng.
  * ~~Dễ dàng dồn điền, tách thửa disk space.~~
  * Điều chỉnh phân vùng ổ cứng một cách linh động.
  * Backup hệ thống bằng cách snapshot các phân vùng ổ cứng (real-time).
  * Migrate dữ liệu dễ dàng.



Trước khi đi sâu vào cách thức hoạt động của LVM, ta cần phải hiểu về một số khái niệm cơ bản của công nghệ này là:

  * **Physical Volume – PV:** Ổ cứng vật lý từ hệ thống (đĩa cứng, partition, iSCSI LUN, SSD…) là đơn vị cơ bản để LVM dùng để khởi tạo các Volume Group. Trên mỗi một PV sẽ chứa khoảng 1 MB header ghi dữ liệu về cách phân bố của Volume Group chứa nó. Header này sẽ hỗ trợ rất nhiều trong việc phục hồi dữ liệu khi có sự cố xảy ra.
  * **Volume Group – VG:** là tập hợp các ổ cứng vật lý (PV) thành một kho lưu trữ chung với tổng dung lượng của các ổ đĩa con. Mỗi khi ta thêm một PV vào VG, LVM sẽ tự động chia dung lượng trên PV thành nhiều Physical Extent với kích cỡ bằng nhau. Và từ VG, ta có thể tạo ra nhiều Logical Volume và dễ dàng chỉnh sửa dung lượng của chúng.
  * **Logical Volume – LV:** là các phân vùng luận lý được tạo ra từ VG. Logical Volume tương tự như các partition trên ổ cứng bình thường nhưng linh hoạt hơn vì kích thước của LV có thể được dễ dàng thay đổi theo thời gian thực mà không lo làm gián đoạn hệ thống. Sở dĩ ta có thể dễ dàng thay đổi được kích thước của LV vì LV được chia thành nhiều Logical Extent, mỗi Logical Extent này sẽ được mapping tương ứng với 1 Physical Extent trên các ổ đĩa.
  * **extent:** extent là đơn vị nhỏ nhất của VG. Mỗi một volume được tạo ra từ VG chứa nhiều extent nhỏ với kích thuớc cố định bằng nhau. Các extent trên LV không nhất thiết phải nằm liên tục với nhau trên ổ cứng vật lý bên dưới mà có thể nằm rải rác trên nhiều ổ cứng khác nhau. Extent chính là nền tảng cho công nghệ LVM, các LV có thể được mở rộng hay thu nhỏ lại bằng cách add thêm các extent hoặc lấy bớt các extent từ volume này.



Tóm lại, với công nghệ LVM ta có thể gộp nhiều ổ cứng vật lý Physical Volume lại thành Volume Group để tổng hợp toàn bộ tài nguyên lưu trữ cần thiết. Sau đó, người quản trị có thể chia nhỏ Volume Group ra thành các Logical Volume một cách tùy ý và linh hoạt. Mỗi một Logical Volume gồm nhiều extent, khi cần mở rộng Logical Volume thì ta thêm vào một số extent, khi cần thu nhỏ thì ta lấy lại một số extent.

**Ví dụ:** Theo hình ví dụ dưới đây, ta có một VG được tạo ra từ 3 PV. Trên đó, ta tạo ra 3 LV và có một LV chạy trên 2 PV khác nhau.

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-1.png)_

_Hình ảnh minh họa về LVM (Nguồn: Wikipedia)_

Một số tính năng cơ bản của LVM: 

  * Di chuyển LV giữa các PV khác nhau.
  * Thay đổi kích thước của VG online bằng cách gắn thêm hoặc tháo bớt PV.
  * Thay đổi kích thước của LV bằng cách thay đổi số lượng extent của PV này.
  * Tạo snapshot của các LV (giữ nguyên toàn bộ trạng thái của LV vào thời điểm đó).



# Thử nghiệm

(Link cheatsheet như mọi khi, bạn nào lười đọc có thể làm theo link này cho gọn: [GitHub](https://github.com/nduytg/System-Engineer-Cheat-Sheets/blob/master/Disk%20Management/LVM_Basics)) Để tìm hiểu rõ hơn về LVM, ta hãy cùng tạo thử phân vùng LVM mới qua 2 cách: 

  1. Tạo một VM chạy trên 2 ổ cứng dùng LVM để phân vùng đĩa.
  2. Add thêm đĩa cứng và tạo phân vùng mới cho VM đang chạy (hệ thống của ai đang chạy, cần add thêm disk chạy LVM thì kéo thẳng xuống mục 2 nhé ;) )



## Tạo phân vùng LVM khi tạo máy ảo mới

Trong phần này, ta sẽ tìm hiểu cách cài đặt mới hệ điều hành CentOS 6 trên LVM. Ở đây, ta sẽ cần hai ổ cứng cho VM để khởi tạo 2 PV, sau đó tạo một VG mới từ 2 PV này. ![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-2-1.png)

_Thông tin cấu hình máy ảo cài đặt trên VMware (hoặc VirtualBox)_

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-3.png)_

_Chọn_** _Basic Storage Devices_**

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-4.png)_

_Chọn ổ đĩa_** _sda_** _, chọn_** _Create_** _, chọn_** _LVM Physical Volume_**

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-5.png)__Chọn dung lượng tạo**Physical Volume**_

Ta chọn dung lượng để khởi tạo **PV** từ **sda** là **18000 MB** , làm tương tự như vậy với sdb. Ta sẽ có được _![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-6.png)_

_Tiếp tục tạo**Virtual Group** từ **2 Physical Volume** đã tạo_

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-7.png)__Ta cần khởi tạo thêm phân vùng_** _boot_** _từ dung lượng trống trên ổ đĩa_** _sda_**

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-8.png)_

_Khởi tạo phân vùng_** _swap_** _từ dung lượng trống của đĩa_** _sdb_**

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-9.png)_

_Kiểm tra cấu hình LVM trước khi cài đặt_

**Giải thích:** Ở đây, ta đã tạo ra **1 Volume Group** từ **2 Physical Volume**. Tiếp đó, ta tạo thêm **3 Logical Volume** từ **Volume Group** **vg_ducduy** , Volume Group này còn trống **5992 MB** (sẽ dùng ở phần kế tiếp). Ổ đĩa sda cũng còn **1979 MB** (cũng sẽ dùng ở phần kế tiếp).

Cấu hình LVM xong rồi thì bấm Next, Next cài CentOS như bình thường là được ;). Sau khi cài xong, ta truy cập vào máy ảo và kiểm tra lại thông tin các phân vùng LVM.

![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-10-1.png)_Kiểm tra cấu hình**LVM** sau khi cài đặt bằng lệnh **lvmdiskscan**_

## Cấu hình LVM khi hệ thống đang chạy

Ngoài việc cấu hình LVM khi mới cài đặt CentOS, ta cũng có thể cấu hình LVM khi hệ thống đã hoạt động ổn định và cần gắn thêm nhiều ổ cứng để mở rộng không gian lưu trữ. Trong phần ví dụ này, mình sẽ **add thêm 2 ổ cứng** là **sdb** và **sdc** , tạo **PV** , **LV** và mount các folder này lên hệ thống

![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-29.png)

_Kịch bản tạo Volume Group từ 2 Physical Volume_

Quy trình này gồm 4 bước:

  1. Khởi tạo Physical Volume từ đĩa cứng vật lý.
  2. Gộp các Physical Volume lại thành Volume Group.
  3. Tạo các Logical Volume từ Volume Group.
  4. Format và mount các Logical Volumes lên hệ thống.



Các bước thực hiện chi tiết như sau

### Khởi tạo Physical Volume từ đĩa cứng vật lý

Để khởi tạo 2 physical volume từ sdb, sdc, ta thực thi những lệnh sau: 
    
    
    # List physical disk
    lsblk
    
    # Create 2 physical volumes
    pvcreate /dev/sdb /dev/sdc
    
    # List physical volumes
    pvs

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-11.png)_

_Khởi tạo 2**Physical Volume** từ**sdb** , **sdc**_

### Gộp các Physical Volume lại thành Volume Group

Gộp 2 Physical Volume mới tạo lại thành **1 Volume Group** có tên **DucDuyVolGroup**
    
    
    # Create volume group
    vgcreate DucDuyVolGroup /dev/sdb /dev/sdc
    
    # List physical volumes
    pvs
    
    # List Volume Group
    vgs

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-12.png)_

_Tạo**Volume Group** từ **sdb** , **sdc**_

### Tạo các Logical Volume từ Volume Group

Tiếp đến, ta sẽ tạo 3 Logical Volume với tên gọi **project1** , **project2** và **db** từ DucDuyVolGroup với dung lượng lần lượt là (5G, 5G và 2G). 
    
    
    lvcreate -L 5G -n "project1" DucDuyVolGroup
    
    lvcreate -L 5G -n "project2" DucDuyVolGroup
    
    lvcreate -L 2G -n "db" DucDuyVolGroup
    
    vgs -o +lv_size,lv_name

  _![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-13.png)_

_Tạo**3 Logical Volume** từ **DucDuyVolGroup**_

### Format và mount các Logical Volumes lên hệ thống

Trước khi có thể sử dụng được 3 Logical Volume này, ta cần format lại các volume này theo chuẩn **ext4** và mount lên hệ thống. Format lại logical volume như sau: 
    
    
    mkfs.ext4 /dev/DucDuyVolGroup/project1
    
    mkfs.ext4 /dev/DucDuyVolGroup/project2
    
    mkfs.ext4 /dev/DucDuyVolGroup/db

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-14.png)_

_**Format** lại các **logical volume**_

Tạo 3 mount points và mount các logical volume này lên 
    
    
    mkdir -p /mnt/{project1,project2,db}
    
    mount /dev/DucDuyVolGroup/project1 /mnt/project1
    
    mount /dev/DucDuyVolGroup/project2 /mnt/project2
    
    mount /dev/DucDuyVolGroup/db /mnt/db

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-15.png)_

_**Mount** 3 logical volume lên hệ thống_

Cấu hình file **/etc/fstab** để hệ thống**tự động mount volume** khi server khởi động lại 
    
    
    vi /etc/fstab
    
    ...
    
    /dev/DucDuyVolGroup/project1 /mnt/project1 ext4 defaults,nofail 0 0
    /dev/DucDuyVolGroup/project2 /mnt/project2 ext4 defaults,nofail 0 0
    /dev/DucDuyVolGroup/db /mnt/db ext4 defaults,nofail 0 0

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-16.png)_

_Chỉnh sửa file**/etc/fstab**_

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-17.png)_

_Kiểm tra lại kết quả_

Vậy là xong phần cơ bản, các bạn có thể xài 3 volume mới tạo thoải mái theo ý thích, y như ổ cứng bình thường thôi. 

### Thay đổi kích cỡ logical volume

**Tăng kích cỡ logical volume không downtime** Để tăng kích cỡ logical volume **_db_** lên 1Gb mà _**không downtime**_ , ta dùng lệnh sau: 
    
    
    lvscan
    
    lvresize -L +1G --resizefs DucDuyVolGroup/db

Khi ta tăng kích thước của phân vùng này lên 1GB, ta cũng phải tăng kích thước của filesystem lên 1Gb bằng cờ **_\--resizefs_**

![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-18-1.png)

_Tăng kích thước phân vùng_** _db_** _theo thời gian thực thêm 1GB và không làm mất dữ liệu_

**Giảm kích thước logical volume "có downtime"** Khác với khi tăng kích cỡ logical volume, khi ta giảm kích thước của logical volume thì ta cần phải unmout phân vùng này (gây downtime) và _**có thể gây ra mất dữ liệu**_ trong quá trình giảm dung lượng của phân vùng. Để giảm kích thước phân vùng project2 từ 5GB xuống 4GB, ta thực hiện các lệnh sau: 
    
    
    umount /dev/DucDuyVolGroup/project2
    
    fsck -t ext4 -f /dev/DucDuyVolGroup/project2
    
    resize2fs -p /dev/DucDuyVolGroup/project2 4G
    
    lvresize -L 4G DucDuyVolGroup/project2
    
    fsck -t ext4 -f /dev/DucDuyVolGroup/project2
    
    mount /dev/DucDuyVolGroup/project2 /mnt/project2

Khi ta unmout 1 logical volume, ta cần phải kiểm tra lại filesystem bằng lệnh **_fsck._** Ngoài ra, ta còn cần phải resize lại filesystem bằng lệnh **_resize2fs_** _![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-19.png)_

_**Resize** lại 1 Logical Volume_

# Tìm hiểu về LVM Thin Provisioning

## Giới thiệu

Thin Provisioning là tính năng cấp phát ổ cứng dựa trên sự linh hoạt của LVM. Giả sử ta có một **Volume Group** , ta sẽ tạo ra **1 Thin Pool** từ **VG** này với dung lượng là 20GB cho nhiều khách hàng sử dụng. Giả sử ta có 3 khách hàng, mỗi khách hàng được cấp 6GB lưu trữ. Như vậy ta có 3 x 6GB là 18GB. Với kỹ thuật cấp phát truyền thống thì ta chỉ có thể cấp phát thêm 2GB cho khách hàng thứ 4.

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-20.png)_

_So sánh giữa cách cấp phát truyển thống so với**Thin Provisioning** (Nguồn: VMware)_

Nhưng với kỹ thuật Thin Provisioning, ta vẫn có thể cấp thêm 6GB nữa cho khách hàng thứ 4. Tức là 4 x 6GB = 24GB > 20GB lúc đầu. Sở dĩ ta có thể làm được như vậy là do mỗi user tuy được cấp 6GB nhưng thường thì họ sẽ không xài hết số dung lượng này (nếu 4 khách hàng đều xài hết thì ta sẽ gặp tình trạng Over Provisioning). Ta sẽ giả dụ là họ không xài hết dung lượng được cấp thì trên danh nghĩa mỗi người sẽ được 6GB, nhưng thực tế thì họ xài đến đâu, hệ thống sẽ cấp thêm dung lượng đến đó.

Đối với cơ chế cấp phát bình thường thì LVM sẽ cấp phát 1 dãy block liên tục mỗi khi người dùng tạo ra 1 volume mới. Nhưng với cơ chế thin pool thì LVM chỉ sẽ cấp phát các block ổ cứng (là một tập hợp các con trỏ, trỏ tới ổ cứng) khi có dữ liệu thật sự ghi xuống đó. Cách tiếp cận này giúp tiết kiệm dung lượng cho hệ thống, tận dụng tối ưu dung lượng lưu trữ. Tuy nhiên, khuyết điểm là có thể gây phân mảnh hệ thống và gây ra tình trạng Over Provisioning như đã nói ở trên. 

## Cách thức thực hiện

Sau đây là kịch bản demo Thin Provisioing + Over Provisioning trên LVM 

  1. Tạo một **Virtual Volume** từ 2 đĩa **sdb** và **sdc**.
  2. Tạo một **Thin Pool** (thực chất là tạo một **Logical Volume** với cờ **\--thinpool**).
  3. Tạo 4 **Thin Volume** (thực chất vẫn là **Logical Volume**) cho 4 user. 
     1. Tạo **filesystem** cho 4 Volume này.
     2. Tạo **mount point** và mount 4 volume này.
  4. Mở rộng **Thin Pool**.



# ![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-21.png)

_Kịch bản tạo**Thin Pool** và **Thin Volume**_

### Tạo Virtual Volume

Khởi tạo Virtual Volume từ 2 đĩa sdb và sdc với tổng dung lượng là 20GB (10GB + 10GB): 
    
    
    vgcreate DuyThinVolGroup /dev/sdb /dev/sdc
    pvg
    vgs

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-22.png)_

_Khởi tạo**VG** từ 2 disk_

### Tạo Thin Pool

Khởi tạo 1 Thin Pool với dung lượng là 18GB (phải có cờ --thinpool): 
    
    
    lvcreate -L 18G --thinpool "DuyThinPool" DuyThinVolGroup
    
    vgs -o +lv_size,lv_name
    
    lvdisplay

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-23.png)_

_Khởi tạo 1**Thin Pool** từ **VG** đã tạo_

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-24.png)_

_Thông tin của**Thin Pool** vừa tạo_

### Tạo các Thin Volumes

Tạo 4 Thin Volume cho các user, mỗi Volume có dung lượng là 6G (6*6 = **24GB** **>** **18GB**). Đây chính là **Over Provisioning** dựa trên **Thin Provisioning**
    
    
    lvcreate -V 6G --thin -n "Thin_User1" DuyThinVolGroup/DuyThinPool
    
    lvcreate -V 6G --thin -n "Thin_User2" DuyThinVolGroup/DuyThinPool
    
    lvcreate -V 6G --thin -n "Thin_User3" DuyThinVolGroup/DuyThinPool
    
    lvcreate -V 6G --thin -n "Thin_User4" DuyThinVolGroup/DuyThinPool

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-25.png)_

_Hệ thống cảnh báo khi ta cấp các Volume**nhiều hơn tổng dung lượng** của Thin Pool _

_~~(nhưng quan tâm làm gì, kemeno)~~_

Format 4 volume này về định dạng **ext4**
    
    
    mkfs.ext4 /dev/DuyThinVolGroup/Thin_User1
    
    mkfs.ext4 /dev/DuyThinVolGroup/Thin_User2
    
    mkfs.ext4 /dev/DuyThinVolGroup/Thin_User3
    
    mkfs.ext4 /dev/DuyThinVolGroup/Thin_User4

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-26.png)_

_Format các**Thin Volume** mới tạo_

Tạo **mount point** và mount 4 volume này lên hệ thống 
    
    
    mkdir -p /mnt/{user1,user2,user3,user4}
    
    mount /dev/DuyThinVolGroup/Thin_User1 /mnt/user1
    
    mount /dev/DuyThinVolGroup/Thin_User2 /mnt/user2
    
    mount /dev/DuyThinVolGroup/Thin_User3 /mnt/user3
    
    mount /dev/DuyThinVolGroup/Thin_User4 /mnt/user4
    
    mount
    
    df -h
    lvdisplay DuyThinVolGroup

![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-27.png)

_Các**Thin Volume** đã được **mount** lên hệ thống_

Cái này chỉ là mount tạm thời, nếu muốn set mount vĩnh viễn thì ta cần phải chỉnh **lại file fstab** giống như phần trên ;) 

### Mở rộng Thin Pool

Về bản chất, Thin Pool vẫn là 1 Logical Volume nên ta có thể dễ dàng mở rộng Thin Pool, miễn là Volume Group chứa nó vẫn còn dung lượng trống: 
    
    
    lvscan
    
    lvresize -L +1G DuyThinVolGroup/DuyThinPool
    
    hoặc
    
    lvextend -L +1G DuyThinVolGroup/DuyThinPool

_![](https://cloudcraft.info/wp-content/uploads/2018/09/LVM-28.png)_

_Mở rộng**Thin Pool** thêm 1GB_

# Tham khảo

<https://www.digitalocean.com/community/tutorials/how-to-use-lvm-to-manage-storage-devices-on-ubuntu-16-04> <https://en.wikipedia.org/wiki/Logical_Volume_Manager_(Linux)> <https://www.tecmint.com/setup-thin-provisioning-volumes-in-lvm/> <https://pve.proxmox.com/wiki/Storage:_LVM_Thin> <http://blog-vpodzime.rhcloud.com/?p=83> <https://www-rhstorage.rhcloud.com/blog/vpodzime/lvm-thin-provisioning>  
