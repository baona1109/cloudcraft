---
title: "MongoDB - Phần 1 Giới thiệu về MongoDB"
date: 2019-02-24 15:52:56
categories: [MongoDB, Database, Linux]
---

# Giới thiệu về MongoDB

![mongodb-logo](https://cloudcraft.info/wp-content/uploads/2019/02/mongodb-logo-min.jpg)

MongoDB (chữ mongo được lấy từ từ humongous trong tiếng Anh), là một NoSQL database. Khác với MySQL hay các loại SQL databse khác chạy theo mô hình database - table - row với số dòng - cột nhất định, schema phức tạp, và phải sử dụng nhiều Join khi query. MongoDB chạy theo mô hình database - collection - document, thay thế mô hình cơ sở dữ liệu dùng table truyền thống bằng các document với định dạng JSON với cấu trúc linh hoạt hơn (MongoDB gọi là BSON).

Với nhiều ưu điểm như hỗ trợ đa nền tảng (Windows, Linux), hiệu năng cao, dễ dàng mở rộng theo chiều ngang (mình sẽ nói thêm trong phần replication và sharding). Hiện tại, MongoDB đang được công ty MongoDB Inc phát triển và có 2 phiên bản như sau: **MongoDB Community Server**

  * Phiên bản MongoDB Community là phiên bản miễn phí cho cộng đồng, hỗ trợ 3 loại hệ điều hành là Linux, Windows và OS X.
  * Tuy nhiên, do dính 1 số vụ lùm xùm về license nên [Fedora](https://www.geekwire.com/2019/mongodbs-licensing-changes-led-red-hat-drop-database-latest-version-server-os/) sẽ loại MongoDB ra khỏi phiên bản RedHat 8 sắp ra mắt của mình ~~(tham thì chết, tội vạ gì)~~. Nhưng ta vẫn có thể add repo bằng tay và tải về chạy bình thường, mình sẽ hướng dẫn trong bài viết sau, bài này chỉ để giới thiệu thôi.

**MongoDB Enterprise Server**

  * MongoDB Enterprise Server là phiên bản thương mại của MongoDB, coi thêm [tại đây](https://www.mongodb.com/lp/download/mongodb-enterprise).

Để tiếp tục tìm hiểu MongoDB thì ta sẽ tìm hiểu một số khái niệm cơ bản của nó 

## Các khái niệm cơ bản trong MongoDB

**Database**

Database chính là tập chứa các collection trong MongoDB. Mỗi database sẽ có một tập file riêng của mình trên file system của hệ thống. Một MongoDB server thường chứa nhiều database trên đó.

**Collection**

Tương tự như Table trong MySQL, Collection là một tập chứa các MongoDB Document. Một điểm khác so với các RDBMS khác đó chính là Collection không bắt buộc một schema cố định nào cả. Các document trong cùng một collection có thể có nhiều field khác nhau. Nhưng thường thì các document trong một collection sẽ có một số field chính tương đồng nhau và có liên quan với nhau.

**Document**

Document là một tập dữ liệu theo dạng key-value, mỗi key sẽ tương ứng với một value. Các document khá linh hoạt về schema, như đã nói ở trên, các document trong cùng một collection không nhất thiết phải có các field hoặc cấu trúc giống nhau. Data trong cùng một field cũng có thể có nhiều kiểu dữ liệu khác nhau.

Ví dụ: field name của document này có thể là string, nhưng ở document khác có thể có kiểu là array, dù 2 document đó cùng nằm chung 1 collection.

==> Túm lại là, có thể tạm coi collection như table, document như row cho dễ hình dung. Nhưng mô hình collection, document linh hoạt hơn so với table - row do có ít schema hơn. Đối với những ai đã xài MySQL mới chuyển qua làm quen MongoDB, các bạn có thể xem thêm bảng so sánh sau để dễ hiểu hơn:  **RDBMS** | **MongoDB**  
---|---  
Database | Database  
Table | Collection  
Tuple/Row | Document  
Column | Field  
Table Join | Embedded Documents  
Primary Key | Primary Key (MongoDB tự tạo Primary key là "_**_id**_ ")   
**Database Server và Client**  
Mysqld/Oracle | mongod (server)  
mysql/sqlplus | mongo (client)  
  **JSON**

Các document của MongoDB sử dụng format JSON (JavaScript Object Notation), đây là một chuẩn lưu trữ, trao đổi dữ liệu đơn giản và gọn nhẹ. Với ưu điểm dễ đọc, dễ hiểu, đa phần các ngôn ngữ lập trình phổ biến hiện nay đều hỗ trợ JSON như: C, C++, C#, Java, JavaScript, Perl, Python,....

Dữ liêu trong JSON được lưu trữ dưới dạng key/value. Một key sẽ tương ứng với 1 value. Value ở đây có thể là một mảng, một chuỗi, một số int, double, mảng hoặc object... Các bạn có thể tìm hiểu kỹ hơn về JSON [tại đây](http://www.json.org/) Dưới đây là một document cơ bản với format JSON, các bạn sẽ thấy nó khá là dễ đọc:
    
    
    {
            "_id" : ObjectId("58c59c8f99d4ee0af9e5ccfc"),
            "title" : "Iron Man",
            "year" : 2008,
            "imdbId" : "tt0371746",
            "mpaaRating" : "PG-13",
            "genre" : "Action, Adventure, Sci-Fi",
            "viewerRating" : 7.9,
            "viewerVotes" : 615059,
            "runtime" : 126,
            "director" : "Jon Favreau",
            "cast" : [
                    "Robert Downey Jr.",
                    "Terrence Howard",
                    "Jeff Bridges",
                    "Gwyneth Paltrow"
            ],
            "plot" : "After being held captive in an Afghan cave, an industrialist creates a unique weaponized suit of armor to fight evil.",
            "language" : "English, Persian, Urdu, Arabic, Hungarian"
    }
    

### Một số ưu điểm của MongoDB

Sau đây là một số ưu điểm của MongoDB:

  * Ít schema hơn: MongoDB là một Document Databse, các document trong cùng một collection không nhất thiết phải giống nhau về số field, loại dữ liệu như trong SQL.
  * Cấu trúc của một Document rất rõ ràng, dễ đọc và dể hiểu.
  * Không cần sử dụng đến các lệnh Join phức tạp.
  * Khả năng mở rộng dữ liệu dễ dàng, không cần phải quá bận tâm về kiểu dữ liệu, khóa chính, khóa ngoại như SQL.
  * Lưu dữ liệu trên RAM, giúp truy xuất dữ liệu nhanh hơn.
  * Dễ dàng Scale Out (Horizontally Scale): mình sẽ nói kỹ về vụ Scale này ở những bài viết sau.



### Khi nào nên dùng MongoDB

Vậy khi nào ta nên dùng MongoDB:

  * Sử dụng document để lưu trữ dữ liệu, dễ dàng thêm bớt, mở rộng dữ liệu tùy ý.
  * Ứng dụng có tính chất Insert nhiều (write-intensive)
  * Cần cơ chế Replication và High Availabity.
  * Cần cơ chế Sharding (sẽ nói rõ trong phần sau).



Một số use case phổ biến của MongoDB là Hệ thống quản lý nội dung số, Các hệ thống phân tích dữ liệu lớn, Quản lý danh mục các sản phẩm trong thương mại điên tử, các ứng dụng social...

### Khi nào không nên dùng MongoDB

Còn khi nào thì không nên dùng MongoDB?

  * Hệ thống cần xử lý nhiều transaction như các giao dịch mua bán, chuyển tiền, ngân hàng. Đối với các trường hợp này thì SQL phù hợp hơn hẳn!
  * Cần sử dụng đến các lệnh JOIN.



Hy vọng qua bài viết này, các bạn sẽ nắm được một số kiến thức cơ bản về MongoDB. Mình sẽ nói kỹ thêm về các thao tác cài đặt, quản trị và xây dựng mongodb cluster ở các bài viết sau nhé.

## Tham khảo & Đọc thêm

https://en.wikipedia.org/wiki/MongoDB https://www.tutorialspoint.com/mongodb/mongodb_overview.htm https://viblo.asia/p/tim-hieu-ve-mongodb-4P856ajGlY3
