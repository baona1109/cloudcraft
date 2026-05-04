---
title: "Hướng dẫn cài đặt Redmine trên CentOS"
date: 2018-07-11 15:57:20
categories: [Linux]
---

Nếu các bạn đã & đang làm việc trong một công ty IT Nhật Bản, hẳn các bạn đã từng nghe đến công cụ Redmine. Redmine là một công cụ để quản lý dự án trên giao diện web, được viết trên Ruby on Rails, cho phép người dùng quản lý nhiều project và tạo ra cả những subproject trong đó. Ở bài viết này, mình sẽ hướng dẫn các bạn cài đặt Redmine trên CentOS 7 step-by-step. Bài viết giả định bạn đang có một server / VPS mới toanh, chỉ vừa cài đặt OS, cấu hình mạng và chưa cài đặt gì thêm. **1) Update hệ thống và cài đặt các công cụ hỗ trợ việc cài đặt**
    
    
    # yum install epel-release -y
    
    # yum update -y
    
    # yum install nano wget unzip mlocate -y
    
    # updatedb

**2) Cài đặt các thư viện và các gói cần thiết**
    
    
    # yum groupinstall "Development Tools" -y
    
    # yum install zlib-devel curl-devel openssl-devel httpd-devel apr-devel apr-util-devel mysql-devel -y
    
    # yum install gcc-c++ patch readline readline-devel zlib zlib-devel -y
    
    # yum install libyaml-devel libffi-devel openssl-devel make -y
    
    # yum install bzip2 autoconf automake libtool bison iconv-devel sqlite-devel -y
    
    # yum install ruby-devel ImageMagick-devel ImageMagick -y

Sẽ có các gói bị trùng lặp, có các gói không dùng tới, nhưng ông bà ta có câu "thừa hơn thiếu" <3 **3) Disable selinux**
    
    
    # nano /etc/sysconfig/selinux

sửa đoạn **enforcing** thành **disabled** **4) Cài đặt Apache và MariaDB**
    
    
    # yum install httpd mariadb-server -y

**5) Cài đặt RVM (dùng để cài đặt Ruby ở bước tiếp theo)**
    
    
    # curl -sSL https://rvm.io/mpapis.asc | gpg --import -
    
    # curl -L get.rvm.io | bash -s stable
    
    # source /etc/profile.d/rvm.sh
    
    # rvm reload
    
    # rvm requirements run

**6) Cài đặt Ruby** Ở bài viết này, mình sẽ cài đặt Remine phiên bản 3.4.x (yêu cầu ruby 2.2) 
    
    
    # rvm install 2.2.4
    
    # rvm use 2.2.4 --default
    
    # ruby --version

**7) Bật MariaDB và tạo database cho Redmine**
    
    
    # systemctl start mariadb
    
    # systemctl enable mariadb
    
    # mysql
    
    > CREATE DATABASE redmine;
    
    > GRANT ALL PRIVILEGES on redmine.* to redmine@'localhost' identified by 'cloudcraftnumber1';
    
    > CREATE DATABASE redmine_development;
    
    > GRANT ALL PRIVILEGES on redmine_development.* to redmine@'localhost' identified by 'cloudcraftnumber1';

**8) Tải Redmine**
    
    
    # wget https://www.redmine.org/releases/redmine-3.4.3.tar.gz
    
    # tar xzvf redmine-3.4.3.tar.gz
    
    # mv redmine-3.4.3 /var/www/redmine
    
    # cd /var/www/redmine/

**9) Cấu hình database cho Redmine**
    
    
    # mv config/database.yml.example config/database.yml
    
    # nano config/database.yml

tìm và sửa lại đoạn sau theo đúng thông tin db đã tạo ở bước 7: 
    
    
    production:
      adapter: mysql2
      database: redmine
      host: localhost
      username: redmine
      password: "cloudcraftnumber1"
      encoding: utf8

**10) Cài đặt Bundler**
    
    
    # gem install bundler --no-rdoc --no-ri

**11) Cài đặt Redmine bằng Bundler**
    
    
    # bundle install --without development test postgresql sqlite

**12) Khởi tạo key cho Redmine**
    
    
    # RAILS_ENV=production bundle exec rake generate_secret_token

**13) Khởi tạo database cho Redmine**
    
    
    # RAILS_ENV=production bundle exec rake db:migrate
    
    # RAILS_ENV=production bundle exec rake redmine:load_default_data

**14) Thay đổi char encoding của database** Vì Redmine đa số được sử dụng bởi các công ty Nhật, nên các bạn rất có thể sẽ gặp trường hợp không dùng được kí tự tiếng Nhật khi tạo task hay các tác vụ khác. Ta chỉ việc thay đổi char encoding của database bằng cách sau: 
    
    
    # DB="redmine"; ( echo 'ALTER DATABASE `'"$DB"'` CHARACTER SET utf8 COLLATE utf8_general_ci;'; \
    mysql "$DB" -e "SHOW TABLES" --batch --skip-column-names | xargs -I{} echo 'ALTER TABLE `'{}'` \
    CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;' ) | mysql "$DB"
    
    # DB="redmine_development"; ( echo 'ALTER DATABASE `'"$DB"'` CHARACTER SET utf8 COLLATE utf8_general_ci;'; \
    mysql "$DB" -e "SHOW TABLES" --batch --skip-column-names | xargs -I{} echo 'ALTER TABLE `'{}'` \
    CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;' ) | mysql "$DB"

**15) Điều chỉnh permission cho thư mục cache** Trong quá trình sử dụng, các bạn có thể sẽ gặp trường hợp khi upload file hay hình ảnh sẽ không xuất hiện thumbnail. Ta khắc phục bằng cách set lại permission cho thư mục cache: 
    
    
    # chmod -R 777 tmp/cache

**16) Cài đặt Passenger cho Apache**
    
    
    # gem install passenger --no-rdoc --no-ri
    
    # reboot now

Ơ, sao lại có reboot ở đây? Vì lúc nãy mình chỉnh Selinux mà quên reboot nên phải thực hiện reboot ở bước này :( Tiếp tục nào 
    
    
    # passenger-install-apache2-module

Trên màn hình sẽ xuất hiện các thông báo, hướng dẫn nếu server / VPS của ta không đạt đủ các điều kiện, ví dụ như chưa disable Selinux, swap partition không lớn hơn 1G... **17) Cấu hình Apache**
    
    
    # nano /etc/httpd/conf.d/passenger.conf

Thêm vào đoạn sau: 
    
    
    <VirtualHost *:80>
        ServerName redmine.example.com
        DocumentRoot /var/www/redmine/public
        LoadModule passenger_module /usr/local/rvm/gems/ruby-2.2.4/gems/passenger-5.1.12/buildout/apache2/mod_passenger.so
        <IfModule mod_passenger.c>
            PassengerRoot /usr/local/rvm/gems/ruby-2.2.4/gems/passenger-5.1.12
            PassengerDefaultRuby /usr/local/rvm/gems/ruby-2.2.4/wrappers/ruby
        </IfModule>
    </VirtualHost>

Bạn có thể thay đổi ServerName theo ý muốn. **18) Xong rồi đó, chạy thôi** Khoan đã, kiểm tra cấu hình Apache trước cái đã :3 
    
    
    # httpd -t

Giờ thì bật Apache và sử dụng Redmine thôi <3 
    
    
    # systemctl start httpd
    
    # systemctl enable httpd

   
