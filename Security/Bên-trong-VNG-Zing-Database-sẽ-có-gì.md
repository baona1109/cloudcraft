---
title: "Bên trong VNG Zing Database sẽ có gì"
date: 2018-05-02 12:05:28
categories: [Security]
---

"Bài post này chỉ nhằm mục đích tìm hiểu, học hỏi và dẫn lại nội dung của bên thứ ba. Mọi hành động sử dụng các thao tác tại đây, hay dẫn lại post này cho mục đích xấu chúng tôi (cloudcraft) hoàn toàn không chịu trách nhiệm."

Xin chào, tôi có vô tình tìm được một bản copy database (đã decrypted) vụ VNG Zing Database, nên hôm nay rảnh rỗi tôi sẽ mở nó ra và thử cho các bạn xem có gì trong đó. (Một số blog khác cũng đã viết về vụ này, bao gồm luôn cả việc thống kê thông tin và crack password xem số lượng tài khoản trên đã đặt mật khẩu như thế nào nên tôi không viết lại nữa - xem blog của anh thai-vnhacker).

Sau đây là một vài thông tin có trong VNG Zing Database:

![Data VNG Zing](https://cloudcraft.info/wp-content/uploads/2018/05/vng-zing-db-1.png)

(Phần che đi là email của tài khoản)

Tổng quát lại sẽ có một số thông tin quan trọng như: passportid, accountname, password (hash md5 không salt), email, name, birthday, telephone, address

\---------

Bản copy tôi thu được gồm các file .csv (nội dung thì giống như hình ở trên). Hình sau là 1/3 dữ liệu của VNG Zing Database

![](https://cloudcraft.info/wp-content/uploads/2018/05/vng-zing-db-2.png)

Trong bài post này tôi sẽ hướng dẫn căn bản cách trích xuất email và tìm một email nào đó.

  1. Để trích xuất email thì chạy script sau.


    
    
    #!/bin/bash
    
    file=`ls | grep -E "*.csv"`
    
    array_file=( $file )
    
    for(( i=0; i<${#array_file[@]}; i++ ))
    do
    awk -F "," '{print $6 "|" $40}' "${array_file[$i]}" > "${array_file[$i]}.email"
    done

Script này sẽ chạy từng file .csv có trong thư mục run của nó (vứt scripts vào thư mục cần lọc email). Lấy field thứ 6 và 40 tức field email, email2.

Nếu bạn muốn biết email và email2 nó là field thứ mấy thì dùng script sau:
    
    
    head -1 000000_0.csv | tr ',' '\n' | grep -n 'email\|email2'

Kết quả ta sẽ có như sau:

![](https://cloudcraft.info/wp-content/uploads/2018/05/vng-zing-db-3.png)

Giải thích: email sẽ có field number là 6, lastupdatedemail là 35 và email2 là 40. Thật ra thì lastupdatedemail và email2 sẽ không có dữ liệu đâu, nhưng tôi vẫn dùng email2 trong scripts trích xuất email để luyện chút kỹ năng viết shell.

Sau 10 phút chạy thì 1/3 dữ liệu của database đã trích xuất phần email ra hết, ta có những file và nội dung sau:

![](https://cloudcraft.info/wp-content/uploads/2018/05/vng-zing-db-4.png)

![](https://cloudcraft.info/wp-content/uploads/2018/05/vng-zing-db-5.png)

2\. Bây giờ tôi sẽ tìm thử email trong đám email mới trích xuất ở trên.
    
    
    #!/bin/bash
    
    file=`ls | grep -E "*.email"`
    
    array_file=( $file )
    
    for(( i=0; i<${#array_file[@]}; i++ ))
    do
    echo "${array_file[$i]}" >> result.txt
    grep -in "myemail@mail.com" "${array_file[$i]}" >> result.txt
    done

Giải thích: "**myemail@mail.com** " là địa chỉ email tôi muốn tìm. Nếu khớp sẽ xuất ra line và file trích xuất email.

Hình sau mô tả ví dụ tôi thử tìm email có cụm từ "thiensutinhyeu"

![](https://cloudcraft.info/wp-content/uploads/2018/05/vng-zing-db-6.png)

Giải thích: 000000_0.scv.email cho biết bạn đang tìm được ở file nào, 5280 là line number và phần còn lại là địa chỉ email khớp với pattern.

Kết 1: Các bạn có thể tùy ý thiết lập để trích xuất dữ liệu làm sao mà dễ dàng tái sử dụng và nhanh truy xuất nhất có thể.

Kết 2: Các bạn có thể biết được những tài khoản trên dùng mật khẩu gì (crach password) bằng cách sử dụng hashcat với wordlists vietnamese password. Với những wordlists tốt sẽ crack được 99% số tài khoản trong VNG database này. Cách mật khẩu được lưu là dùng md5. (Tham khảo [link](https://www.4armed.com/blog/hashcat-crack-md5-hashes/))
