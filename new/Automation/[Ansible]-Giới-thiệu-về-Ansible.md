---
title: "[Ansible] Giới thiệu về Ansible"
date: 2019-03-19 10:16:49
categories: [Automation, Ansible, Linux]
---

# Giới thiệu về Ansible

![ansible-wide](https://cloudcraft.info/wp-content/uploads/2019/03/ansible-wide.png)  

Trong một môi trường với nhiều server thì ta sẽ có vô vàn thứ phải lo. Từ setup crontab, update các gói phần mềm mới, deploy ứng dụng mới, chỉnh sửa file cấu hình.... Những công việc này tuy không khó, nhưng rất mất thời gian của những người quản trị (SysAdmin/SysEngineer/DevOp). Vậy có cách nào để tự động hóa những thao tác nhàm chán, lặp đi lặp lại này không?

_"Cùng một việc mà làm quá 2 lần thì có gì đó không ổn rồi" Từ một giảng viên cũ từng dạy mình_

Câu trả lời chính là ứng dụng những tool automation để quản trị hệ thống. Hiện nay có rất nhiều tool như vậy trên thị trường cụ thể như: Chef, Puppet, CFEngine, StackStorm, Ansible, SaltStack... Trong bài này, mình sẽ giới thiệu đến các bạn một công cụ rất mạnh mẽ trong việc quản trị hệ thống, đó chính là **Ansible**.

## Giới thiệu Ansible

Như đã nói ở trên, Ansible là một công cụ dùng để tự động hóa việc cấu hình trên nhiều server. So với các công cụ khác với tính năng tương đương thì Ansible dễ học và dễ tiếp cận hơn rất nhiều. Cộng đồng người dùng cũng nhiều hơn so với các công cụ khác.

So sánh một vài thông số về **Ansible, SaltStack, Chef, Puppet** trên **GitHub (11/2018)**

| **Ansible** | **SaltStack** | **Chef** | **Puppet**  
---|---|---|---|---  
**Số sao trên GitHub** | 33,500 | 9,341 | 5,543 | 5,125  
**Số lượt fork** | 13,338 | 4,366 | 2,268 | 2,049  
**Ngôn ngữ lâp trình** | Python | Python | Ruby | Ruby  
**Ngôn ngữ cấu hình** | YAML | YAML | Ruby DSL | Puppet DSL  
 

Có thể thấy Ansible là công cụ tự động hóa phổ biến nhất trên GitHub với số sao được người dùng bình chọn cho project này là 33,500 sao. Ansible cũng là tool dễ tiếp cận và làm quen do được build bằng Python và sử dụng file cấu hình theo dạng YAML (**Y** AML **A** in’t **M** arkup **L** anguage) dễ đọc và dễ hiểu.

### Kiến trúc

Ansible sử dụng kiến trúc agentless để giao tiếp với các máy khác mà không cần agent. Cơ bản nhất là giao tiếp thông qua giao thức SSH trên Linux, WinRM trên Windows hoặc giao tiếp qua chính API của thiết bị đó cung cấp.

![gioi-thieu-ansible-2](https://cloudcraft.info/wp-content/uploads/2018/10/gioi-thieu-ansible-2-1024x578.png)

_Kiến trúc của Ansible_

Ansible có thể giao tiếp với rất nhiều platform, OS và loại thiết bị khác nhau. Từ Ubuntu, CentOS, VMware, Windows cho tới AWS, Azure, các thiết bị mạng Cisco và Juniper....vân vân và mây mây....(hoàn toàn không cần agent khi giao tiếp).

Chính cách thiết kế này làm tăng tính tiện dụng của Ansible do không cần phải setup bảo trì agent trên nhiều host. Có thể coi đây là một thế mạnh của Ansible so với các công cụ có cùng chức năng như Chef, Puppet, SaltStack (Salt thì hỗ trợ cả 2 mode là agent và agentless, có thời gian thì mình sẽ viết 1 bài về Salt).

### Ứng dụng

Ansible có rất nhiều ứng dụng trong triển khai phần mềm và quản trị hệ thống. 

  * **Provisioning:** Khởi tạo VM, container hàng loạt trong môi trường cloud dựa trên API (OpenStack, AWS, Google Cloud, Azure...)
  * **Configuration Management:** Quản lý cấu hình tập trung các dịch vụ tập trung, không cần phải tốn công chỉnh sửa cấu hình trên từng server.
  * **Application Deployment:** Deploy ứng dụng hàng loạt, quản lý hiệu quả vòng đời của ứng dụng từ giai đoạn dev cho tới production.
  * **Security & Compliance:** Quản lý các chính sách về an toàn thông tinmột cách đồng bộ trên nhiều môi trường và sản phẩm khác nhau (deploy policy, cấu hình firewall hàng loạt trên nhiều server...).



### Một số thuật ngữ cơ bản

  * **Controller Machine** : Là máy cài Ansible, chịu trách nhiệm quản lý, điều khiển và gởi task tới các máy con cần quản lý.
  * **Inventory** : Là file chứa thông tin các server cần quản lý. File này thường nằm tại đường dẫn /etc/ansible/hosts.
  * **Playbook** : Là file chứa các task của Ansible được ghi dưới định dạng YAML. Máy controller sẽ đọc các task trong Playbook và đẩy các lệnh thực thi tương ứng bằng Python xuống các máy con.
  * **Task** : Một block ghi tác vụ cần thực hiện trong playbook và các thông số liên quan. Ví dụ 1 playbook có thể chứa 2 task là: yum update và yum install vim.
  * **Module** : Ansible có rất nhiều module, ví dụ như moduel yum là module dùng để cài đặt các gói phần mềm qua yum. Ansible hiện có hơn ....2000 module để thực hiện nhiều tác vụ khác nhau, bạn cũng có thể tự viết thêm các module của mình nếu muốn.
  * **Role** : Là một tập playbook được định nghĩa sẵn để thực thi 1 tác vụ nhất định (ví dụ cài đặt LAMP stack).
  * **Play** : là quá trình thực thi của 1 playbook
  * **Facts** : Thông tin của những máy được Ansible điều khiển, cụ thể là thông tin về OS, network, system...
  * **Handlers** : Dùng để kích hoạt các thay đổi của dịch vụ như start, stop service.



## Hướng dẫn cài đặt Ansible

### Cấu hình yêu cầu

Python 2.7 trở lên, Python 3.x thì càng tốt <3

Đối với các host Linux thì cần hỗ trợ SSH.

Đối với những host chạy Windows thì cần hỗ trợ WinRM.

Coi thêm tại: [Hướng dẫn quản trị Windows Server bằng Ansible](https://cloudcraft.info/ansible-quan-tri-windows-server-2012/)

### Cách thức hoạt động

Giống như đa phần các phần mềm quản lý cấu hình tập trung khác. Ansible có 2 loại server là control machine và node. Control machine là máy có trách nhiệm quản lý các node con trong hệ thống. Đây cũng là máy lưu trữ các thông tin về các node, playbook và các script cần dùng để deploy trên các node khác qua giao thức SSH.

Để quản lý các node, Ansible sẽ gởi các module lệnh tới các node con qua SSH. Các module lệnh này sẽ được lưu trữ tạm thời trên các node con và giao tiếp với máy chủ Ansible bằng JSON. Khi đã thực thi xong tác vụ trên các máy này, các module đó sẽ được xóa đi. Các module này thường được lưu ở folder /root/.ansible hoặc /home/<user>/.ansible, tùy theo user mà Ansible dùng để quản lý các node con.

Khi Ansible ở chế độ rảnh, ko có task để thực hiện máy chủ Ansible sẽ không chiếm dụng tài nguyên do Ansible không sử dụng trình daemon hoặc program chạy ở chế độ background. Chỉ khi nào thực thi lệnh thì Ansible mới sử dụng tài nguyên của hệ thống.

### Cài đặt

**Ansible Server - Control Machine** Ansible Server sẽ là nơi người quản trị chạy lệnh (ad-hoc/playbooks), Ansible engine sẽ xử lý các lệnh này và đẩy xuống các server slave tương ứng. 
    
    
    # Install on CentOS
    sudo yum update
    sudo yum install epel-release
    sudo yum install ansible
    
    # Install on Ubuntu
    sudo apt update
    sudo apt install software-properties-common
    sudo apt-add-repository ppa:ansible/ansible
    sudo apt update
    sudo apt install ansible
    

Tạo file inventory chứa thông tin về các node cần quản lý 
    
    
    vim /etc/ansible/hosts
    
    [group1]
    10.0.17.5
    10.0.17.6

**Trên các Nodes con** Cài đặt Python 2.7 trở lên và mở port SSH. Chạy lệnh sau để kiểm tra các thông tin trên server 
    
    
    #Kiem tra ket noi den cac nodes
    ansible group1 -m ping
    
    #Kiem tra luong RAM trong tren cac nodes
    ansible group1 -a "free -m" -u <username>
    
    #Kiem tra nhanh o cung tren cac nodes
    ansible group1 -a "df -lh" -u <username>

Như vậy là các bạn đã biết được Ansible là gì cùng một số thao tác cơ bản để quản lý server trên ansible. Ở bài viết sau, mình sẽ nói rõ hơn về 2 loại hình thao tác với các node là Ad-hoc và Playbook cũng như những best practice thường dùng khi làm việc với Ansible.

## Đọc thêm

[Ansible với Windows Server - Phần 1](https://cloudcraft.info/ansible-quan-tri-windows-server-2012/) [Ansible với Windows Server - Phần 2](https://cloudcraft.info/ansible-quan-tri-windows-server-2012-voi-ansible-phan-2/) <https://www.ansible.com/overview/how-ansible-works>
