---
title: "Hướng dẫn cài đặt python và virtualenv"
date: 2018-07-11 13:43:19
categories: [Python, Programming]
---

Trong bài viết này, mình sẽ hướng dẫn các bạn tạo môi trường ảo virtualenv để phát triển ứng dụng Python trên CentOS Đầu tiên ta cần phải kiểm tra phiên bản Python hiện tại trên máy mình đã. Đa phần các bản Linux hiện nay đều được cài sẵn Python 2.7. Để kiểm tra trên máy mình có cài đặt Python chưa, ta dùng lệnh 
    
    
    python --version
    Python 2.7.5
    

Để cài đặt Python trên CentOS, ta thực hiện các lệnh sau Update yum trước khi cài đặt 
    
    
    sudo yum update -y
    sudo yum install epel-release -y

## Cài đặt Python 2.7
    
    
    sudo yum install python -y
    sudo yum install python-pip -y
    

Mặc định thì repo của CentOS chỉ hỗ trợ cài đặt Python 2.7, muốn cài các bản Python cao hơn thì ta phải add repo IUM (Inline with Upstream Stable) để cài các bản Python mới nhất. 
    
    
    sudo yum install -y https://centos7.iuscommunity.org/ius-release.rpm

## Cài đặt Python 3.6
    
    
    # Install python 3.6
    sudo yum install python36u
    
    # Install other necessary packages
    sudo yum install python36u-pip
    sudo yum install python36u-devel
    sudo yum install python36u-libs

Như vậy là ta đã cài được cả 2 phiên bản của Python trên máy của mình. Kiểm tra lại bằng lệnh: 
    
    
    # Check Python 2.7 version
    python -V
    Python 2.7.5
    
    # Check Python 3.6 version 
    python3.6 -V
    Python 3.6.5

## Cài đặt virtualenv

Cài đặt virtualenv tùy theo phiên bản Python của bạn 
    
    
    # For python 2.7
    pip install virtualenv
    
    # For python 3.6
    pip3.6 install virtualenv

## Khởi tạo virtualenv cho project

Trong folder của project của bạn, ta khởi tạo virtualenv như sau: 
    
    
    # Create virtual enviroment for project
    virtualenv myenv
    
    # Activate myenv
    source myenv/bin/activate
    (myenv) $ _
    

Lệnh virtualenv dùng để tạo một môi trường ảo có tên myenv. Sau khi tạo và kích hoạt môi trường ảo, ta sẽ thấy trước dấu nhắc lệnh có (myenv), đây là thông báo cho biết ta hiện đang ở trong môi trường ảo. Mọi thao tác cài đặt các gói trên pip chỉ sẽ cài đặt trong môi trường này, không cài ngoài máy thật. Tránh được xung đột các gói cài đặt khi phải phát triển nhiều project khác nhau. Cài đặt thêm các gói cần thiết cho project python của bạn 
    
    
    # Install requirement packages for project inside virtualenv
    (myenv) $ pip install [package_name]
    (myenv) $ pip install -r requirements.txt
    
    # Export all packages inside virtualenv to requirements.txt
    (myenv) $ pip freeze > requirements.txt
    

Còn để thoát khỏi môi trường ảo này, bạn chỉ cần chạy lệnh deactivate 
    
    
    # Deactivate myenv
    (myenv) $ deactivate

Như vậy là các bạn đã setup thành công môi trường ảo để phát triển phần mềm rồi đấy ^^. Chúc các bạn thành công ^^.
