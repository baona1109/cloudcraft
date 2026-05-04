---
title: "Hướng dẫn toàn tập về partition trên Linux"
date: 2017-12-04 17:43:39
categories: [Linux]
---

Trong bài viết này, mình sẽ giới thiệu các bạn các bước để gắn thêm một ổ cứng mới vào server Linux. Đối với những bạn đã nắm vững kiến thức về partition thì các bạn có thể lướt qua thẳng tới mục hướng dẫn ở dưới, nếu các bạn chưa vững lắm thì có thể cùng ôn tập về partition với mình ở mục đầu tiên này. Link cheat-sheet cho bạn nào lười đọc^^: [GitHub](https://github.com/cloudcraftteam/System-Engineer-Cheat-Sheets/blob/master/Disk%20Management/Partitioning_Basics)

# Giới thiệu về partition

Partition là những phân vùng nhỏ (phân vùng logic) được chia ra từ 1 ổ cứng vật lý. Một ổ cứng có thể có 1 hoặc nhiều partition. Partition là cách phân chia và quản lý một ổ cứng đơn giản và hiệu quả (chẳng hạn như phân ra 1 vùng quan trọng để chứa dữ liệu của hệ điều hành và 1 phân vùng để chứa phim, nhạc). Dữ liệu trên 1 partition A sẽ được phân tách với dữ liệu trên partition B, mọi thao tác trên partition này sẽ không ảnh hưởng đến partition kia (trừ khi ổ cứng chung bị hư). Hiện có 3 loại partition chính là: _primary, extended và logical_. 

  * **Primary partition:** đây là những phân vùng có thể được dùng để boot hệ điều hành
  * **Extended partition:** là vùng dữ liệu còn lại khi ta đã phân chia ra các primary partition, extended partition chứa các logical partition trong đó. Mỗi một ổ đĩa chỉ có thể chứa 1 extended edition.
  * **Logical partition:** các phân vùng nhỏ nằm trong extended partition, thường dùng để chứa dữ liệu.



![](https://cloudcraft.info/wp-content/uploads/2017/12/Huong-dan-toan-tap-ve-partition-tren-Linux-1.png)

_Ổ đĩa trên Windows dùng MBR hỗ trợ tối đa 4 Primary Partitions_

## MBR vs GPT

Thông tin về các partition của ổ cứng sẽ được lưu trữ trên MBR (Master Boot Record) hoặc GPT (GUID Partition Table) tùy loại ổ cứng hỗ trợ. Đây là 2 chuẩn cấu hình và quản lý các partition trên ổ cứng. Thông tin được lưu trữ trên đây gồm vị trí và dung lượng của các partition. **MBR** MBR là chuẩn phân chia ổ đĩa truyền thống, một ổ đĩa sẽ được chia thành các vùng nhỏ (sector) với dung lượng bằng nhau là 512 bytes. Trên Linux, một ổ đĩa cứng được chia thành nhiều partition với số hiệu như sau: _/dev/hda1_ , _/dev/hda2_ , _/dev/sda1_ , _/dev/sda2_ , _/dev/sdb1_ ,… Ta có thể dùng lệnh **_fdisk_** hoặc **_parted_** để hiển thị thông tin về ổ đĩa dùng MBR trên Linux. ![](https://cloudcraft.info/wp-content/uploads/2017/12/Huong-dan-toan-tap-ve-partition-tren-Linux-2.png)

_Lệnh fdisk để kiểm tra các ổ đĩa dùng MBR_

Đối với các ổ cứng kiểu cũ chỉ hỗ trợ MBR thì ta chỉ được phép có tối đa 4 primary partition trên 1 ổ cứng, extended partion cũng được coi là 1 primary partition. Toàn bộ các thông tin về partition sẽ được lưu trữ ở 512 bytes đầu tiên trên ổ đĩa vật lý (sector đầu tiên của ổ đĩa), sector này có tên là Master Boot Record. **GPT** GPT là chuẩn mới hơn, hỗ trợ đến 128 phân vùng trên 1 ổ đĩa vật lý. Thông tin về các partition sẽ được ghi thành nhiều bản rải rác khắp ổ vật lý. GPT hỗ trợ cơ chế kiểm tra và chỉnh sửa dữ liệu dựa trên CRC (cyclic redundancy check). Nhờ 2 cơ chế này, chuẩn GPT làm giảm tỷ lệ mất mát dữ liệu. Ngoài ra, nếu ta cần khởi tạo một phân vùng với dung lượng lớn hơn 2TB, ta sẽ phải dùng GPT vì MBR không trợ dung lượng lớn hơn 2 TB. Ta có thể dùng lệnh **_gdisk_** hoặc **_parted_** để kiểm tra các ổ đĩa dùng GPT. 

![](https://cloudcraft.info/wp-content/uploads/2017/12/Huong-dan-toan-tap-ve-partition-tren-Linux-3.png)

_Cấu hình GPT cho ổ đĩa và kiểm tra ổ đĩa này_

## Giới thiệu về file system

Mỗi một partition sẽ cần có một filesystem riêng. Filesystem là cách thức lưu trữ và tìm kiếm dữ liệu trên một partition. Một số file system thông dụng được hỗ trợ trên Linux gồm: 

  * File system trên đĩa cứng: ext2, ext3, ext4 (hiện đang rất thông dụng trên các hệ điều hành Linux mới), Btrfs, JFS, NTFS, xfs…
  * File system trên thẻ nhớ flash: ubifs, JFFS2, YAFFS,…
  * File system của các loại database.
  * File system đặc biệt: procfs, sysfs, tmpfs, debugfs,…



## Hướng dẫn cấu hình cho một ổ cứng mới

Quy trình lắp mới một thiết bị lưu trữ gồm những bước sau: 

  1. Xác định chuẩn lưu thông tin về partition (MBR, GPT).
  2. Gắn một ổ cứng mới vào server.
  3. Chia partition cho ổ cứng.
  4. Khởi tạo file system (ext4, xfs...) cho partition vừa mới được tạo.
  5. Mount partition lên hệ thống.
  6. Cấu hình auto mount partition khi reboot.

Ở phần demo này, ta sẽ dùng lệnh parted để chuyển đổi chuẩn MBR và GPT, sau đó là tạo partition và mount partition mới tạo lên hệ thống. Dùng lệnh sau để chuyển đổi giữa 2 loại chuẩn: 
    
    
    parted /dev/sdb mklabel gpt
    #OR
    parted /dev/sdb mklabel msdos

  ![](https://cloudcraft.info/wp-content/uploads/2017/12/Huong-dan-toan-tap-ve-partition-tren-Linux-4.png)

_Tạo một primary partition mới với toàn bộ dung lượng của ổ /dev/sdb_
    
    
    parted /dev/sdb mkpart primary ext4 0% 100%

  ![](https://cloudcraft.info/wp-content/uploads/2017/12/Huong-dan-toan-tap-ve-partition-tren-Linux-5.png)

_Tạo filesystem ext4 cho phân vùng mới tạo_
    
    
    mkfs.ext4 -L datapartition /dev/sdb1

  ![](https://cloudcraft.info/wp-content/uploads/2017/12/Huong-dan-toan-tap-ve-partition-tren-Linux-6.png)

_Tạo mount point và mount phân vùng vừa tạo được lên đó để sử dụng_
    
    
    mkdir -p /mnt/data
    mount /dev/sdb1 /mnt/data
    mount | grep /dev/sdb1

  ![](https://cloudcraft.info/wp-content/uploads/2017/12/Huong-dan-toan-tap-ve-partition-tren-Linux-7.png)

_Kiểm tra lại phân vùng vừa khởi tạo_
    
    
    lsblk --fs

  Tuy nhiên, với cách mount này, mỗi khi reboot, hệ thống sẽ không tự động mount lại phân vùng đó. Để cấu hình automount cho các phân vùng này, ta cần thiết lập trong file **_/etc/fstab_** ![](https://cloudcraft.info/wp-content/uploads/2017/12/Huong-dan-toan-tap-ve-partition-tren-Linux-8.png)

_Nội dung file_** _/etc/fstab_** _mặc định_

Mỗi dòng trong file này quy định cách thức mount các partition trên hệ thống và những option cần thiết để mount các partition đó. Mỗi dòng trong file tuân theo format sau: 
    
    
    <Device> <Mount Point> <File System Type> <Options> <Dump> <Pass>

Trường | Ý nghĩa  
---|---  
<device> | Vị trí của thiết bị/phân vùng cần mount.  
<mount point> | Vị trí được mount lên.  
<file system type> | Loại filesystem (vfat, ntfs, ext2, ext3, ext4, jfs, xfs, swap, udf, iso9660, auto…)  
<options> | 

  * **default** gồm các option sau: **_rw, suid, dev, exec, auto, nouser, async_**
  * **sync/async** : sync – ghi xuống disk rồi mới chạy tiếp chương trình, async – “vờ như” đã ghi xuống đĩa (thật sự là ghi tạm lên buffer, lâu lâu mới ghi xuống đĩa 1 lần), chương trình có thể hoạt động tiếp mà không cần chờ ghi đĩa.
  * **auto/noauto** : tự động/không tự động mount khi boot hệ thống.
  * **dev/nodev:** dùng/không dùng special device (block, character) device
  * **exec/noexec** : cho phép/chặn không cho thực thi các file nhị phân trên filesystem.
  * **suid/nosuid** : cho phép/không cho phép dùng các bit SUID, SGID
  * **ro** : partition/thiết bị được mount với mode read-only.
  * **rw** : partition/thiết bị được mount với mode read-write.
  * **user** : cho phép _user_ nào đó tương tác với partition được mount (mặc định là đi kèm với các option: noexec, nosuid, nodev).
  * **noouser** : chỉ cho phép user root tương tác với thiết bị được mount.
  * **_netdev:** xác định rằng đây là một thiết bị mạng, chỉ mount thiết bị này khi mạnh đã được start lên (dùng với filesystem nfs).

  
<dump> | Bật/tắt tính năng backup filesystem. Tính năng này ít khi được dùng, giá trị mặc định là 0 (tắt), giá trị 1 là bật.  
<pass num/fsck order> | Thông số này quyết định thứ tự mà lệnh fsck (filesytem check) thực thi với các partition được mount lúc boot hệ thống. 

  * 0 == không check filesystem.
  * 1 == check partition này đầu tiên.
  * 2 == check partition này sau partition đầu tiên.

Giá trị “1” được dùng cho root (/) partition, các partition còn lại sẽ được gán giá trị 2 (nếu cần check filesystem).  
Ví dụ: Tự động mount partition sdb 1 lên thư mục /mnt/data mỗi khi reboot 
    
    
    vim /etc/fstab
    […]
    /dev/sdb1 /mnt/data ext4 defaults 0 0

Reboot và kiểm tra lại bằng lệnh _**lsblk**_ ![](https://cloudcraft.info/wp-content/uploads/2017/12/Huong-dan-toan-tap-ve-partition-tren-Linux-9.png)

_Partition_** _/dev/sdb1_** _đã được tự động mount lên hệ thống khi boot_

Các bạn có thể đọc thêm một số bài viết liên quan tại đây: 

  * [Giới thiệu về LVM trên Linux](https://cloudcraft.info/gioi-thieu-ve-lvm-logical-volume-management/)



# Tham khảo

<https://wiki.archlinux.org/index.php/partitioning> <https://www.ibm.com/developerworks/library/l-lpic1-v3-104-1/index.html> <https://www.digitalocean.com/community/tutorials/how-to-partition-and-format-storage-devices-in-linux> <https://www.tecmint.com/parted-command-to-create-resize-rescue-linux-disk-partitions/> <http://www.linuxstall.com/fstab/>
