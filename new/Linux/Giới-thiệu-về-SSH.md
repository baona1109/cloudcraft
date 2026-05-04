---
title: "Giới thiệu về SSH"
date: 2017-11-29 17:15:54
categories: [Linux]
---

Để kết nối và quản trị các máy chủ Linux thì SSH chính là cách thức tiện lợi và an toàn nhất. **_SSH_** viết tắt cho cụm từ **_Secure Shell_** , cho phép người dùng thực thi các lệnh, cấu hình thay đổi hệ thống từ xa qua giao diện command line một cách an toàn mà không sợ bị bên thứ 3 can thiệp. 

# Sơ lược về SSH

## Giới thiệu về SSH

Kết nối SSH sử dụng mô hình client-server. Người dùng trên laptop cá nhân sẽ đóng vai trò là client còn máy chủ Linux sẽ đóng vai trò là server nhận kết nối đièu khiển từ xa. Server SSH sẽ chạy ngầm 1 chương trình SSH deamon, chịu cách nhiệm lắng nghe các yêu cầu kết nối được gởi đến và khởi tạo môi trường shell thích hợp cho từng người dùng. Theo mặc định, SSH server sẽ chạy trên port 22, tuy nhiên, người dùng có thể thay đổi giá trị này. Đối với client, người dùng cần có một chương trình gọi là SSH Client (có thể dùng putty, mobaxterm, terminal trên Linux desktop…) và một đường truyền Internet, đường truyền này không nhất thiết phải an toàn, việc bảo mật cho kết nối sẽ do chính giao thức SSH đảm nhiệm. Ngoài khả năng kết nối và thực thi lệnh từ xa, SSH hỗ trợ thêm một số tính năng khác như tunneling, forward TCP port, hỗ trợ kết nối X11 (giao diện đồ họa thông qua ssh), truyền gởi file (SFTP)… 

## Chứng thực

Giao thức SSH hỗ trợ 2 loại hình chứng thực cho người dùng, đó là: 

  * Chứng thực người dùng bằng **_mật khẩu_**. Ưu điểm của phương pháp này là dễ dàng sử dụng, tiện lợi và nhanh chóng. Tuy nhiên, khuyết điểm lớn của phương pháp này chính là độ bảo mật thấp, dễ dàng bị các chương trình dò tìm mật khẩu brute-force.
  * Cách thứ hai chính là dùng **_cặp khóa bất đối xứng_** để chứng thực. Người dùng sẽ khởi tạo một cặp khóa **_public key – private key_** với độ bảo mật cao. Public key sẽ được người dùng up lên bất kỳ máy chủ cần đăng nhập, còn private key sẽ được người dùng giữ lại (không được làm lộ ra ngoài). Cách làm này đảm bảo độ bảo mật cần thiết cho việc quản trị hệ thống từ xa vì chỉ có người có private key phù hợp với public key được lưu trên server mới có thể truy cặp được.

Khi nhận được thông điệp mã hóa, trình client của người dùng sẽ dùng private key để giải mã và tạo một thông điệp mới gồm thông điệp được server gởi gới + session ID đã được 2 bên thỏa thuận từ đầu. Client sau đó sẽ tạo một chuỗi băm MD5 từ chuỗi mới này và gởi lại cho Server. Server vốn đã biết 2 giá trị này nên sẽ kiểm tra được rằng client có thực sự sở hữu private key hay không. 

## Giới thiệu về các thuật toán tạo khóa

Để chứng thực bằng SSH key, người dùng cần phải lưu trữ cặp khóa này tại máy tính của mình. Còn trên server, người dùng cần phải để public ket của mình tại thư mục home: **_~/.ssh/authorized_keys_**. Hiện có nhiều thuật toán để khởi tạo cặp khóa SSH key này như RSA, DSA, ECDSA, trong đó RSA là thuật toán thường hay được dùng nhất với độ dài khóa từ 2048 – 4096. Sơ lược về các thuật toán này: 

  * **RSA** : đây là thuật toán khóa bất đối xứng cổ điển và được dùng phổ biến nhất hiện nay. Keysize giao động từ 1024 – 4096 bit (nên chọn keysize từ 2048 trở đi vì lý do an toàn). Do thuật toán này đã có từ lâu, việc thuật toán này bị bẻ khóa trong tương lai gần là không thể tránh khỏi, nên chọn 1 thuật toán thay thế khác nếu có thể. Hiện nay, toàn bộ các SSH Client đều hỗ trợ thuật toán này.
  * **DSA** : một thuật toán mã hóa bất đối xứng lâu đời của chính phủ mỹ với keysize thường dùng là 1024 bit. Không nên sử dụng thuật toán này nếu có thể.
  * **ECDSA** : thuật toán chữ ký điện tử mới được tiêu chuẩn hóa bởi chính phủ Mỹ, thuật toán được dựa trên cách tính đường cong elip, khác với cách tính số nguyên tố của các thuật toán cũ. Đây là một thuật toán tốt, bảo mật và nên được dùng khi tạo cặp khóa bất đối xứng. Thuật toán hỗ trợ 3 loại key size là: 256, 384 và 521 bit. Hiện đa phần các trình SSH Client đã hỗ trợ thuật toán này.
  * **ED25519** : đây là thuật toán mới được thêm vào OpenSSH. Hiện vẫn chưa có nhiều SSH Client hỗ trợ thuật toán này nên sẽ gặp nhiều khó khăn khi sử dụng trong thực tế.

Khi người dùng cần truy cập bằng cặp khóa bất đối xứng, trình client sẽ báo với server và nói với server rằng cần dùng public key nào phù hợp. Server sau đó sẽ đọc file **_~/.ssh/authorized_keys_** của người dùng, khởi tạo một chuỗi với giá trị ngẫu nhiên và mã hóa chuỗi này bằng chính public key của người dùng. Thông điệp này chỉ có thể được mã hóa bởi private key tương ứng nằm tại máy của người dùng. 

# Cấu hình

Trong phần này, ta sẽ cấu hình SSH Server chỉ chấp nhận chứng thực bằng key chứ không nhận password nữa, làm như vậy sẽ đảm bảo an toàn khi truy cập hệ thống. Xem cách cấu hình mẫu bên dưới Ta khởi tạo cặp key SSH tại client (máy mà ta đang sử dụng như sau), cặp khóa này sẽ dùng thuật toán RSA với keysize là 4096. 
    
    
    ssh-keygen -b 4096 -t rsa

Kiểm tra lại cặp key vừa tạo: 
    
    
    ssh-keygen -l

Tiếp theo, ta sẽ upload cặp key này lên thư mục HOME của người dùng mà ta muốn đăng nhập trên Remote Server 
    
    
    ssh-copy-id user@remote_host

hoặc dùng lệnh 
    
    
    cat ~/.ssh/id_rsa.pub | ssh username@remote_host "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"

Thay đổi file cấu hình SSH Deamon như sau: Không cho phép đăng nhập với quyền root và đăng nhập bằng mật khẩu 
    
    
    PermitRootLogin no
    PasswordAuthentication no
    PermitEmptyPasswords no

Dùng cặp key RSA để chứng thực người dùng 
    
    
    RSAAuthentication yes
    PubkeyAuthentication yes
    AuthorizedKeysFile .ssh/authorized_keys

Tham khảo thêm file cấu hình mẫu SSH server tại đây: [GitHub](https://github.com/cloudcraftteam/System-Engineer-Cheat-Sheets/tree/master/SSH)
