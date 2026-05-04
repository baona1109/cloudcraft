---
title: "Hướng dẫn Upgrade Kernel Linux."
date: 2018-04-18 21:46:58
categories: [Linux]
---

Như các bạn đã biết, một số tools hoặc utilities cần có một số gói cài đặt khác đi kèm. Tuy nhiên những gói cài đặt đi kèm đó lại không có hoặc không được hỗ trợ bởi Kernel OS hiện tại của bạn (ví dụ Kernel release quá thấp). Nên tôi sẽ hướng dẫn các bạn Upgrade Kernel để cài đặt các tools đó.

Điển hình như sau. Tôi muốn cài Docker CE trên CentOS 6. Và vấp phải cái lỗi như thế này:

![](https://cloudcraft.info/wp-content/uploads/2018/04/upgrade-kernel-1.png)

Và vấn đề chính là vì Kernel release của tôi quá thấp (bạn xem link sau để hiểu vì sao: https://docs.docker.com/install/linux/docker-ce/debian/#os-requirements), release hiện tại của tôi là **2.6.32-696.16.1.el6.x86_64**

![](https://cloudcraft.info/wp-content/uploads/2018/04/upgrade-kernel-2-1.png)

Đồng ý là tôi có thể sử dụng các phiên bản Docker cũ hơn như docker-io hoặc dùng Ubuntu thay thế. Nhưng vì tôi khá lỳ, vẫn muốn cài Docker trên cái máy chạy CentOS này thì làm thế nào cho đơn giản?

Vâng, chỉ có thể upgrade kernel. Miễn là tôi nâng cấp kernel lên ít nhất release >= 3.10 là ổn. Vậy, tôi quyết định là sẽ nâng cấp cho mình "kernel release 4.15" mà không làm mất dữ liệu trên con máy CentOS này. Sau đây là các bước tôi thực hiện:

**Bước 1: Tạo môi trường build kernel**

Để upgrade kernel mới. Tôi tải phiên bản 4.15 từ kernel.org về, bạn có thể tải phiên bản khác nếu muốn.
    
    
    yum install xz -y
    
    wget https://cdn.kernel.org/pub/linux/kernel/v4.x/linux-4.15.4.tar.xz
    
    tar -Jxvf linux-4.15.4.tar.xz
    
    cd linux-4.15.4

Giải thích:

1\. Tôi cài đặt xz để giải nén định dạng xz. 2\. https://cdn.kernel.org/pub/linux/kernel/v4.x/linux-4.15.4.tar.xz là đường dẫn tôi tải kernel source về máy. 3\. Sau đó tôi giải nén và truy xuất vào thư mục chưa source.

**Bước 2: Cài đặt một số công cụ để build kernel**

Tôi cài một số công cụ cần có để build kernel. Lý do tôi biết các gói nào cần thì đơn giản thôi, tôi cứ make source một hồi. Báo lỗi cái nào tôi cài cái đó.
    
    
    yum install gcc -y
    
    yum install ncurses ncurses-devel -y
    
    yum install openssl-devel bc -y
    
    yum install libssl-dev bc libelf-dev elfutils-libelf-devel -y
    
    make mrproper

Giải thích:

1\. Cài gcc vì kernel được viết bằng C. 2\. Mấy cái ncurses, ncurses-devel... thì khi build có cần mấy gói này. 3\. **make mrproper** ở đây là đưa source kernel về trạng thái gốc của mã nguồn khi lần đầu unpacked (vì nếu bạn đã make trước đó thì cấu trúc mã nguồn có thể đã bị thay đổi, make lại lần nữa có khả năng lỗi)

**Bước 3: Tạo config**

Khi bạn build lại kernel. Có 1 điểm quan trọng chính là các module mà bạn muốn hỗ trợ trong kernel mới. Bạn có thể tùy chỉnh hoặc lựa chọn những gì bạn muốn. Từ việc lựa chọn các Device Drivers, Network, File system... cho đến Security options hay Processor type. Các module này bạn nên để mặc định những gì đang có trên kernel cũ. Nếu bạn có hiểu biết thì có thể loại bỏ không build cùng kernel mới hay muốn thêm thì "Select".

Với tôi thì sẽ để default những gì đang có. Save .config và Exit
    
    
    make menuconfig

![](https://cloudcraft.info/wp-content/uploads/2018/04/upgrade-kernel-3.png)

Sau khi Save thành công bạn sẽ có một file .config.

![](https://cloudcraft.info/wp-content/uploads/2018/04/upgrade-kernel-4.png)

**Bước 4: Build từ source kernel với config mới.**

Với những config đã tạo ở trên. Tôi sẽ build các module, kernel chính và sau đó cài đặt. Việc cài đặt kernel (nếu build đã thành công) sẽ tạo một image mới, nếu gặp vấn đề khi boot vào kernel mới thì bạn dùng Recsue mode để chỉnh lại boot loader. Vì sao thì bạn vui lòng tìm hiểu thêm cách kernel được load.
    
    
    make
    
    make modules
    
    make modules_install
    
    make install

Bước build sẽ khá lâu, nếu bạn dùng remote login thì nên đặt trong screen kẻo văng session thì phải build lại từ đầu.

Trước khi build thì tôi chỉ có initramfs-2.6 và vmlinuz-2.6, đây chính là kernel được load hiện tại.

![](https://cloudcraft.info/wp-content/uploads/2018/04/upgrade-kernel-5.png)

Nhưng sau khi **make install** thì sẽ có image kernel mới:

![](https://cloudcraft.info/wp-content/uploads/2018/04/upgrade-kernel-6.png)

Đồng thời trong boot loader cũng đã thêm kernel tôi mới cài đặt vào. Bạn để ý thông số default=1 tức là sẽ lựa chọn title = 1 để hiển thị mặc định nếu bạn không nhấn ESC để ra tùy chọn boot. Bạn nên để default=1 như trên, vì nếu để bằng 0 mà boot vào luôn kernel mới build nằm ở đầu danh sách thì sẽ không ổn đâu. (Thứ tự bắt đầu đánh số từ 0)

![](https://cloudcraft.info/wp-content/uploads/2018/04/upgrade-kernel-7.png)

Bây giờ tôi sẽ reboot -> Esc để xem kết quả thì đã có kernel tôi mới build xong. Tôi sẽ thử chọn kernel mới này và boot vào.

![](https://cloudcraft.info/wp-content/uploads/2018/04/upgrade-kernel-8.png)

Và mọi thứ đã thành công, giờ tôi vẫn giữ được giữ liệu cũ cũng như có kernel mới để cài đặt những thứ tôi muốn.

![](https://cloudcraft.info/wp-content/uploads/2018/04/upgrade-kernel-9.png)

Các bạn lưu ý là Upgrade kernel chỉ làm được với VM, KVM hoặc máy host. Không dùng được với Container sử dụng chung kernel. Có thể dùng được với Linux Container + Hyper V Isolated trên Windows.
