---
title: "Hướng dẫn cài đặt phiên bản Docker bất kì trên Linux"
date: 2018-02-08 10:26:35
categories: [Container, Docker, Linux]
---

Việc cài đặt Docker trên các distro Linux thông thường sẽ mặc định cài đặt phiên bản latest stable release. Nhưng đối với một số hệ thống tích hợp, việc đòi hỏi một phiên bản cụ thể bất kì là điều cần thiết và có thể đảm bảo hoạt động của toàn bộ hệ thống. Bài viết này tôi sẽ hướng dẫn cách cài đặt phiên bản Docker cũ hơn trên các Linux Distro. 

## Ubuntu

Hỗ trợ: 

  * Phiên bản bằng hoặc mới hơn 14.04 (Trusty)
  * Sử dụng một trong các kiến trúc sau: `x86_64`, `armhf`, `s390x` (IBM Z), và `ppc64le` (IBM Power)

Nếu trên máy đã từng cài đặt Docker, hãy xóa nó trước khi thực hiện hướng dẫn. Thực hiện tuần tự các bước sau. Update index của apt: 
    
    
    $ sudo apt-get update

Cài đặt các gói cần thiết: 
    
    
    $ sudo apt-get install \
        apt-transport-https \
        ca-certificates \
        curl \
        software-properties-common

Add GPG's key: 
    
    
    $ curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -

Kiểm tra key vừa add: 
    
    
    $ sudo apt-key fingerprint 0EBFCD88
    
    pub   4096R/0EBFCD88 2017-02-22
          Key fingerprint = 9DC8 5822 9FC7 DD38 854A  E2D8 8D81 803C 0EBF CD88
    uid                  Docker Release (CE deb) <docker@docker.com>
    sub   4096R/F273FCD8 2017-02-22

Add repository vào apt: \-- Đối với x86_64/amd64 
    
    
    $ sudo add-apt-repository \
       "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
       $(lsb_release -cs) \
       stable"
    

\-- Đối với armhf 
    
    
    $ sudo add-apt-repository \
       "deb [arch=armhf] https://download.docker.com/linux/ubuntu \
       $(lsb_release -cs) \
       stable"
    

\-- Đối với ppc64le (IBM Power) 
    
    
    $ sudo add-apt-repository \
       "deb [arch=ppc64el] https://download.docker.com/linux/ubuntu \
       $(lsb_release -cs) \
       stable"

\-- Đối với s390x (IBM Z) 
    
    
    $ sudo add-apt-repository \
       "deb [arch=s390x] https://download.docker.com/linux/ubuntu \
       $(lsb_release -cs) \
       stable"

Cập nhật lại index: 
    
    
    $ sudo apt-get update

Tìm phiên bản cần cài đặt: 
    
    
    $ sudo apt-cache madison docker-ce
    
     docker-ce | 17.12.0~ce-0~ubuntu | https://download.docker.com/linux/ubuntu xenial/stable amd64 Packages
     docker-ce | 17.09.1~ce-0~ubuntu | https://download.docker.com/linux/ubuntu xenial/stable amd64 Packages
     docker-ce | 17.09.0~ce-0~ubuntu | https://download.docker.com/linux/ubuntu xenial/stable amd64 Packages
     docker-ce | 17.06.2~ce-0~ubuntu | https://download.docker.com/linux/ubuntu xenial/stable amd64 Packages
     docker-ce | 17.06.1~ce-0~ubuntu | https://download.docker.com/linux/ubuntu xenial/stable amd64 Packages
     docker-ce | 17.06.0~ce-0~ubuntu | https://download.docker.com/linux/ubuntu xenial/stable amd64 Packages
     docker-ce | 17.03.2~ce-0~ubuntu-xenial | https://download.docker.com/linux/ubuntu xenial/stable amd64 Packages
     docker-ce | 17.03.1~ce-0~ubuntu-xenial | https://download.docker.com/linux/ubuntu xenial/stable amd64 Packages
     docker-ce | 17.03.0~ce-0~ubuntu-xenial | https://download.docker.com/linux/ubuntu xenial/stable amd64 Packages
    

Cài đặt phiên bản docker-ce kèm với thông số version: 
    
    
    $ sudo apt-get install docker-ce=17.03.1~ce-0~ubuntu-xenial

Thêm user đang sử dụng vào group docker: 
    
    
    $ sudo usermod -aG docker currentusername

Hoàn tất cài đặt. 

## CentOS

Yêu cầu: 

  * centos-extra
  * overlay2
  * CentOS 7

Nếu trên máy đã từng cài đặt Docker, hãy xóa nó trước khi thực hiện hướng dẫn. Thực hiện tuần tự các bước sau. Cài đặt các gói hỗ trợ: 
    
    
    $ sudo yum install -y yum-utils \
      device-mapper-persistent-data \
      lvm2

Thêm repository vào yum: 
    
    
    $ sudo yum-config-manager \
        --add-repo \
        https://download.docker.com/linux/centos/docker-ce.repo

Cập nhật lại index từ repository 
    
    
    $ sudo yum update

Xem các phiên bản khác đang được hỗ trợ: 
    
    
    $ yum list docker-ce --showduplicates | sort -r
    
    docker-ce.x86_64            17.12.ce-1.el7.centos             docker-ce-stable

Cài đặt với phiên bản chỉ định: 
    
    
    $ sudo yum install 17.12.ce-1.el7.centos

Thêm user đang sử dụng vào group docker: 
    
    
    $ sudo usermod -aG docker currentusername

Hoàn tất cài đặt.  
