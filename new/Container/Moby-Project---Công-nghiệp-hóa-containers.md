---
title: "Moby Project - Công nghiệp hóa containers"
date: 2018-02-09 14:50:17
categories: [Container, Docker, Linux]
---

Moby sẽ biến Docker Engine từ kiến trúc đơn nhân sang thành kiến trúc nhiều thành phần có thể tái sử dụng được. Trong không khí DockerCon 2017 diễn ra vào năm ngoái, có một số thông tin đáng chú ý được công bố, nhưng đáng quan tâm nhất vẫn là cái tên [Moby Project](https://mobyproject.org), một project mới và mang tham vọng biến Docker trở thành một công cụ đã đắc lực nay còn đắc lực hơn nữa. Moby Project thực sự là dự án gì? Chính xác thì Moby là một framework để tái tạo lại hệ thống containers mà không cần tạo ra một thứ gì mới hơn hay phải bắt đầu lại từ con số 0. Vẫn còn khá mơ hồ. Nói cho dễ hình dung rằng chúng ta sẽ ánh xạ Moby Project và Docker tương tự như Fedora Project với Red Hat. Cách thức Docker được xây dựng nay đã thay đổi. Red Hat đã định hướng rõ ràng ngay từ đầu khi bắt tay vào tạo nên RHEL, tách Fedora khỏi RHEL. Docker cũng tương tự như vậy, tách Moby Project ra khỏi Docker sẽ hướng đến tương lai hơn là tạo nên một cộng đồng thống nhất. Trước đây, ranh giới giữa cộng đồng và sản phẩm là khá mờ nhạt, cũng giống như việc người trong ngành đóng góp vào một project sẽ ít được nhắc đến hơn là khi họ góp phần tạo nên một sản phẩm có giá trị mang tầm công nghiệp hay được ứng dụng trong môi trường doanh nghiệp. Về mặt kỹ thuật, Moby đã biến Docker Engine từ đơn nhân thành đa nhân và có thể áp dụng vào nhiều trường hợp hơn trước, mang lại tính mềm dẻo và khả cấu hình trên nhiều hệ thống hơn. Docker Team mong muốn phân nhỏ Docker Engine ra thành nhiều thành phần hơn nữa, và những thành phần nhỏ này có thể trở thành một block riêng biệt, tái sử dụng lại trong nhiều trường hợp, và cũng có thể thích ứng với nhiều giải pháp hơn nữa, ví dụ: containerd, LinuxKit, InfraKit, Notary... Mục tiêu hướng đến của Docker Inc. chính là việc tạo nên một cộng đồng phát triển Moby riêng biệt, với hàng triệu sự đóng góp đến từ các contributor, và sẽ sử dụng những sự đóng góp đó biến Moby thành một tiêu chuẩn công nghiệp mang tên Docker. Đây sẽ là sự hòa hợp đáng có cho việc Docker được ứng dụng vào môi trường enterprise nhiều hơn và an toàn hơn khi sử dụng. Moby sẽ là một project thực sự, và Docker sẽ là một sản phẩm. Moby Project sẽ bao gồm các layer sau:

  * Các thành phần lõi
  * Moby
  * Docker Community Edition
  * Docker Enterprise Edition

![](https://cloudcraft.info/wp-content/uploads/2018/02/moby-project-cong-nghiep-hoa-containers-01.jpg) Việc tổ chức Moby project như hình trên sẽ dẫn đến mối quan tâm rằng liệu các component trên có thực sự cần thiết giữa môi trường project và product. Docker là một product đúng nghĩa, nên sẽ quan tâm đến sự tiện dụng cho người dùng hơn là mối quan tâm đến việc tinh chỉnh dịch vụ lõi của sản phẩm. Ví dụ, đối với Moby Project, containerd sẽ không có repository mặc định, nhưng với Docker, repository mặc định sẽ là Docker Hub. Những thay đổi bên trong Moby Project sẽ không hề ảnh hưởng đến người dùng Docker, người dùng vẫn sử dụng Docker như chuyện hằng ngày. Một số đối tượng người sử dụng mà Moby Project và Docker Product hướng đến:

  * Docker Developer/System Builder tìm kiếm nền tảng phát triển Docker để chạy các bài test, các đóng góp vào sản phẩm Docker sử dụng Moby Project.
  * Application Developer tìm kiếm cách đơn giản nhất để chạy ứng dụng bằng container sử dụng Docker CE.
  * Enterprise IT tìm kiếm một sản phẩm "bật phát chạy ngay", nhận hỗ trợ ở mức công nghiệp sử dụng Docker EE.

Và như đã nói ở trên, người dùng vẫn sẽ sử dụng Docker như chuyện hằng ngày vẫn thường làm, command line vẫn như thế, kiến trúc vẫn như cũ. Docker Inc. chỉ đang tạo ra môi trường riêng biệt cho developer của họ, nhắm đến việc phát triển Docker toàn diện và nhanh chóng hơn.
