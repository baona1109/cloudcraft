---
title: "Soft Delete của Azure Storage Blob: Trứng phục sinh của dữ liệu đã xóa"
date: 2018-08-07 10:47:15
categories: [Cloud Computing, Azure]
---

Ngày 30 tháng 5 vừa qua, Microsoft Azure đã chính thức mở tính năng Soft Delete của Azure Storage Blob, tính năng này được mở trên tất cả các regions của Azure Cloud. Soft Delete đúng với cái tên của nó, khi tính năng này được bật, tất cả các dự liệu đã bị delete khỏi blobs hoặc blob snapshots đều có thể được lưu và khôi phục. 

# Cơ cấu hoạt động

Khi dữ liệu bị xóa đi, hành động xóa sẽ mặc định là xóa tạm thời thay vì xóa vĩnh viễn. Trong trường hợp ghi đè dữ liệu, nếu tính năng soft delete đã bật, dữ liệu ghi đè sẽ được lưu giữa lại một phiên bản snapshot, đồng nghĩa người quản trị có thể khôi phục dữ liệu cũ. Người quản trị hoàn toàn có thể cấu hình thời gian hết hạn của dữ liệu đã bị xóa. Giả sử dữ liệu đang có là D0, D0 sẽ bị ghi đè bởi D1. Khi ghi đè, D0 sẽ trở thành snapshot của dữ liệu, D1 sẽ là dữ liệu hiện hữu. Trong trường hợp D1 cũng bị xóa, thì cả D0 và D1 cũng đều nằm trong kho dữ liệu soft delete cho đến thời gian hết hạn. Lưu ý: Soft Delete hoàn toàn có thể hoạt động với tất cả các Storage Blob mà không cần thay đổi hay tạo mới Storage Blob. Soft Delete chỉ hoạt động với object-level, đối với các trường hợp xóa Storage Container hoặc Storage Account đều không hỗ trợ. Dữ liệu trong Soft Delete vẫn sẽ được tính phí tương tự như dữ liệu thật. Tham khảo biểu phí tại [đây](https://azure.microsoft.com/pricing/details/storage/blobs/). Khi tạo mới Storage Account, mặc định tính năng Soft Delete sẽ không được kích hoạt, và Soft Delete cũng mặc định không kích hoạt cho những Storage Account đã tồn tại. Người quản trị có thể vào bất/tắt tính năng đó bất cứ lúc nào. 

# Kích hoạt Soft Delete

Soft Delete có thể được kích hoạt bằng nhiều cách: 

  * Azure Portal
  * .NET Client Library (version 9.0.0 hoặc cao hơn)
  * Java Client Library (version 7.0.0 hoặc cao hơn)
  * Python Client Library (version 1.1.0 hoặc cao hơn)
  * Node.js Client Library (version 2.8.0 hoặc cao hơn)
  * Powershell (version 5.3.0 hoặc cao hơn)
  * CLI 2.0 (version 2.0.27 hoặc cao hơn)
  * Storage Services REST API (version 2017-07-29 hoặc cao hơn)

Để kích hoạt tính năng Soft Delete trên Azure Portal, vào giao diện quản trị của Storage Account và chọn Soft Delete ở phía bên trái. Sau đó Enable, chọn thời gian giữ bản soft delete sau đó chọn Save. ![](https://cloudcraft.info/wp-content/uploads/2018/08/soft-delete-cua-azure-storage-blob-trung-phuc-sinh-cua-du-lieu-da-xoa-01.jpg) Tham khảo thêm [tài liệu Microsoft](https://docs.microsoft.com/en-us/azure/storage/blobs/storage-blob-soft-delete).
