---
title: "PHƯƠNG PHÁP LẬP KẾ HOẠCH SAO LƯU HIỆU QUẢ"
date: 2019-12-25 14:53:21
categories: [Featured, General]
---

_**Để có một cơ chế sao lưu & phục hồi hiệu quả, việc lập kế hoạch trước khi thực hiện là viên gạch đầu tiên và quan trọng nhất trong toàn bộ quá trình.**_

# Các khía cạnh cần quan tâm khi lập kế hoạch

Sao lưu & phục hồi là một quy trình đòi hỏi độ hiệu quả trong đó hiệu quả được đo lường bởi thao tác phục hồi, nhưng lại có được nhờ thao tác sao lưu.  Theo lý thuyết của sự giới hạn (Theory of Constraint – được nhắc đến trong PMP): “ _Trong bất kì một quy trình nào cũng sẽ có ít nhất một điểm giới hạn, muốn nâng cao hiệu năng của quy trình, việc cần làm là cải thiện điểm giới hạn đó_ ”. Sơ đồ bên dưới chỉ ra 3 khía cạnh cần quan tâm khi lập kế hoạch cho quy trình sao lưu & phục hồi. ![Aspect Model](https://cloudcraft.info/wp-content/uploads/2019/12/phuong-phap-lap-ke-hoach-sao-luu-hieu-qua-5.png) Về thời gian (time), đây là yếu tố ảnh hưởng lớn nhất đến thao tác quản trị khi thực hiện sao lưu & phục hồi. Thời gian có thể phần nào nói lên được phạm vi & chi phí của việc sao lưu & phục hồi.  Nếu thời gian thực hiện sao lưu & phục hồi quá lớn, đồng nghĩa rằng doanh nghiệp của bạn đã phải trả thêm một phần chi phí kha khá cho công việc này như chi phí lưu trữ dữ liệu sao lưu, chi phí điện, tài nguyên hệ thống, etc.  Và tương tự, nếu thời gian sao lưu và phục hồi ngắn hơn, doanh nghiệp cũng đã phần nào đánh giá được phạm vi và chi phí của thao tác đang ở mức thấp. Bằng cách đo lường thời gian, doanh nghiệp có thể tối ưu được chi phí và phạm vi phục vụ của việc sao lưu và phục hồi. ![](https://cloudcraft.info/wp-content/uploads/2019/12/phuong-phap-lap-ke-hoach-sao-luu-hieu-qua-4.png) Về phạm vi (scope), là yếu tố ảnh hưởng chi phí & thời gian một cách trực tiếp. Thử làm một phép toán nho nhỏ, doanh nghiệp đang giữ trong tay 1TB dữ liệu cần được sao lưu & phục hồi, họ đang lên kế hoạch sao lưu mỗi đêm vào 12:00AM. Kết quả đo lường về thời gian, mỗi đêm hệ thống mất 2h (120 phút) để thực hiện Incremental Backup và vào cuối tuần mất 6h để thực hiện Full Backup, tổng dung lượng sao lưu sau 1 tuần là >5TB. Qua đó, chúng ta cũng thấy rõ, chỉ riêng chi phí mà doanh nghiệp phải tiêu tốn cho việc lưu trữ dữ liệu sao lưu cũng đã rất hao tốn. Báo cáo của IDC nhận thấy: _“cứ mỗi 1$ doanh nghiệp chi trả cho dung lượng lưu trữ thì sẽ mất 4$ để giải quyết vấn đề phục hồi và bảo đảm độ sẵn sàng cho chúng”_ Nếu thu hẹp phạm vi (rút gọn dữ liệu) và thay đổi phạm vi cho từng loại dữ liệu, bạn có thể tiết kiệm được 30 phút mỗi đêm cho quá trình Incremental Backup, và tiết kiệm được đến 1h vào cuối tuần khi thực hiện Full Backup, đồng thời giảm thiểu tổng dung lượng sao lưu sau 1 tuần xuống mức xấp xỉ 3.5TB.  Như vậy, bằng phương pháp thu hẹp phạm vi & tối ưu hóa phạm vi cho đặc thù loại dữ liệu, doanh nghiệp đã tiết giảm được thời gian & chi phí cho thao tác này. Yếu tố cuối cùng là chi phí, như phân tích bên trên, yếu tố chi phí chịu ảnh hưởng bởi 2 thành phần còn lại và hơn hết là mối quan tâm của doanh nghiệp khi đầu tư hệ thống sao lưu & phục hồi. Để tối ưu được chi phí, đòi hỏi doanh nghiệp phải có chiến lược, lập kế hoạch việc sao lưu & phục hồi một cách thực sự hiệu quả. Trong một bối cảnh tương tác giữa 3 yếu tố kể trên, người quản trị cần có những động thái cân đối giữa các yếu tố để tạo nên một kế hoạch sao lưu & phục hồi hiệu quả. Kết quả mong đợi của sự cân đối chính là **HIỆU QUẢ**.

# Lập kế hoạch như thế nào là hiệu quả? Các thông số cần đo lường.

Để biết được độ hiệu quả của việc sao lưu & phục hồi, chúng ta cần đo lường dựa trên các thông số để có sự so sánh trước/sau. Châm ngôn “ _KHÔNG BAO GIỜ KHẲNG ĐỊNH, KHI BẠN KHÔNG CÓ DỮ LIỆU CHỨNG MINH_ ”.

## Sơ đồ quy trình Sao lưu & Phục hồi

Dưới đây là một gợi ý về quy trình Sao lưu & Phục hồi chung nhất mà chúng ta có thể dùng để hiệu chỉnh linh động sao cho phù hợp với doanh nghiệp. ![](https://cloudcraft.info/wp-content/uploads/2019/12/phuong-phap-lap-ke-hoach-sao-luu-hieu-qua-1.png.png)

## Đo lường dựa trên sơ đồ - Các thông số

Để tính toán được mức độ hiệu quả, hơn hết là cơ sở để cải thiện quy trình, chúng ta mặc nhiên cần đến các thông số đo lường, và những thông số này cần thực sự có ý nghĩa hơn là chỉ để “nhìn”.  ![](https://cloudcraft.info/wp-content/uploads/2019/12/phuong-phap-lap-ke-hoach-sao-luu-hieu-qua-3.png) Các thông số dùng để đo lường có thể chia làm 4 nhóm như sau: ![](https://cloudcraft.info/wp-content/uploads/2019/12/phuong-phap-lap-ke-hoach-sao-luu-hieu-qua-2.png)
