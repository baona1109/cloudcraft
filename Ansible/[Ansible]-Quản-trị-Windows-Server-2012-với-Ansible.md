---
title: "[Ansible] Quản trị Windows Server 2012 với Ansible"
date: 2018-06-13 09:26:40
categories: [Ansible, Automation, Windows]
---

# Giới thiệu

Ansible là một công cụ quản lý cấu hình mạnh mẽ và tiện dụng. Ansible sử dụng giao thức SSH để kết nối và quản lý các máy khác. Thế nhưng, trên Windows lại không có SSH (Microsoft dự kiến sẽ tích hợp sẵn OpenSSH vào Windows trong tương lai, nhưng chưa phải bây giờ :))) ).

Vậy nên, ta cần phải sử dụng WinRM, một tính năng của Windows để cho phép Ansible quản trị từ xa.

**WinRM**

WinRM là giao thức quản trị từ xa (windows remote management) được Microsoft phát triển dành cho các hệ điều hành Windows.

Giao thức này cho phép người dùng quản trị các máy Windows từ xa qua HTTP/HTTPS. Từ phiên bản Windows Server 2012, WinRM được bật sẵn, nhưng để giao tiếp được với Ansible thì ta cần phải cấu hình thêm kha khá mới chạy được (và chạy an toàn).

**Có thể dùng key SSH để kết nối không?**

Việc quản trị các host windows từ xa được thực hiện qua giao thức WinRM (Windows Remote Management).

WinRM hỗ trợ nhiều phương pháp chứng thực khác nhau (coi bảng bên dưới). Gần giống với SSH Key là Phương pháp chứng thực bằng certificate X509 dựa trên cặp certificate - private key (coi cụ thể hơn trong phần cài đặt).

# Môi trường yêu cầu

Trong bài viết này, mình sẽ hướng dẫn cấu hình Ansible quản trị Windows cho môi trường **WorkGroup** , **sử dụng certificate – private key** để kết nối. Nếu có thời gian, mình sẽ bổ sung thêm cách cấu hình trong môi trường Domain.

Người viết thử nghiệm trên CentOS 7 và Windows Server 2012 R2 Standard

**Linux – Controller**

  * Cài đặt Ansible bản 2.5 trở lên.
  * Cài đặt gói pywinrm (cài qua pip).

**Windows – Slaves**

  * Windows Server bản 2008 trở lên. Windows bản 7 trở lên.
  * Powershell phiên bản 3.0 + .NET Framework bản 4.0 trở lên (nếu thấp hơn thì có thể upgrade bằng script [này](https://github.com/jborean93/ansible-windows/blob/master/scripts/Upgrade-PowerShell.ps1)). Nếu
  * AD DC – Windows Server (_**Không bắt buộc** ,_ nhưng nếu có môi trường Domain thì việc setup các hosts Windows _**sẽ đơn giản hơn rất nhiều**_). 
    * AD: Tạo account Admin local cho toàn bộ các máy Windows Slave.
    * ADCS: Tạo và quản lý 1 certificate chung để kết nối đến toàn bộ các máy Windows Slave.

Nếu phiên bản Windows của bạn là 2008 trở về trước, có thể upgrade phiên bản PowerShell trong máy bằng cách sau, mở PowerShell trên Windows dưới quyền Administrator và gõ những lệnh sau: 
    
    
    $url = "https://raw.githubusercontent.com/jborean93/ansible-windows/master/scripts/Upgrade-PowerShell.ps1"
    $file = "$env:temp\Upgrade-PowerShell.ps1"
    $username = "Administrator"
    $password = "PasswordCuaBan"
    
    (New-Object -TypeName System.Net.WebClient).DownloadFile($url, $file)
    Set-ExecutionPolicy -ExecutionPolicy Unrestricted -Force
    
    # Chon version 3.0, 4.0 hoac 5.1, tot nhat la ban 4.0 tro len
    &$file -Version 5.1 -Username $username -Password $password -Verbose
    
    # Tat tinh nang thuc thi script
    Set-ExecutionPolicy -ExecutionPolicy Restricted -Force
    
    $reg_winlogon_path = "HKLM:\Software\Microsoft\Windows NT\CurrentVersion\Winlogon"
    Set-ItemProperty -Path $reg_winlogon_path -Name AutoAdminLogon -Value 0
    Remove-ItemProperty -Path $reg_winlogon_path -Name DefaultUserName -ErrorAction SilentlyContinue
    Remove-ItemProperty -Path $reg_winlogon_path -Name DefaultPassword -ErrorAction SilentlyContinue

**Chú ý:** Để đảm bảo an toàn, cần setup một đường mạng Private để Ansible liên lạc với các máy Windows. **Không nên quản trị các máy này qua đường mạng Public.**

# Cài đặt

**Cài đặt Ansible trên Linux Controller**

Ansible dùng gói pywinrm để tương tác với các máy chạy Windows thông qua giao thức WinRM. Set up Ansible trên Linux khá đơn giản, ta dùng những lệnh sau:
    
    
    sudo yum install -y epel-release
    sudo yum install ansible
    sudo yum install python-pip
    sudo pip install "pywinrm>=0.3.0"

Sau khi đã cài đặt xong, ta cần phải tạo cặp certificate – private key, ta sẽ đặt file certificate trên các host Windows (tương tự như public key). Điều chỉnh username thành user Admin local chạy trên máy windows, sau đó chạy script sau để tạo cặp key:
    
    
    # set the name of the local user that will have the key mapped to
    # This is the local admin user on windows hosts
    USERNAME="userAdminLocal"
    
    cat > openssl.conf << EOL
    distinguished_name = req_distinguished_name
    [req_distinguished_name]
    [v3_req_client]
    extendedKeyUsage = serverAuth
    subjectAltName = otherName:1.3.6.1.4.1.311.20.2.3;UTF8:$USERNAME@localhost
    EOL
    
    export OPENSSL_CONF=openssl.conf
    openssl req -x509 -nodes -days 3650 -newkey rsa:2048 -out cert.pem -outform PEM -keyout cert_key.pem -subj "/CN=$USERNAME" -extensions v3_req_client
    rm openssl.conf

Trong folder /etc/ansible, tạo folder group_vars, trong folder này, tạo file windows, chứa thông tin private key dùng để kết nối tới Windows
    
    
    ---
    ansible_port: 5986
    ansible_connection: winrm
    ansible_winrm_cert_pem: /etc/ansible/certs/cert.pem
    ansible_winrm_cert_key_pem: /etc/ansible/certs/cert_key.pem
    ansible_winrm_transport: certificate
    ansible_winrm_server_cert_validation: ignore

Tiếp theo, điều chỉnh file **/etc/ansible/hosts** như sau: 
    
    
    [windows:children]
    #win-*
    win-test
    win-db
    
    [win-test]
    10.10.10.15
    10.10.10.16
    
    [win-db]
    10.10.10.123
    10.10.10.124
    

[windows:children] có nghĩa là những group win-test, win-db sẽ kế thừa các thuộc tính được cấu hình trong /etc/ansible/group_vars/windows. Sau này khi cần tạo thêm group mới, ta chỉ cần add thêm vô mục [windows:childern] là được.

**Cài đặt trên các host chạy Windows**

Cấu hình trên các host windows khá đơn giản nếu có AD (không có AD thì phải làm tay từng con, hơi mệt). Các bạn tải về bộ script mình đã soạn sẵn ở đây: [GitHub](https://github.com/nduytg/Ansible-Windows/tree/master/WindowsSlave)

Các bước làm tuần tự:

  1. Copy file cert.pem đã tạo trên Controller, copy qua con Windows cần điều khiển
  2. Chạy script [ConfigureRemotingForAnsible.ps1](https://github.com/nduytg/Ansible-Windows/blob/master/WindowsSlave/ConfigureRemotingForAnsible.ps1 "ConfigureRemotingForAnsible.ps1") script này dùng để setup cơ bản cho WinRM
  3. Chạy script [ansible_winrm_enable.ps1](https://github.com/nduytg/Ansible-Windows/blob/master/WindowsSlave/ansible_winrm_enable.ps1 "ansible_winrm_enable.ps1"), script này sẽ gọi lại 2 script: 
     * Script [createAdminAccount.ps1](https://github.com/nduytg/Ansible-Windows/blob/master/WindowsSlave/createAdminAccount.ps1 "createAdminAccount.ps1") tạo 1 account Admin trên local, account này dùng để quản trị con Windows
     * Script [import_cert.ps1](https://github.com/nduytg/Ansible-Windows/blob/master/WindowsSlave/import_cert.ps1 "import_cert.ps1") mapping certificate vừa tạo trên windows (cert.pem) vào account mới tạo, như vậy Ansible mới có thể quản trị Windows bằng private key.



Sau khi cài đặt xong, ta đứng trên máy Controller, gõ lệnh sau để kiểm tra khả năng kết nối tới các máy Windows
    
    
    [root@centos7]# ansible win-test -m win_ping
    10.10.10.16 | SUCCESS => {
        "changed": false,
        "ping": "pong"
    }
    10.10.10.15 | SUCCESS => {
        "changed": false,
        "ping": "pong"
    }
    

## Một số lỗi thường gặp khi cấu hình WinRM

Sau đây là một số lỗi thường gặp khi cấu hình WinRM:

  * WinRM không chạy trên máy remote => Kiểm tra và chạy lại script
  * Firewall trên máy remote đang chặn WinRM => Allow port 5895, 5896



# Một số tùy chọn chứng thực của WinRM

Ở trên là hướng dẫn chạy script nhanh gọn lẹ, còn mục dưới đây là dành cho bạn nào cần tìm hiểu thêm về các tùy chọn chứng thực của Ansible.

Khi giao tiếp giữa Ansible và WinRM, ta cần chọn một chuẩn chứng thực để 2 bên kết nối được với nhau (như dùng password, certificate, Kerberos…).

Ta chọn loại giao thức chứng thực cho Ansible bằng biến ansible_winrm_transport

Các giao thức chứng thực được hỗ trợ:

**Option** | **Local Accounts** | **Account của Domain** | **Mã hóa** **HTTP** | **Nhận xét**  
---|---|---|---|---  
_**Basic**_ | Yes | No | No | Chỉ nên dùng để test kết nối  
_**NTLM**_ | Yes | Yes | Yes | An toàn hơn Basic một chút, nhưng về cơ bản vẫn là chứng thực bằng Password  
_**Certificate**_ | Yes | No | No | Đối với môi trường WorkGroup thì đây là cách kết nối bảo mật nhất.  
_**Kerberos**_ | No | Yes | Yes | An toàn, cần môi trường domain.  
_**CredSSP**_ | Yes | Yes | Yes | An toàn, cần môi trường domain.  
  
## Basic

là giao thức chứng thực đơn giản, chỉ dùng username/password. Đây cũng là giao thức kém bảo mật nhất, dữ liệu nếu truyền qua HTTP thì có thể dễ dàng bị bẻ khóa. Chỉ hỗ trợ chứng thực bằng Local Account trên từng máy. Dùng cho môi trường Test/Dev, **KHÔNG DÙNG CHO MÔI TRƯỜNG PRODUCTION**

**Cấu hình trên Ansible**
    
    
    ansible_user: LocalUsername
    ansible_password: PasswordLocalUser
    ansible_connection: winrm
    ansible_winrm_transport: basic

**Cấu hình trên Windows** Giao thức này mặc định là tắt, để bật lên thì ta dùng lệnh sau: 
    
    
    Set-Item -Path WSMan:\localhost\Service\Auth\Basic -Value $true

## Certificate

Đây là cách chứng thực dùng cặp certificate – private key, tương tự như dùng SSH key pair trên linux. Tuy nhiên format file và quá trình tạo key có chút khác biệt.

**Cấu hình trên Ansible**
    
    
    ansible_connection: winrm
    ansible_winrm_cert_pem: /path/to/certificate/public/key.pem
    ansible_winrm_cert_key_pem: /path/to/certificate/private/key.pem
    ansible_winrm_transport: certificate

**Cấu hình trên Windows** Giao thức này mặc định là tắt, để bật lên thì ta dùng lệnh sau: 
    
    
    Set-Item -Path WSMan:\localhost\Service\Auth\Certificate -Value $true

## **Một số thao tác cần nắm thêm**

**Lấy thông tin về WinRM**
    
    
    winrm get winrm/config
    winrm get winrm/config/Service
    winrm get winrm/config/Winrs

**Lấy thông tin về WinRM Listener**
    
    
    winrm e winrm/config/Listener

**Xóa WinRM Listener**
    
    
    # remove all listeners
    Remove-Item -Path WSMan:\localhost\Listener\* -Recurse -Force
    
    # only remove listeners that are run over HTTPS
    Get-ChildItem -Path WSMan:\localhost\Listener | Where-Object { $_.Keys -contains "Transport=HTTPS" } | Remove-Item -Recurse -Force

# Một số ứng dụng

## Triển khai phần mềm hàng loạt

Hiện mình biết là có một giải pháp của Microsoft là SCCM (System Center Configuration Manager). Tham khảo tại đây: <https://cloudcraft.info/gioi-thieu-sccm/> Nó có thể quản lý và deploy phần mềm hàng loạt trên Windows. Điểm mạnh là ….nó rất mạnh, có thể triển khai cho môi trường domain với khoảng chục ngàn client. Còn điểm yếu là nó tốn phí license.

Thực sự thì không phải ai cũng đủ tiền để chơi với Microsoft cả. Đối với những hệ thống chỉ cõ 100~200 server Windows đổ lại, ta có thể dùng Ansible để quản lý việc cài đặt phần mềm này.

Có 2 cách để deploy phần mềm trên Windows thông qua Ansible

  1. Đẩy file cài đặt (.msi, .exe) về từng máy, sau đó chạy lệnh cài
  2. Dùng một giải pháp mã nguồn mở là [Chocolatey.org](https://chocolatey.org/)



![](https://cloudcraft.info/wp-content/uploads/2018/06/word-image.jpeg)

## Đồng bộ cấu hình firewall

Sếp: “Em chặn port SMB trên mấy con Windows để ngừa ransomware nhé” Nhân viên: “Dạ anh, bao nhiêu con vậy anh?” Sếp: “100 con thôi em” Nhân viên: “….” => Dùng Ansible 

# Tạm kết

Ở bài viết sau, mình sẽ hướng dẫn các bạn sử dụng playbook/ad-hoc command để chạy các tác vụ quản lý cho Windows như:

  * Cấu hình Scheduler Task hàng loạt
  * Khởi tạo và quản lý user/group (môi trường WorkGroup)
  * Quản lý và cài đặt các gói Windows Updates (Test kỹ trước khi làm hàng loạt)
  * Đẩy file từ máy chủ xuống các máy con/Kéo file từ máy con về máy chủ.
  * Đẩy và thực thi các script Powershell xuống các máy con. (Tính năng rất hay)

Link bài 2: [Quản trị Windows bằng Ansible-Playbook](https://cloudcraft.info/ansible-quan-tri-windows-server-2012-voi-ansible-phan-2/)

# Tham khảo

<http://docs.ansible.com/ansible/latest/user_guide/windows_setup.html> <https://docs.ansible.com/ansible/latest/user_guide/windows_usage.html> <https://argonsys.com/learn-microsoft-cloud/articles/configuring-ansible-manage-windows-servers-step-step/> <https://fabianlee.org/2017/06/05/ansible-managing-a-windows-host-using-ansible/> <https://www.infrasightlabs.com/how-to-enable-winrm-on-windows-servers-clients> <http://www.hurryupandwait.io/blog/understanding-and-troubleshooting-winrm-connection-and-authentication-a-thrill-seekers-guide-to-adventure> <http://www.hurryupandwait.io/blog/certificate-password-less-based-authentication-in-winrm>
