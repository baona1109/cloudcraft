---
title: "[Ansible] Quản trị Windows Server 2012 với Ansible - Phần 2"
date: 2018-07-24 14:34:43
categories: [Ansible, Automation, Windows]
---

## Quản trị Windows Server 2012 với Ansible - P2

Ở bài trước, mình đã hướng dẫn các bạn cách cài đặt và cấu hình quản lý Windows Server với Ansible. Ở bài này, mình sẽ hướng dẫn các bạn một số thao tác đơn giản để quản trị Windows Server bằng Ansible. Đọc lại bài trước tại đây: 

  * [Quản trị Windows Server 2012 với Ansible - Phần 1](https://cloudcraft.info/ansible-quan-tri-windows-server-2012-voi-ansible/)



## Ad-hoc Commands

Ad-hoc command là cách thức quản trị nhanh gọn lẹ, dùng với những thao tác đơn giản, cần kết quả liền. Ví dụ như cần kiểm tra kết nối tới các máy con, hoặc đồng bộ 1 file xuống nhiều máy con cùng một lúc. 
    
    
    ansible <host-group> -m <module> -a <arguments>

Ví dụ: Ping tới các máy trong group **win-test**
    
    
    ansible win-test -m win_ping

Copy file text.txt xuống ổ D các máy trong group win-text 
    
    
    ansible win-test -m win_file "src=/home/nduytg/text.txt dest=D:\text.txt

Các thao tác ad-hoc command này khá đơn giản và gọn nhẹ và dễ nhớ. Tuy nhiên, mình khuyên là nên dùng Playbook, hỗ trợ nhiều tính năng hơn. Ad-hoc command chỉ dùng khi cần check nhanh trên 1 vài host thôi ^^. 

## Ansible-playbook

Tất cả các playbook đều được lưu dưới dạng file **YAML**. Để chạy các playbook này, ta dùng lệnh _**ansible-playbook <đường dẫn tới file yml>**_ Ví dụ: _**ansible-playbook /etc/ansible/win_playbooks/test.yml**_ Cần chú ý là ta sẽ không xác định group host nào chạy lệnh này khi chạy giống như lệnh Ad-hoc mà sẽ xác định thông qua thông số **hosts** trong các file YAML. Dưới đây là một số playbook mẫu, các bạn có thể tham khảo 

### Quản lý user trên Windows

Khởi tạo user có tên là test, password là "Password123", user này thuộc nhóm Administrator. Tham số **state: present** có nghĩa là nếu chưa tồn tại user test thì ansible sẽ tạo mới user này, còn nếu đã tồn tại rồi thì Ansible sẽ không làm gì cả. Ngược lại, nếu ta để **state: absent** , thì có nghĩa là sẽ xóa user test 
    
    
    - name: Ensure user test is present
      hosts: win-test
      tasks:
        - name: Create test account
          win_user:
            name: test
            password: Password123
            state: present
            groups:
              - Administrators

### Quản lý file trên Windows

Copy file từ Ansible controller sang Windows, thêm xóa sửa và thực thi file đó. Ở đây, mình ví dụ bằng 1 script python. Ở ví dụ dưới, mình sẽ copy 1 script python từ server master xuống các máy con bằng module **win_copy** , điều chỉnh thông số trong các file này bằng module **win_inlinefile** kết hợp với regexp. Và cuối cùng là chạy script python bằng module **win_shell**. 
    
    
    - name: Copy, modify and executes Python files
      hosts: win-test
      tasks:
        - name: Copy upgrade scripts to windows hosts
          win_copy:
            src: /home/nduytg/dosomething.py
            dest: C:\Users\nduytg\dosomething.py
    
        - name: add IP to hosts
          win_lineinfile:
            path: C:\Users\nduytg\dosomething.py
            regexp: '^1\.1\.1\.5 abc\.xyz\.com\.vn'
            line: '1.1.1.5 abc.xyz.com.vn'
    
        - name: Install 
          win_shell: C:\Python27\python C:\Users\nduytg\dosomething.py

### Quản lý các gói cài đặt trên Windows

**Giới thiệu về Chocolatey** Nếu như trên Ubuntu có apt, CentOS có yum thì trên Windows, ta cũng có một thư viện cài đặt gói tương tự như vậy, đó là Chocolatey. Trang chủ: <https://chocolatey.org/>

**Yêu cầu cài đặt**

  * Windows 7+/Windows 2003+ trở lên
  * Windows PowerShell v2+ trở lên
  * .NET Framework 4.x+ trở lên

_(Thằng này do bên thứ 3 phát triển, không phải Microsoft, nên nếu công ty nào có quy định cao về Security thì nên cân nhắc. Trước giờ thì thằng này cũng ko dính phốt gì, nên chắc là không sao đâu :P)_ Chocolatey hỗ trợ khá nhiều phần mềm thông dụng với người dùng Windows như 7zip, Notepad++, Chrome, VLC cho tới git, Nodejs, Atom, PHP, cURL, pip, rsync (tự động cài CygWin nếu cài các lệnh của Linux)... Tham khảo danh sách các gói cài được hỗ trợ tại đây: <https://chocolatey.org/packages> **Hướng dẫn dùng Choco với Ansible** Cài đặt Notepad++ hàng loạt bằng Chocolatey 
    
    
    - name: Install  notepad++ from chocolatey
      hosts: win-db
      tasks:
        - name: Install Notepad++
          win_chocolatey:
            name: notepadplusplus
            state: latest

Cài đặt Python3, Rsync cho Windows từ Choco repo 
    
    
    - name: Install python, rsync from chocolatey
      hosts: win-test
      tasks:
        - name: Install Python3
          win_chocolatey:
            name: python3
            state: latest
    
        - name: Install Rsync
          win_chocolatey:
            name: rsync
            state: latest

**Note:** Nếu không muốn cài từ bên thứ 3 thì ta có thể đẩy file msi, exe xuống và chạy cài đặt bình thường bằng module **win_msi** hoặc **win_shell**. 

### Chạy lệnh trên Windows

Chạy các lệnh command có sẵn của Windows bằng module **win_command** , ở đây mình xin lấy ví dụ là lệnh netstat, liệt kêt các services đang chạy. 
    
    
    - name: Run netstat command
      hosts: windows
      tasks:
        - name: run netstat and return Ethernet stats 
          win_command: netstat -e
          register: netstat
        - debug: var=netstat

Chạy một số loại shell khác bằng module **win_shell** , mình lấy ví dụ ở đây là Python 
    
    
    - name: Test python from powershell
      hosts: win-test
      tasks:
        - name: Test run python by powershell
          win_shell: C:\Python27\python --version

### Update và reboot Windows hàng loạt

Reboot Windows với thời gian timeout là 900s. Có thể set thời gian timeout cao hơn, nếu Windows cần update nhiều gói update. 
    
    
    - name: Reboot Windows
      hosts: win-test
      tasks:
        - name: Reboot Windows
          win_reboot:
            reboot_timeout: 900

Update Windows. Ở đây mình chỉ update các gói thuộc 3 danh mục chính là Security, Critical và Rollup Updates bằng module **win_updates**. Update xong không reboot, nên dùng 1 playbook khác để reboot. 
    
    
    - name: Update Windows (Security, Critical, Rollup update)
      hosts: win-test
      tasks:
        - name: Install all security, critical, and rollup updates
          win_updates:
            category_names:
             - SecurityUpdates
             - CriticalUpdates
             - UpdateRollups
            reboot: no

### Đặt Scheduled Task

Set up Scheduled Task trên Windows. Ở đây mình sẽ đẩy 1 script backup bằng powershell xuống và thiết lập Windows chạy script này hàng ngày bằng module **win_scheduled_task**
    
    
    - name: Setup Schedule Task
      hosts: win-test
      tasks:
       - name: Create directory structure
         win_file:
            path: C:\Users\nduytg\BackupScripts
            state: directory
    
       - name: Copy backup script
         win_copy:
            src: /home/nduytg/RandomTasks/scriptDoSomeThing.ps1
            dest: C:\Users\nduytg\BackupScripts\scriptDoSomeThing.ps1
    
       - name: Install Rsync by chocolatey
         win_chocolatey:
            name: rsync
            state: latest
    
       - name: Setup Schedule Task
         win_scheduled_task:
            name: Backup log GameServer Daily
            author: nduytg
            path: \Tasks
            description: Backup log GameServer Daily
            
            actions:
            - path: C:\Windows\System32\WindowsPowerShell\v1.0\powershell.exe
              arguments: -ExecutionPolicy Unrestricted -NonInteractive -File C:\Users\nduytg\BackupScripts\scriptDoSomeThing.ps1
    
            triggers:
            - type: daily
              start_boundary: '2018-07-03T05:30:00'
            start_when_available: yes 
            state: present
            enabled: yes
            user: nduytg
            password: Password123
            runlevel: highest

Sơ sơ là như vậy thôi.... mình cũng chưa tìm hiểu được hết, các bạn có thể tham khảo thêm một số module Windows mà Ansible hỗ trợ tại đây: <https://docs.ansible.com/ansible/2.5/modules/list_of_windows_modules.html> Nên xài Ansible bản 2.5 trở lên nhé, mấy bản cũ còn thiếu nhiều tính năng lắm^^ 

## Tham khảo

https://developers.redhat.com/blog/2017/06/02/managing-windows-updates-with-ansible-in-redhat-7-3/
