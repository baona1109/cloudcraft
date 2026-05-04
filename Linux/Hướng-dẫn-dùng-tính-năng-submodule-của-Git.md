---
title: "Hướng dẫn dùng tính năng submodule của Git"
date: 2020-06-25 10:00:51
categories: [Uncategorized, Linux, Programming]
---

# **Hướng dẫn dùng tính năng submodule của Git**

Trong môi trường phát triển phần mềm, phát triển các dự án lớn, chúng ta không thể gôm tất cả vào một repository trên gitlab hay github được, mà chia thành nhiều module lớn nhỏ khác nhau rồi trong project chính sẽ tiến hành clone source từ các module đó về để chạy. Có nhiều cách để quản lý vấn đề trên, trong bài viết này mình sẽ hướng dẫn các bạn sử dụng một tính năng mà git cung cấp sẵn đó là submodule. 

## **Thao tác với submodule**

Mình sẽ đi qua với các lệnh cơ bản để làm việc trên submodule và sẽ có ví dụ ở gần cuối bài để các bạn dễ hình dun Để add một module hay nói cách khác là 1 repo khác vào làm submodule cho project hiện hành mình sử dụng lệnh sau: 
    
    
    git submodule add <URL repo.git> <path_to_submodule_folder>

**Trong đó:**

  * <URL repo.git>: Đường dẫn để bạn git clone được repo về máy
  * <path_to_submodule_folder>: Đường dẫn local trên máy tới folder để chứa submodule, nếu path chưa có nó sẽ tự tạo

Khi mà bạn git clone project của bạn trên 1 máy khác, thì nó sẽ không tự động clone các submodule đã add xuống theo mà bạn cần phải chạy update và pull các submodule về. 
    
    
    git submodule update --init
    git submodule update --recursive --remote
    git pull --recurse-submodules

Một cái hay của việc dùng submodule là dễ dàng đồng bộ source code trên repository, bạn không cần phải clone module về để sửa, sau đó copy vào project của bạn, việc quản lý sẽ rắc rối hơn khi dự án lớn dùng nhiều submodule. Khi bạn sử dụng git submodule như vậy thì các submodule này sẽ tương đương với bạn đã clone sẵn các module về, bạn có thể chỉnh sửa code trên đó và tiến hành git commit, git push lên repository. Sau đó chỉ việc ra ngoài folder gốc của project tiến hành commit. Vậy khi submodule đó không sử dụng, hoặc bị lỗi cần add lại thì ta phải remove như thế nào? Chạy các lệnh sau đây theo trình tự để remove submodule 
    
    
    git submodule deinit <path_to_submodule_folder> -f
    git rm <path_to_submodule_folder>
    git commit -m "Remove submodule" .
    rm -rf .git/modules/<path_to_submodule_folder>

## **Ví dụ tham khảo**

Sau đây là ví dụ để các bạn dễ hình dung: 

  * 1 project ansible-monitor: Sẽ là project để run setup các dịch vụ cho monitor, project này tập hợp ansible-roles để sử dụng cho project
  * 4 repo ansible roles cơ bản cho setup server monitor: 
    * ansible-role-grafana: Chứa role để setup grafana
    * ansible-roles-prometheus: Chứa role để setup prometheus
    * ansible-roles-exporters: Chứa role để setup các exporters dành cho prometheus
    * ansible-role-alertmanager: Chứa role để setup alertmanager

Trước tiên clone project ansible-monitor về 
    
    
    git clone https://gitlab.cloudcraft.info/mytest/ansible-monitor.git
    cd ansible-monitor/

Để add 4 repo ansible-role-grafana, ansible-role-prometheus, ansible-role-exporters vào làm submodule ta chạy các lệnh sau 
    
    
    git submodule add https://gitlab.cloudcraft.info/ansible/ansible-role-grafana.git roles/ansible-role-grafana/
    git submodule add https://gitlab.cloudcraft.info/ansible/ansible-roles-prometheus.git roles/ansible-roles-prometheus/
    git submodule add https://gitlab.cloudcraft.info/ansible/ansible-roles-exporters.git roles/ansible-roles-exporters/
    git submodule add https://gitlab.cloudcraft.info/ansible/ansible-role-alertmanager.git roles/ansible-role-alertmanager/
    

Sau khi chạy 4 lệnh trên, trong project ansible-monitor sẽ tạo thêm 1 folder roles, trong folder roles sẽ chứa 4 repo trên, sau khi add thì sẽ có 1 file .gitmodules được tạo, chứa thông tin các submodules Cấu trúc folder sau khi add submodule ![](https://cloudcraft.info/wp-content/uploads/2020/04/huong-dan-dung-tinh-nang-submodule-cua-git-1.png) Folder roles được tạo và chứa các submodule ![](https://cloudcraft.info/wp-content/uploads/2020/04/huong-dan-dung-tinh-nang-submodule-cua-git-2.png) Thông tin file .gitmodules ![](https://cloudcraft.info/wp-content/uploads/2020/04/huong-dan-dung-tinh-nang-submodule-cua-git-3.png) Thông tin file .git/config ![](https://cloudcraft.info/wp-content/uploads/2020/04/huong-dan-dung-tinh-nang-submodule-cua-git-4.png) Ngoài ra lệnh git submodule còn nhiều tính năng nữa, các bạn có thể tham khảo thêm với lệnh help 

## **Tham khảo:**

<https://git-scm.com/book/en/v2/Git-Tools-Submodules> <https://cloudcraft.info/huong-dan-cai-dat-gitbucket-tren-centos-7/>
