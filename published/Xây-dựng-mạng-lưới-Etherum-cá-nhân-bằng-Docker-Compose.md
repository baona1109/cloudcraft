---
title: "Xây dựng mạng lưới Etherum cá nhân bằng Docker Compose"
date: 2018-02-05 15:35:48
categories: [Container, Docker, Linux, Blockchain]
---

Etherum được hình thành và phát triển dựa trên những use cases xoay quanh vân đề quản lý định danh và hợp đồng thông minh (smart contracts). Và đó cũng là vấn đề giải quyết chung của công nghệ blockchain. Nếu bạn muốn bước vào thế giới blockchain mà không phải đào sâu nghiên cứu và cũng còn mơ hồ về việc bạn có thực sự quan tâm đến vấn đề xử lý phân tán hay không, thì việc tạo nên một ứng dụng Etherum Network đơn giản dành cho việc testing & developing là việc khả thi. Bạn có thể tham khảo phương pháp build [ứng dụng dựa trên nền tảng open-source của Etherum](https://www.ethereum.org/). Chọn platform cho blockchain luôn là việc cần cân nhắc kỹ, nhưng trong bài này tôi chỉ tập trung vào Etherum với các lý do sau: 

  * Nền tảng open-source với cộng đồng đông đảo.
  * Hỗ trợ Smart Contracts thông qua Solidity (ngôn ngữ lập trình dành riêng cho smart contracts)
  * APIs được cung cấp tài liệu rất chi tiết, và có thể tích hợp với nhiều ngôn ngữ khác nhau từ phía client.
  * Các công cụ hỗ trợ cho developers nhiều.

Các nền tảng sử dụng trong bài này: 
  * Etherum làm nền tảng công nghệ Blockchain và hạ tầng phục vụ tính toán.
  * Meteor cho nền tảng website bao gồm cả backend/frontend của ứng dụng.
  * ReactJS + Redux cho việc thể hiện thông tin và thao tác ứng dụng.
  * React Bootstrap để làm cho giao diện thêm trực quan.
  * Webpack đóng vai trò làm build system.
  * Karma + Mocha làm công cụ testing.

Bây giờ thì chúng ta bắt đầu build Etherum private blockchain cho mục đích testing bằng Docker Compose. Tôi vẫn hay thường sử dụng testrpc để dựng một instance Etherum đơn giản có hỗ trợ APIs và các tài khoản khởi tạo. TestRPC là công cụ khá mạnh, nhanh và cách thức sử dụng cũng khá là đơn giản, gần gủi với việc phát triển một ứng dụng nào đó. Tuy nhiên, khi nhu cầu của bạn cần nhiều hơn thế, nhiều hơn việc testing, và gần với môi trường production như: 
  * Test khả năng ứng phó của từng node trong network một cách chi tiết, như cluster hoặc động bộ dữ liệu thông qua P2P connection chẳng hạn.
  * Kết nối giữa các node sử dụng APIs
  * Truy cập vào JavaScript Console
  * Phân lập tầng developing bằng Geth node
  * Dễ dàng tích hợp, mở rộng cluster mà không phải thực hiện quá nhiều thao tác.

Chính vì lý do này, tôi chọn Docker Compose thay cho TestRPC để đáp ứng tất cả các nhu cầu kể trên. Docker Compose có thể sử dụng lại trên nhiều nền tảng khác nhau, và đảm bảo hệ thống Etherum network trên nhiều hệ thống đó đều được deploy giống nhau. Tôi sẽ sử dụng repository Github của Capgemini với đầy đủ các Dockerfile, Docker Compose script phục vụ cho mục đích tạo nên cluster Etherum network đủ để testing. 

## Khởi tạo Etherum node

Clone Capgemini Github Repository: `$ git clone https://github.com/Capgemini-AIE/ethereum-docker.git` `$ cd etherum-docker` Khởi chạy Etherum node: `$ docker-compose -f docker-compose-standalone.yml up -d` JSON RPC API sau khi khởi chạy Etherum node sẽ được mở trên HTTP port 8545 cho việc truy cập từ bên ngoài vào. Bước tiếp theo, ta thực hiện kết nối geth JavaScript console trên node: `$ docker exec -it bootstrap geth --datadir=~/.etherum/devchain attach` Bây giờ thì bạn có thể truy cập vào JavaScript runtime environment, và thực hiện các thao tác điều khiển như start/stop mining: `$ docker exec -it bootstrap geth --datadir=~/.etherum/devchain --exec 'miner.stop()' attach`

## Khởi tạo Etherum cluster

Để khởi tạo Etherum cluster: `$ git clone https://github.com/Capgemini-AIE/ethereum-docker.git` `$ cd etherum-docker` `$ docker-compose up -d` Cluster được tạo ra sẽ bao gồm 3 nodes: 2 Etherum nodes + 2 netstats node. Netstats UI được mở tại HTTP port 3000. Có thể truy cập thông qua `http://localhost:3000` ![](https://cloudcraft.info/wp-content/uploads/2018/02/xay-dung-mang-luoi-etherum-ca-nhan-bang-docker-compose-01.png) Bạn cũng có thể scale Etherum nodes lên một con số cao hơn bằng cách `$ docker-compose scale eth=3` Như vậy là bạn đã có 1 private Etherum cluster phục vụ cho mục đích testing, cluster có thể scale up/down, in/out.    
