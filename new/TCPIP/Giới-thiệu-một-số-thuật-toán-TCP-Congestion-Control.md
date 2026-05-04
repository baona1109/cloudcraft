---
title: "Giới thiệu một số thuật toán TCP Congestion Control"
date: 2019-04-24 17:00:02
categories: [TCP/IP, Network]
---

# Giới thiệu một số thuật toán TCP Congestion Control

Trong bài viết này, mình sẽ giới thiệu sơ lược về một số thuật toán Congestion Control từ thuật toán đơn giản nhất là Tahoe cho đến các thuật toán nâng cao khác và so sánh hiệu năng giữa những thuật toán này:

## Thuật toán Tahoe

Đây là thuật toán kiểm soát tắt nghẽn cơ bản và dễ hiểu nhất

### _Đặc trưng_

Tahoe tăng congestion window (cwnd) theo hàm mũ trong giai đoạn Slow Start và tăng tuyến tính trong giai đoạn congestion avoidance.

Tahoe dùng một timer canh giờ để xét xem liệu một gói tin có “timeout” hay chưa.

Trong giai đoạn congestion advoidance, khi phát hiện tắt nghẽn (một gói tin bị time out) thì threshold sẽ bị giảm còn bằng ½ của cwnd lúc đó. Đồng thời cwnd sẽ bị giảm xuống còn 1 và bắt đầu lại từ giai đoạn slow start.

### _Khuyết điểm_

Chính vì sử dụng timer để xét một gói tin bị timeout, nên vấn đề chính ở TCP Tahoe là thời gian chờ timeout để phát hiện gói tin bị mất là khá lâu. Các gói tin ACK không được gởi ngay sau mỗi gói tin mà đươc gởi cộng dồn.

Vì TCP Tahoe sử dụng phương thức “go back N” nên mỗi khi có một gói tin bị mất thì đường truyền sẽ trống trong một khoảng thời gian. Gây lãng phí tài nguyên.Không sử dụng tối ưu băng thông.

## Thuật toán TCP Reno

Tương tự như TCP Reno ở giai đoạn slow start nhưng có một số cải tiến. Giúp phát hiện gói tin bị mất sớm hơn trước khi một gói tin bị timeout. Tăng hiệu suất gởi nhận.

### _Điểm khác biệt_

Với mỗi gói tin được gởi thì các gói ACK trả về sẽ được trả ngay lập tức theo đúng thứ tự của gói tin đã nhận, chứ không cộng dồn các gói ACK như TCP Tahoe.

TCP Reno có cài đặt thêm thuật toán “Fast-Retransmit” khi gặp trường hợp có 3 gói ACK bị _**lặp lại**_. Reno sẽ giảm đặt giá trị của cwnd mới bằng ½ giá trị của cwnd hiện tại. Và đặt ngưỡng threshold mới bằng giá trị của cwnd mới. Đồng thời chuyển nhanh lại gói tin đã bị mất (Fast-Retransmit) và bước vào một pha gọi là Fast Recovery. Nếu sau pha này mà gói ACK của gói tin vừa được gởi lại bị time out thêm lần nữa thì cwnd sẽ bị hạ xuống thành 1 và trở lại giai đoạn Slow Start giống như Tahoe.

Tahoe sử dụng timout để phát hiện tắt nghẽn còn Reno sử dụng cả timer và thuật toán Fast-Retransmit để nhận biết tắt nghẽn.

Tahoe hạ cwnd xuống còn 1 sau khi mất gói tin, còn Reno hạ cwnd xuống ½ của cwnd hiện tại khi mất gói tin (khi phát hiện 3 duplicate ACK).

### _Ưu điểm_

  * Quá trình phục hồi truyền dữ liệu nhanh hơn so với Tahoe (Fast Recovery) vì thường thì thời gian chờ để nhận 3 gói ACK trùng lặp sẽ nhanh hơn là chờ timeout rồi mới send lại gói tin bị mất. Hiệu quả hơn Tahoe.

  * Reno hoạt động tốt khi số lượng gói tin bị mất là tương đối nhỏ.




### _Khuyết điểm_

  * Nếu cwnd size của Reno quá nhỏ (nhỏ hơn 4 gói) thì có thể sẽ không nhận đủ 3 gói ACK để chạy thuật toán.

  * Mỗi lần Reno chỉ có thể phát hiện được một gói tin bị mất và không thể nhận biết được trường hợp có nhiều gói tin bị mất.




## TCP New Reno

Là phiên bản cải tiến thuật toán Re-Trasmit trong giai đoạn Fast Recovery của TCP Reno.

### Khác biệt

Khi ở trong Fast Recovery sẽ có hai trường hợp xảy ra

_**TH1:**_ Nếu _**tất cả**_ gói tin bị mất trước đó được ACK đầy đủ thì New Reno sẽ thoát giai đoạn fast recovery và đặt giá trị cwnd = threshold và chuyển sang giai doạn congestion avoidance như Tahoe.

_**TH2:**_ Nếu chỉ có một phần gói ACK được trả về so với tổng số gói tin đã Re Transmit thì New Reno sẽ giả định rằng gói tin kế tiếp, gần nhất với các gói tin đã được ACK là đã bị mất và sẽ gởi lại các gói tin đó.

New Reno sẽ thoát khỏi giai đoạn Fast Recovery khi tất cả các gói tin bị mất đã được ACK.

### Ưu điểm

  * New Reno có thể phát hiện nhiều gói tin bị mất cùng một lúc và chỉ thoát ra khỏi giai đoạn Fast Recovery khi mà tất cả gói tin bị mất đã được ACK. Khác với Reno là sẽ thoát khỏi giai đoạn này khi chỉ một gói tin đầu tiên bị mất được ACK. Nên không cần phải giảm cwnd nhiều lần.

  * Cho phép gởi lại nhiều gói tin khi Re Transmit.

  * Khi xác suất lỗi nhiều thì New Reno chạy tốt hơn hẳn Reno.




### Khuyết điểm

Phải tốn một round trip time (RTT) để phát hiện mỗi gói tin bị mất. Vì New Reno phải chờ gói ACK cho gói đầu tiên được gởi lại trở về mới có thể xác định được gói bị mất kế tiếp.

## TCP Vegas

Chống tắt ngẽn hiệu quả và không tốn băng thông. Là một phiên bản cải tiến của Reno và có 3 thay đổi chính.

### Điểm khác biệt

Đề xuất ra thuật toán phát hiện tắt nghẽn mới, phát hiện tắt nghẽn trước khi bị mất gói tin. Xác định thời gian bị delay của packet để nhận biết tắt nghẽn.

Không cần nhiều gói duplicate ack để phát hiện mất gói tin. 5 điểm khác biệt chính của Vegas so với Reno:

  1. Tính toán thời gian RTT (round trip time) chính xác hơn. Dẫn đến tính toán thời gian timeout hiệu quả hơn và có thể quyết định chính xác hơn lúc nào thì nên Re Transmit lại gói tin.

  2. Cơ chế mới xác định thời điểm thích hợp để Re Transmit. Khi nhận được 2 gói duplicate ACK, Vegas sẽ kiểm tra thời gian hiện tại và thời gian gởi tương ứng của gói tin đó. Nếu khoảng chênh lệch này lớn hơn thời gian timeout thì Vegas sẽ gởi lại gói tin mà không cần chờ thêm một gói duplicate ACK nữa.

  3. Thay đổi cách giảm Window size (cwnd). Vegas chỉ giảm cwnd khi gói tin được re transmit trước đó là gói tin được gởi sau khi giảm cwnd (tức 2 gói tin bị mất không cùng một RTT).

  4. Kiềm chế tăng đột biến (Spike Suppression).

  5. Phát hiện và tránh tắt nghẽn hiệu quả.




Vegas chống tắt nghẽn hiệu quả hơn và không lãng phí băng thông như các thuật toán khác khi tăng tốc độ gởi lên quá cao rồi lại cắt giảm đột ngột.

### Ưu điểm

  * Phát hiện và và Re Transmit nhiều gói tin trước khi timeout xảy ra.

  * Không cần phải chờ đủ 3 gói duplicate ACK để gởi lại gói tin.

  * Phát hiện tắt nghẽn sớm trước khi gói tin bị mất.

  * Ít phải gởi lại gói tin.

  * Không giảm cwnd quá sớm.

  * Tận dụng băng thông tốt hơn.




### Khuyết điểm

  * Cơ chế phát hiện tắt nghẽn phụ thuộc quá nhiều vào việc tính toán RTT.




## TCP SACK – Selective Acknowledgments

Là phiên bản mở rộng của Reno. Giải quyết các vấn đề mà Reno và New Reno mắc phải. Vẫn giữ lại giai đoạn Slow Start, Fast Re Transmist của Reno

### Điểm khác biệt

SACK yêu cầu mỗi gói tin phải được ACK riêng biệt chứ không được cộng dồn. Mỗi gói ACK cần phải có một _trường_ miêu tả cụ thể rằng gói ACK này là ACK cho gói tin nào.

SACK sử dụng phương thức _“selective N”_ cho phép bên nhận thông báo chính xác cho bên gởi biết gói tin nào đã bị mất, không cần phải gởi lại toàn bộ các gói tin kể từ gói tin bị mất.

### Ưu điểm

  * Gởi lại chính xác gói tin bị mất.

  * Giảm tải băng thông trong giai đoạn Fast Re Transmist do chỉ cần gởi đúng các gói bị mất.




### Khuyết điểm

  * Khó cài đặt. Cả bên gởi và bên nhận đều phải cài đặt SACK thì mới hoạt động được.




## TCP FACK – Forward Acknowledment

FACK là một thuật toán tắt nghẽn mới, được thiết kế để có thể sử dụng lại một số tính năng của SACK.

### Điểm khác biệt

Điểm đặc biệt của FACK là dự đoán trạng thái của mạng một cách chính xác nhờ vào gói tin có số sequence number lớn nhất (hay gói tin được forward-most) được gởi tới bên nhận.

Mục tiêu chính của FACK là thực hiện chính xác giai đoạn kiểm soát tắt nghẽn khi Fast Recovery bằng cách ước tính chính xác lượng gói tin bị gởi sai lệch trên đường truyền.

### Ưu điểm

  * Dự đoán lượng gói tin bị gởi sai chính xác hơn SACK.

  * Có thể xử lý được những trường hợp bị mất gói tin quá nghiêm trọng.




### Khuyết điểm

  * Chưa được triển khai rộng rãi.

  * Khó cài đặt.




# Bảng so sánh

**Tên giao thức** |  **Đặc điểm** |  **Ưu điểm** |  **Khuyết điểm**  
---|---|---|---  
_**Tahoe**_ | 

  * Dùng timeout để xác định congestion.
  * Quay lại giai đoạn slow start (cwnd = 1) khi phát hiện congestion.

| 

  * Có thể phát hiện tắt nghẽn.

| 

  * Gây lãng phí băng thông khi chờ timeout.
  * Độ trễ rất cao

  
_**Reno**_ | 

  * Cài đặt thêm thuật toán ReTransmit để gởi lại các gói tin bị mất
  * Dùng dấu hiệu 3 duplicate ACK để phát hiện mất gói tin.
  * Thêm giai đoạn phục hồi Fast Recovery

| 

  * Quá trình phục hồi truyền dữ liệu nhanh hơn so với Tahoe.
  * Reno hoạt động tốt khi số lượng gói tin bị mất là tương đối nhỏ.

| 

  * Nếu cwnd size của Reno quá nhỏ (nhỏ hơn 4 gói) thì có thể sẽ không nhận đủ 3 gói ACK để chạy thuật toán.
  * Không thể nhận biết được nhiều gói tin bị mất một lần.
  * Không biết chính xác gói tin nào đã được ACK.

  
_**New Reno**_ | 

  * New Reno chỉ thoát khỏi giai đoạn Fast Recovery khi tất cả các gói tin bị mất đã được ACK.
  * Có thể phát hiện nhiều gói tin bị mất.

| 

  * New Reno có thể phát hiện nhiều gói tin bị mất cùng một lúc.
  * Cho phép gởi lại nhiều gói tin khi Re Transmit.

| 

  * Phải tốn một round trip time (RTT) để phát hiện mỗi gói tin bị mất.

  
|  | 

  * Khi xác suất lỗi nhiều thì New Reno chạy tốt hơn hẳn Reno.

|   
---|---|---|---  
_**Vegas**_ | 

  * Tính toán thời gian RTT chính xác hơn.
  * Thay đổi cách giảm Window size (cwnd).
  * Cơ chế mới xác định thời điểm thích hợp để Re Transmit.
  * Kiềm chế tăng đột biến (Spike Suppression).
  * Phát hiện và tránh tắt nghẽn hiệu quả.

| 

  * Phát hiện và và Re Transmit nhiều gói tin trước khi timeout xảy ra.
  * Không cần phải chờ đủ 3 gói duplicate ACK để gởi lại gói tin.
  * Phát hiện tắt nghẽn sớm.
  * Ít phải gởi lại gói tin.
  * Không giảm cwnd quá sớm.
  * Tận dụng băng thông tốt hơn.

| 

  * Cơ chế phát hiện tắt nghẽn phụ thuộc quá nhiều vào việc tính toán RTT.

  
_**SACK**_ | 

  * Mỗi gói tin ACK được thêm vào một trường miêu tả gói ACK này là cho gói tin nào trước đó.
  * SACK sử dụng phương thức “selective-N”.

| 

  * Gởi lại chính xác gói tin bị mất.
  * Giảm tải băng thông trong giai đoạn fast re transmit do chỉ cần gởi đúng các gói bị mất.

| 

  * Khó cài đặt, phải cài đặt cho cả bên gởi và bên nhận

  
_**FACK**_ | 

  * Điểm đặc biệt của FACK là dự đoán trạng thái của mạng một cách chính xác nhờ vào gói tin có số sequence number lớn nhất (hay gói tin được forward-most) được gởi tới bên nhận.
  * Mục tiêu chính của FACK là thực hiện chính xác giai đoạn kiểm soát tắt nghẽn khi Fast Recovery bằng cách ước tính chính xác lượng gói tin bị gởi sai lệch trên đường truyền.

| 

  * Dự đoán lượng gói tin bị gởi sai chính xác hơn SACK.
  * Có thể xử lý được những trường hợp bị mất gói tin quá nghiêm trọng.

| 

  * Chưa được triển khai rộng rãi.
  * Khó cài đặt.

  
  Bài viết sẽ liên tục được update để cập nhật thêm nội dung mới
