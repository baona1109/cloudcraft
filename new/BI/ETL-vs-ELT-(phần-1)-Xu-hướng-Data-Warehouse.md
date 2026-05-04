---
title: "ETL vs ELT (phần 1): Xu hướng Data Warehouse"
date: 2018-10-01 09:06:01
categories: [BI, Database]
---

Sau sự xuất hiện của Data Warehouse đến nay, ETL (Extract – Transform – Load) vẫn là một cách tiếp cận phổ biến mà người mới bắt đầu thường chọn khi làm việc với Data Warehouse và phân tích dữ liệu. Nhưng cùng với sự phát triển đó, một phương thức khác cũng đã tồn tại song song với ETL, chính là ELT (đổi chỗ L và T; Extract – Load –Transform), ắt hẳn sẽ có các trường hợp sử dụng nhất định cho cả 2 phương thức này sẽ được đề cập trong bài viết dưới đây.  Điểm chung: Cả ETL & ELT đều phục vụ cho cùng 1 mục đích: khối dự liệu lớn và các sự kiện diễn tra với khối dữ liệu được tập kết về một nơi từ nhiều nguồn khác nhau, tất cả các dữ liệu này đều thô và cần phải được xử lý dựa trên đặc thù business của doanh nghiệp. Đòi hỏi đặt ra rằng làm sao cho các dữ liệu này có thể được “làm sạch”, loại trừ dữ liệu không cần thiết, giữ lại các dữ liệu có ích và tạo nên các mối liên kết giữa dự liệu, dữ liệu sẽ sẵn sàng được phân tích theo nhiều cấp độ hơn. Để đáp ứng đòi hỏi đó, ETL & ELT đều phải cho ra cùng một kết quả đó là dữ liệu được mô hình hóa, chuẩn hóa hay thậm chí là đồng nhất về mặt giá trị. Từ đó, mục tiêu cuối cùng hướng đến chính là một bộ dữ liệu “đã qua chế biến”. Điểm qua một vài con số thống kê về Data Warehouse trong năm 2018 – theo báo cáo Data Warehouse Trends Report 2018 của Panoply: 

  * 21% số người được hỏi vẫn chưa có giải pháp Data Warehouse trong hệ thống doanh nghiệp. Một trong những rào cản khi sử dụng Data Warehouse đó chính là suy nghĩ Data Warehouse sẽ không thay đổi quá nhiều về việc xử lý dữ liệu như hiện tại doanh nghiệp vẫn làm vì vậy không nhận ra được lợi lịch của Data Warehouse trong việc giải quyết các vấn đề tồn đọng.
  * 62% số người được khảo sát cảm thấy khó thậm chí là rất khó khi quản lý Data Warehouse. Khi được hỏi, các khảo sát viên nhận được câu trả lời khá tương tự nhau về sự phức tạp, chi phí và bài toán hiệu năng của hệ thống Data Warehouse. Trong số các SMBs với khoảng dưới 250 nhân viên, 75% trong số các công ty này có Data Warehouse và 58% trong số đó cảm thấy rất khó khăn trong việc quản lý Data Warehouse.
  * 81% số người sử dụng Data Warehouse mong muốn tính tự động hóa nhiều hơn nữa trong quy trình. Khi được hỏi về vấn đề tự động hóa để giảm bớt sự phức tạp và chi phí, những người tham gia khảo sát đã chỉ ra rất rõ 4 khía cạnh: thu thập dự liệu từ nhiều nguồn, biến đổi dữ liệu, quản lý dữ liệu và cuối cùng là tối ưu hóa truy vấn.
  * 20% sử dụng PowerBI so với 56% sử dụng Tableau. Việc chọn công cụ BI phụ thuộc khá nhiều vào thói quen của người sử dụng và tính tự chủ khi sử dụng công cụ như: khả năng hiệu chỉnh giao diện, phân hóa dữ liệu, và tích hợp với cloud… bên cạnh đó còn có một số yêu cầu cao hơn về mặt AI và Machine Learning nhưng không có quá nhiều doanh nghiệp đòi hỏi 2 yếu tố này.
  * 45% sử dụng RedShift, 39% sử dụng on-premise data warehouse, 8% sử dụng Azure SQL Server.

![](https://cloudcraft.info/wp-content/uploads/2018/10/etl-vs-elt-phan-1-xu-huong-data-warehouse-1.jpg)

  * Đánh giá về độ phức tạp của Data warehouse: 53% đánh giá Khó, 9% đánh giá Rất Khó, 33% đánh giá Dễ, 5% đánh giá rất dễ.

![](https://cloudcraft.info/wp-content/uploads/2018/10/etl-vs-elt-phan-1-xu-huong-data-warehouse-2.jpg)

  * Tỉ lệ phần trăm số người khảo sát có nhu cầu về tự động hóa trong Data Warehouse: thu thập dữ liệu (25%), chuyển đổi dữ liệu (26%), quản lý dữ liệu (25%) và tối ưu hóa truy vấn (24%).

![](https://cloudcraft.info/wp-content/uploads/2018/10/etl-vs-elt-phan-1-xu-huong-data-warehouse-3.jpg)  
