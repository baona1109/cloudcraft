---
title: "[Nhập môn Docker] Phần 1: Cài đặt Docker trên CentOS7"
date: 2018-01-02 17:02:09
categories: [Docker, Linux, Container]
---

Trong bài viết này, mình sẽ giới thiệu với các bạn cách cài đặt Docker CE (Community Edition) trên CentOS7. Đây cũng là bài đầu tiên trong loạt bài tìm hiểu về Docker của mình. Các bạn có thể tìm hiểu kỹ hơn về công nghệ container tại đây: <https://cloudcraft.info/containers-mot-cach-tiep-can-moi-cua-ao-hoa/> Vẫn là link cheat-sheet cho ai lười đọc như mọi khi :): [Link](https://github.com/cloudcraftteam/System-Engineer-Cheat-Sheets/blob/master/Docker/Docker-Guide-Part-1.txt)

## Giới thiệu sơ lược về Docker

Theo như trang chủ của Docker thì: _"Docker là một nền tảng mở cho việc phát triển (build), vận chuyển (ship) và chạy các ứng dụng (run) dựa trên công nghệ container. Docker cho phép tách ứng dụng ra khỏi hạ tầng bên dưới, từ đó có thể cung cấp các ứng dụng một cách nhanh chóng. Từ đó cho phép giảm thiểu đáng kể sự chậm trễ giữa việc viết mã nguồn và chạy mã nguồn."_ ![](https://cloudcraft.info/wp-content/uploads/2018/01/docker-logo-300x243.png)

_Logo của Docker_

Docker cung cấp các công cụ cũng như bản thân là một nền tảng để quản lý vòng đời của ứng dụng: 

  * Đóng gói các ứng dụng và hỗ trợ các thành phần library/dependencies cần thiết của ứng dụng đó vào trong Container.
  * Phân phối và vận chuyển đến người khác cùng phát triển (development) hoặcthử nghiệm, kiểm thử (testing).
  * Triển khai các ứng dụng từ môi trường tích hợp đến môi trường sản xuất sảnphẩm dùng cho sử dụng thực tế, bất kể nằm trong một trung tâm dữ liệu sở tại hay môi trường điện toán đám mây (cloud).

Trong bài này, mình không đi quá sâu về các chi tiết kỹ thuật của Docker hay container mà chỉ tập trung vào cách thức cài đặt và cấu hình. Trong tương lai, mình sẽ viết thêm một số bài khác để giới thiệu về kiến trúc, cách thức hoạt động và các công nghệ liên quan đến Docker, container. 

## Hướng dẫn cài đặt Docker CE

### Chuẩn bị môi trường

Để cài đặt Docker trên các Linux, bạn cần phải có kernel từ phiên bản 3.10 trở lên (CentOS7, Ubuntu 16,...) và OS của bạn phải là 64-bit. Nếu bạn đang dùng các OS đời cũ có kernel nhỏ hơn 3.10 thì cần phải nâng cấp kernel lên. Xóa các phiên bản Docker cũ (nếu có) 
    
    
    ##### Remove old version of docker #####
    yum remove docker docker-common docker-selinux docker-engine
    
    # Remove all old images, containers and volumes
    rm -rf /var/lib/docker

Cài đặt các gói cần thiết 
    
    
    # Install prerequisite packages
    yum install -y yum-utils device-mapper-persistent-data lvm2

Cấu hình repo cài đặt Docker chính chủ 
    
    
    # Enable "stable" docker
    yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

### Cài đặt

Cài đặt Docker CE 
    
    
    yum install docker-ce
    
    # Start Docker 
    systemctl start docker
    systemctl enable docker
    
    # Check Docker version
    [root@centos-docker ~]# docker --version
    Docker version 17.12.0-ce, build c97c6d6

Chạy thử image hello-world trên Docker 
    
    
    [root@centos-docker ~]# docker run hello-world
    Unable to find image 'hello-world:latest' locally
    latest: Pulling from library/hello-world
    ca4f61b1923c: Pull complete
    Digest: sha256:445b2fe9afea8b4aa0b2f27fe49dd6ad130dfe7a8fd0832be5de99625dad47cd
    Status: Downloaded newer image for hello-world:latest
    
    Hello from Docker!
    This message shows that your installation appears to be working correctly.
    
    To generate this message, Docker took the following steps:
     1. The Docker client contacted the Docker daemon.
     2. The Docker daemon pulled the "hello-world" image from the Docker Hub.
        (amd64)
     3. The Docker daemon created a new container from that image which runs the
        executable that produces the output you are currently reading.
     4. The Docker daemon streamed that output to the Docker client, which sent it
        to your terminal.
    
    To try something more ambitious, you can run an Ubuntu container with:
     $ docker run -it ubuntu bash
    
    Share images, automate workflows, and more with a free Docker ID:
     https://cloud.docker.com/
    
    For more examples and ideas, visit:
     https://docs.docker.com/engine/userguide/

## Một số thao tác cơ bản

Sau khi cài đặt thành công Docker, ta sẽ tiếp tục làm quen một số thao tác cơ bản trên Docker như pull, list image, run container, list container... 
    
    
    # List local images
    docker images
    
    # Searching images (on default registry)
    docker search centos
    # Pull specific images
    docker pull centos:centos6
    docker pull centos
    docker images
    
    docker run [name|image-id]
    
    # Inspect local images
    docker inspect centos
    docker inspect hello-world
    
    # List running containers #
    docker ps
    
    # List all containers
    docker ps -a
    
    # run docker interactive, terminal
    docker run -it centos:latest
    
    # run docker as deamon
    docker run -d centos:lastest

Như vậy là các bạn đã hoàn thành quá trình cài đặt Docker trên CentOS7 và nắm được một số thao tác cơ bản trên Docker rồi đấy ^^. 

## Tham khảo

<https://docs.docker.com/get-started/> <https://docs.docker.com/engine/installation/linux/docker-ce/centos/>
