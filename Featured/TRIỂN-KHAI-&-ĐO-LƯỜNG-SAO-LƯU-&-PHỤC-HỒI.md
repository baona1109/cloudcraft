---
title: "TRIỂN KHAI & ĐO LƯỜNG SAO LƯU & PHỤC HỒI"
date: 2019-12-25 15:08:55
categories: [Featured, General]
---

_**Sau giai đoạn lập kế hoạch hiệu quả, việc thực hiện theo kế hoạch và tiến hành đo lường sẽ hiện thực hóa toàn bộ ý tưởng và kì vọng trở thành hiện thực, đi vào hoạt động của hệ thống.**_

# Thuật ngữ sử dụng trong tài liệu

  * **_Time Frame_** _: khung thời gian để thực hiện thao tác, mỗi thao tác có thể chiếm 1 hoặc nhiều time frame. Đơn vị tính của Lead Time & Cycle Time là time frame. Một time frame có thể là 5 phút, 10 phút hoặc 3 phút, tùy thuộc vào loại dữ liệu và tiêu chí đo lường._
  * **_Lead Time_** _: tổng thời gian từ lúc backup job được đặt ra, cho đến khi được kết thúc (giải phóng tài nguyên)._
  * **_Queue Time_** _: thời gian backup job phải chờ để được thực hiện, việc chờ này có thể do nhiều nguyên nhân: không đủ tài nguyên, có quá nhiều backup job cùng một lúc..._
  * **_Cycle Time_** _: tổng thời gian từ lúc backup job thực sự được thực hiện (không bao gồm Queue Time), cho đến khi hoàn thành._
  * **_Waste Time_** _: được tính bằng Lead Time - Cycle Time. Waste Time bằng tổng thời gian của Queue Time, Parity Check Time, De-Duplicate time, Consistency Check Time..._



# Các chiến lược thực hiện (thứ tự thao tác thực hiện)

## Chiến lược ưu tiên tốc độ (Speedy Implementation Priority - SIP)

**Ý TƯỞNG** : Với chiến lược ưu tiên tốc độ (Speedy Implementation Priority - SIP), thời gian thực hiện sao lưu & phục hồi được đặt lên hàng đầu và ưu tiên hơn cả; thời gian thực hiện càng ngắn, chiến lược càng được đánh giá là thành công. **MỤC TIÊU** : Rút ngắn thời gian tiêu tốn cho việc sao lưu & phục hồi đối với dữ liệu. Phù hợp với các loại dữ liệu không quá quan trọng nhưng yêu cầu khắt khe về thời gian cũng như tài nguyên cho việc sao lưu & phục hồi. **ĐO LƯỜNG** : Dựa trên công thức: **Waste Time = Lead Time - Cycle Time**. Ta có thể đánh giá sự thành công của SIP dựa trên số lượng Time Frame của Waste Time, số lượng Time Frame càng nhỏ, Waste Time càng nhỏ, hiệu quả của chiến lược càng cao.

## Chiến lược ưu tiên độ phủ (Coverage Implementation Priority - CIP)

**Ý TƯỞNG** : Đối với chiến lược ưu tiên độ phủ (Coverage Implementation Priority - CIP), tổng lượng dữ liệu được sao lưu & phục hồi sẽ là thông số chính được quan tâm. Càng nhiều dữ liệu được sao lưu & phục hồi sẽ càng có nhiều dữ liệu được an toàn, không quan tâm đến thời gian thực hiện.  **MỤC TIÊU** : Mở rộng lượng dữ liệu được sao lưu & phục hồi. Phù hợp với các loại dữ liệu cực lớn và cực kỳ quan trọng, tính biến động dữ liệu (churn rate) nhỏ, khắt khe về tính an toàn dữ liệu. **ĐO LƯỜNG** : Thông số chính để đo lường cho CIP chính là dung lượng của dữ liệu gốc (original data) được sao lưu & phục hồi. Lượng dữ liệu gốc càng nhiều, tính thành công của chiến lược càng cao.

## Chiến lược ưu tiên độ ổn định (Stability Implementation Priority - STIP)

**Ý TƯỞNG** : Chiến lược ưu tiên độ ổn định (Stability Implementation Priority - STIP) hướng đến độ ổn định và nhất quán trong dữ liệu được sao lưu & phục hồi nhiều hơn tất cả các khía cạnh. Đây cũng là chiến lược cần sự cân đối giữa độ phủ và thời gian, đồng thời cũng cần đảm bảo tỉ lệ thành công của thao tác sao lưu & phục hồi. **MỤC TIÊU** : Đảm bảo một con số cam kết thực hiện thành công thao tác sao lưu & phục hồi. Phù hợp với các loại dữ liệu hỗn hợp, dữ liệu vừa có tính biến động (churn rate) trung bình đến cao, vừa có độ quan trọng nhất định, đòi hỏi khắt khe về việc bảo vệ phục hồi dữ liệu khi có sự cố. **ĐO LƯỜNG** : Đối với STIP có rất nhiều thông số để đo lường, như: tỉ lệ thất bại (failure rate), % thực hiện lại/thành công (% re-work/completed), thời gian trung bình để phát hiện sự cố (mean time to detect)...   Bảng so sánh các chiến lược: | **CHIẾN LƯỢC ƯU TIÊN TỐC ĐỘ** | **CHIẾN LƯỢC ƯU TIÊN ĐỘ PHỦ** | **CHIẾN LƯỢC ƯU TIÊN ỔN ĐỊNH**  
---|---|---|---  
**THỜI GIAN** | Ngắn (nhanh) | Dài (chậm) | Cân đối vừa phải  
**CHI PHÍ LƯU TRỮ** | Thấp (ít) | Cao (nhiều) | Cân đối vừa phải  
**ĐỘ QUAN TRỌNG DỮ LIỆU** | Thấp, Trung bình | Cao | Cao  
**ĐỘ BIẾN ĐỘNG DỮ LIỆU** | Cao | Thấp | Trung bình, Cao  
**THÔNG SỐ ĐO LƯỜNG** | \- No. of Time Frame \- Mean time to backup (MTTB) \- Mean time to Queue (MTTQ) \- Mean time to Recover (MTTR) | \- Storage Usage \- Duplication Rate \- Original Data Backup Amount | \- Failure Rate \- % re-work/completed \- Mean time to Detect (MTTD) \- Recovery Availability  
 

# Các biểu đồ đo lường dựa trên loại thông số

Chúng ta hãy xem lại sơ đồ được nhắc đến ở một tài liệu về đo lường sao lưu và phục hồi: ![](https://cloudcraft.info/wp-content/uploads/2019/12/trien-khai-do-luong-sao-luu-phuc-hoi-1.png) Với mỗi loại thông số sẽ có một dạng sơ đồ thể hiện để trở thành cở sở để thực hiện các thao tác quản trị/tối ưu hóa/rà soát lỗi của hệ thống sao lưu phục hồi. ![](https://cloudcraft.info/wp-content/uploads/2019/12/trien-khai-do-luong-sao-luu-phuc-hoi-2.png)

_Sơ đồ thể hiện các thông số MTTB, MTTQ & MTTR (càng thấp càng tốt)_

![](https://cloudcraft.info/wp-content/uploads/2019/12/trien-khai-do-luong-sao-luu-phuc-hoi-3.png)

_Sơ đồ thể hiện các thông số Failure Rate (càng thấp càng tốt)_

![](https://cloudcraft.info/wp-content/uploads/2019/12/trien-khai-do-luong-sao-luu-phuc-hoi-4.png)

_Recover Availability (SLA) tỉ lệ nghịch với Failure Rate_
