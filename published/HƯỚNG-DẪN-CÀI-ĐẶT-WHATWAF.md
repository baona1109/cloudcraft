---
title: "HƯỚNG DẪN CÀI ĐẶT WHATWAF"
date: 2021-09-04 09:31:53
categories: [Linux]
---

# WhatWaf là gì?

WhatWaf là một công cụ phát hiện hệ thống bảo mật trên ứng dụng web nâng cao nhằm mục đích trả lời cho câu hỏi “Máy chủ web có sử dụng WAF không và nó là loại nào?” . WhatWaf hoạt động bằng cách phát hiện firewall trên một ứng dụng web và cố gắng phát hiện một (hoặc hai) cách bypass firewall đó, trên mục tiêu được chỉ định. Để biết thêm chi tiết về **tính năng** hoặc **danh sách các firewall** mà WhatWaf đang hỗ trợ, các bạn có thể truy cập link gốc của tác giả Ekultek. <https://github.com/Ekultek/WhatWaf>

# Hướng dẫn cài đặt WhatWaf trên Kali Linux

  * Để cài đặt WhatWaf, ta sử dụng các lệnh sau:

`# apt install python3-pip` `# git clone https://github.com/ekultek/whatwaf` `# cd whatwaf` `# pip3 install -r requirements.txt`

  * Để scan thử hệ thống bảo mật của một website, ta sử dụng option -u hoặc --url.

`# python3 ./whatwaf -u https://apple.com` Ngoài ra, ta có thể scan ẩn danh bằng cách đi qua tor hoặc proxy. `# python3 ./whatwaf -u https://apple.com --tor`  
