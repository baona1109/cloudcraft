---
title: "RÀ SOÁT LỖI & TỐI ƯU HÓA HỆ THỐNG SAO LƯU DOANH NGHIỆP"
date: 2019-12-26 15:22:45
categories: [Featured, General]
---

# **CÁCH TIẾP CẬN VỚI TỐI ƯU HÓA**

Đối với hầu hết tất cả doanh nghiệp, tổ chức, việc duy trì quy trình sao lưu và phục hồi hằng ngày là một công việc có rất nhiều vấn đề phức tạp xoay quanh. Và như một kết quả tất yếu, đa số các lãnh đạo của doanh nghiệp có rất ít niềm tin vào bộ phận IT trong việc khôi phục lại dữ liệu khi có sự cố xảy ra, chứ chưa nói đến việc thực hiện tối ưu và đơn giản hóa việc sao lưu và phục hồi. Để thực hiện việc đơn giản hóa quy trình sao lưu & phục hồi, hầu như các tổ chức đều đổ thêm chi phí để bộ phận IT có thể trang bị thêm các giải pháp phụ trợ mới và kết thúc với việc càng khiến quy trình và công việc hằng ngày trở nên phức tạp hơn mà vẫn chưa tìm thấy lời giải cho bài toán tối ưu hóa.

# **LÝ DO TẠI SAO QUY TRÌNH SAO LƯU & PHỤC HỒI TRỞ NÊN PHỨC TẠP**

  1. Quá trình planning chưa chính xác, thất bại trong việc đặt ra mục tiêu, chuẩn quy tắc và chiến lược phù hợp
  2. Tổ chức thiếu đi việc luyện tập / diễn tập các quy trình thực hiện sao lưu & phục hồi (Backup & Recovery Practices), do đó dẫn đến lúng túng, khó khăn trong việc xử lý khi có sự cố xảy ra
  3. Thiếu đi sự giám sát các quy trình sao lưu hằng ngày
  4. Áp dụng các giải pháp phức tạp hơn yêu cầu đặt ra
  5. Quá quan tâm đến yếu tố kỹ thuật mà quên đi các vấn đề về tối ưu dữ liệu, quy trình
  6. Không bắt kịp những xu hướng mới tối ưu, đơn giản và phù hợp hơn cho doanh nghiệp



# **PHƯƠNG THỨC VÀ CÁCH TIẾP CẬN TỐI ƯU HÓA QUY TRÌNH SAO LƯU & PHỤC HỒI**

Đối với sao lưu & phục hồi, cách tiếp cận tối ưu hóa quan trọng nhất mà mỗi doanh nghiệp nên lưu tâm chính là lập kế hoạch (Planning) một cách chính xác, đơn giản, tập trung vào mục tiêu cần bảo vệ.  Ngoài việc thực hiện Planning chính xác, việc thao tác vận hành thực hiện sao lưu & phục hồi (Backup & Recovery Practices) sau đó cũng cần phải được thực hiện nghiêm túc, theo kế hoạch đề ra. Từ đó tạo thói quen hợp lý, giảm bớt các lỗi, sự cố hoặc vấn đề xảy ra trong quá trình phục hồi. Bên dưới là 1 số các phương thức và cách tiếp cách để tối ưu hóa quy trình sao lưu & phục hồi mà doanh nghiệp có thể cân nhắc và áp dụng:

## **_1 – ÁP DỤNG CÁC CHUẨN QUY TẮC PHÙ HỢP CHO DOANH NGHIỆP CỦA BẠN (BACKUP RULES & STRATEGY)_**

_Tham khảo:_

  * _[3-2-1 Backup Practice](https://www.unitrends.com/blog/3-2-1-backup-sucks)_
  * [How to follow the 3-2-1 backup rule with Veeam Backup Replication](https://www.veeam.com/blog/how-to-follow-the-3-2-1-backup-rule-with-veeam-backup-replication.html)

Một trong những chuẩn quy tắc chúng ta thường nghe và được khuyến nghị bởi chính phủ Mỹ chính là quy tắc sao lưu “3-2-1”. Đối với quy tắc này, chúng ta nên ghi nhớ các yếu tố sau:

  * **3:** Đối với dữ liệu, bạn phải luôn có ít nhất **ba (3) bản sao**. Lý do việc lưu trữ dữ liệu thành 3 bản sao giúp chúng ta giảm thiểu cơ hội mất dữ liệu Giả sử bạn lưu dữ liệu của mình vào ổ đĩa 1 và sao lưu vào 2 thiết bị lư trữ khác. Nếu xác suất lỗi của ổ 1 và ổ 2 là 1/100 thì xác suất thất bại đồng thời của cả hai ổ đĩa là 1/100 x 1/100 = 1 / 10.000. Với ba bản sao lưu, xác suất giảm xuống còn 1/1.000.000.
  * **2:** Sử dụng **hai (2)** công nghệ lưu trữ dữ liệu khác nhau làm giảm khả năng mất dữ liệu xảy ra. Chẳng hạn như ta có thể tận dụng NAS để lưu trữ 3 bản sao dữ liệu thứ nhất và Tape để lưu trữ 3 bản sao dữ liệu thứ 2.
  * **1:** Ít nhất 1 bản sao dữ liệu phải được lưu trữ ở một vị trí khác (offsite). Qua đó, nếu có hỏa hoạn, trộm cắp hoặc tấn công mạng xảy ra, chúng ta sẽ đảm bảo có ít nhất 1 bản sao dữ liệu đang được bảo vệ an toàn ở một nơi khác. Việc offsite dữ liệu tại một văn phòng khác hoặc sử dụng các dịch vụ lưu trữ đám mây (Cloud Services) sẽ tăng cường tính an toàn và sẵn sàng của dữ liệu.

![](https://cloudcraft.info/wp-content/uploads/2019/12/ra-soat-loi-toi-uu-hoa-he-thong-sao-luu-doanh-nghiep-1.gif) Đương nhiên, quy tắc “3-2-1” không phải là quy tắc duy nhất. Với việc công nghệ ngày càng phát triển và sự bùng nổ của các dịch vụ sao lưu đám mây (Backup-as-a-Services), chúng ta ngày càng có thêm các quy tắc mới tiến bộ hơn như: **Quy tắc 3-1-2:** trong đó chúng ta sẽ có ít nhất 2 bản Offsite Copy được cung cấp bởi các dịch vụ điện toán đám mây và nằm ở 2 vị trí địa lý khác nhau. Qua đó giảm bớt chi phí về media cũng như tăng thêm tính an toàn cho dữ liệu offsite ở mức địa lý. ![](https://cloudcraft.info/wp-content/uploads/2019/12/ra-soat-loi-toi-uu-hoa-he-thong-sao-luu-doanh-nghiep-2.gif) **Quy tắc 3-2-2:** quy tắc này mang đến khả năng hài hòa giữa việc lưu trữ dữ liệu ở tại cục bộ (Premises) và đám mây (Cloud Services). Với cách này, ta sẽ có được lợi thế của cả hai phương thức, trong đó tại cục bộ mang lại khả năng nhanh chóng và mềm dẻo trong phục hồi còn lưu trữ tại đám mây mang lại sự an toàn về mặt dữ liệu offsite cũng như tính liên tục (continuality) khi đảm bảo dữ liệu được offsite an toàn ở hai vị trí địa lý khác nhau,  ![](https://cloudcraft.info/wp-content/uploads/2019/12/ra-soat-loi-toi-uu-hoa-he-thong-sao-luu-doanh-nghiep-3.gif) Ngoài các quy tắc trên, chúng ta có thể còn có những kết hợp khác tùy vào nhu cầu của doanh nghiệp. Qua đó chúng ta thấy, việc xác định đúng quy tắc sao lưu sẽ góp phần tối ưu hóa chiến lược sao lưu của tổ chức, tăng thêm tính an toàn cho dữ liệu và phòng tránh . Việc chọn lựa quy tắc nên dựa trên những đặc thù về dữ liệu của chính tổ chức muốn bảo vệ. Lưu ý: không phải loại dữ liệu nào trong tổ chức cũng phải có 1 quy tắc nhất định. Ví dụ như những dữ liệu không thật sự quan trọng thì chúng ta cũng không nên áp đặt các quy tắc phức tạp khiến việc planning trở nên khó khăn hơn rất nhiều.

## **_2 – NHẤT QUÁN TRONG QUY TRÌNH (STANDARDIZE YOUR PROCESS)_**

Điều quan trọng để tránh các lỗi phức tạp xảy ra thì doanh nghiệp nên áp dụng các quy trình sao lưu một cách nhất quán và thường xuyên kiểm tra bằng cách luyện tập theo các lịch trình đặt sẵn (hằng tuần, hằng tháng hay quý) để đảm bảo rằng các bản sao lưu đã được chạy và kiểm tra đúng cách (validation).  Việc áp dụng các quy trình nhất quán và được thường xuyên kiểm tra bằng cách diễn tập lặp đi lặp lại nhiều lần sẽ đảm bảo nhân sự của tổ chức nắm rõ cách thức xử lý khi có tình huống cần phục hồi xảy ra. Thêm vào đó, trong quá trình diễn tập hằng ngày, chúng ta sẽ phát hiện thêm những lỗi mới, có được những bài học mới (lesson learned) giúp doanh nghiệp có thêm kiến thức, nhanh chóng xử lý các vấn đề gặp phải trong quá trình thực hiện phục hồi. Từ đó, tối ưu hóa và đơn giản hóa được nhiều vấn đề như:

  * Rút ngắn thời gian phục hồi, giảm thời gian gián đoạn cho doanh nghiệp
  * Thu hẹp khoảng và lượng dữ liệu bị mất (data loss) so với RTO/RPO doanh nghiệp đề ra
  * Nâng cao năng lực nhân sự, tạo được tính kế thừa với quy trình nhất quán, tránh việc phụ thuộc quá nhiều về phương diện con người (human factor).



## **_3 - HÃY XEM VIỆC GIÁM SÁT SAO LƯU & PHỤC HỒI LÀ MỘT THAO TÁC HẰNG NGÀY (BACKUP MONITORING)_**

Trong một báo cáo khảo sát từ SolarWinds MSP cho thấy, 32% doanh nghiệp thừa nhận không thực hiện sao lưu hằng ngày (Daily Backup). Càng nguy hiểm hơn nữa, 48% doanh nghiệp cho biết họ rất ít khi theo dõi và giám sát (monitoring) hệ thống sao lưu và dữ liệu sao lưu của mình. Qua đó đặt bản thân chính doanh nghiệp vào tình huống có thể mất lượng lớn dữ liệu khi có sự cố xảy ra. Chúng ta phải luôn ghi nhớ sao lưu không phải lúc nào cũng ngăn chặn mất dữ liệu trong mọi tình huống, nó chỉ là giải pháp giảm thiểu rủi ro khi xảy ra khi có sự cố, mất mát hay hư hại dữ liệu. Do đó doanh nghiệp phải luôn xem sao lưu & phục hồi là một thao tác phải thường xuyên được giám sát và thực hiện hằng ngày. Việc giám sát quy trình sao & lưu phục hồi giúp tăng cường độ an toàn và bảo mật của dữ liệu khỏi các yếu tố nguy hiểm như ransomware, virus. Đã có những tổ chức bị mã hóa toàn bộ dữ liệu lẫn các bản sao dữ liệu bởi những thiếu sót trong giám sát hằng ngày khiến họ hoàn toàn nằm trong sự kiểm soát của hacker. Ngoài việc bảo vệ an toàn dữ liệu, việc giám sát hằng ngày còn đem lại những dữ kiện thích hợp giúp doanh nghiệp nắm rõ tình trạng dữ liệu của mình đang được bảo vệ như thế nào và nhanh chóng kịp thời xử lý, ví dụ như: Nắm rõ tình trạng các bản sao lưu, tình trạng quá hạn của bản sao dữ liệu. Trong trường hợp các bản sao dữ liệu đã quá xa, không còn giá trị sử dụng thì quản trị viên có thể nhanh chóng loại bỏ để giải phóng tài nguyên

## **_4 – LUÔN CẬP NHẬT VÀ THEO DÕI NHỮNG XU HƯỚNG MỚI_**

Sao lưu và phục hồi không phải là một ý tưởng mới, nhưng hầu hết doanh nghiệp ít lưu tâm đến việc cập nhật những xu hướng bảo vệ và sao lưu dữ liệu mới bởi đa phần doanh nghiệp thường thích sử dụng những giải pháp và quy trình quen thuộc hằng ngày.  Nhưng ngay cả các công nghệ quen thuộc như sao lưu vẫn tiếp tục tiến về phía trước và ngày càng trở nên tốt hơn, dễ dàng hơn, nhanh hơn và ít tốn kém hơn.  Một ví dụ là các giải pháp sao lưu trên nền tảng đám mây (Backup-as-a-services), đang có xu hướng mang lại nhiều hiệu quả về chi phí để lưu trữ các bản sao dữ liệu offsite và giảm yêu cầu lưu trữ cục bộ, phù hợp nhiều có các doanh nghiệp SMB có nhu cầu bảo vệ dữ liệu nhanh chóng, kịp thời. Hoặc chẳng hạn như việc tích hợp các giải pháp near-CDP/snapshot để khôi phục nhanh chóng bảo vệ dữ liệu hoặc áp dụng những giải pháp sao lưu liên tục Continuous Data Protection để tối ưu RTO/RPO cho quy trình sao lưu và liên tục bảo vệ dữ liệu. ![](https://cloudcraft.info/wp-content/uploads/2019/12/ra-soat-loi-toi-uu-hoa-he-thong-sao-luu-doanh-nghiep-4.png)
