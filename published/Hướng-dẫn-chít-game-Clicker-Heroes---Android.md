---
title: "Hướng dẫn chít game Clicker Heroes - Android"
date: 2019-04-29 08:56:55
categories: [Linux, Security]
---

Miss me?

Hôm nay tôi sẽ hướng dẫn các bạn cheat một 'game offline' đơn giản trên android tên là "Clicker Heroes". Mục tiêu của post này nhằm hướng dẫn một số mindset cơ bản về cheat hoặc đơn giản là nâng cao khả năng sử dụng bash shell. Xin lỗi nếu bài post gây cho bạn khó chịu vì sử dụng xen lẫn Anh/Việt.

**— Chuẩn bị —**

1\. Android phone: Cài đặt "Clicker Heroes" thông qua playstore 2\. Linux OS: Tôi dùng CentOS để decode.

Trong con CentOS, cài đặt các tool sau:

  *     * xxd: hex content
    * jq: json format
    * vimdiff: color diff
    * python2.7 + git: set up 'binwalk'
    * base64: decode 
    * openssl: decompress

**— Chơi game —**

Cùng nhìn giao diện game một chút nào:

![](https://cloudcraft.info/wp-content/uploads/2019/04/cheat-clicker-heroes_1.png) | ![](https://cloudcraft.info/wp-content/uploads/2019/04/cheat-clicker-heroes_2.png)  
---|---  
 

Game này nhằm giải trí đơn giản, mục tiêu chính là phá cái màn hình cảm ứng của bạn bằng cách "tap" liên tục để thông nát mông thằng creep ở giữa màn hình, đại loại thế. Vậy tại sao lại sử dụng game này để cheat ??

-> Nào cùng nhìn vào phần Settings của game (hình bên phải phía trên)... à há, có "export" và "import" để lưu nội dung (hay snapshoot) của game nếu bạn muốn chuyển sang điện thoại mới. Vấn đề nằm ở chỗ này.

![](https://cloudcraft.info/wp-content/uploads/2019/04/cheat-clicker-heroes_3.png)

\- Cùng nhìn qua một phần nội dung của phần "export" trên:

"7a990d405d2c6fb93aa8fbb0ec1a3b23eNrtm0tzGzcSgP8Lz6kU5yWKvknUy1nLYkg62crFBc5AJFYgMAFmKDMu/fftxmMwM5Jopmonp/XBRQFooAF0f2gQze+jJyk2H4vRh9Hop5GWNdfLkooK/h5DQanYjvAlFruSXFFSMSlWbEd1RXbl6EOUZWfx"

Tập hợp chỉ gồm các ký tự [0-9], [a-z], [A-Z] và [**+/**]. Không lạ gì với **Base64** nữa phải không nào ?

\- Cùng decode xem thử ta có gì, phần nội dung export tôi gọi là "clickerheroes.export"
    
    
    # base64 -d clickerheroes.export > clickerheroes.decoded
    # file clickerheroes.decoded
    clickerheroes.decoded: data

\- Có vẻ sau khi decode base64, phần nội dung thu được ta không nhận dạng được gì. Lúc này, cần sự trợ giúp của **binwalk** :
    
    
    # git clone https://github.com/ReFirmLabs/binwalk
    # cd binwalk ; python setup.py install; cd -
    # binwalk clickerheroes.decoded
    DECIMAL       HEXADECIMAL     DESCRIPTION
    --------------------------------------------------------------------------------
    24            0x18            Zlib compressed data, best compression
    

\- Có vẻ nhận dạng được đang nén bởi Zlib, vậy thì thử giải nén xem có gì ?
    
    
    # openssl zlib -d < clickerheroes.decoded > clickerheroes.zlib
    140431594055568:error:29065064:lib(41):BIO_ZLIB_READ:zlib inflate error:c_zlib.c:548:zlib error:data error

\- WTF không giải nén được. Tới đây ta biết nó được nén theo Zlib nhờ binwalk, nhưng không thể giải nén được. Nào cùng nhìn vào cái Index DECIMAL/HEXADECIMAL trong output của binwalk ở trên, Index số 24 (hay vị trí thứ 25) mới nhận dạng được Zlib. Vậy dùng hexdump hoặc xxd để xem, "byte index 24" có gì.
    
    
    # xxd clickerheroes.decoded | head -4
    0000000: edaf 7dd1 de34 e5dd 9ce9 f6fd dda6 bc7d  ..}..4.........}
    0000010: b6f4 79cd 5add bdb7 78da ed9b 4b73 1b37  ..y.Z...x...Ks.7
    0000020: 1280 ff0b cfa9 14e7 258a be49 d4cb 59cb  ........%..I..Y.
    0000030: 6248 3ad9 cac5 05ce 4024 5620 3001 6628  bH:.....@$V 0.f(

Tại byte index 24: **78** -> Nếu bạn tìm google, thì sẽ thấy đây là Zlib header.

![](https://cloudcraft.info/wp-content/uploads/2019/04/cheat-clicker-heroes_4.png)

\- Lúc này, chúng ta chỉ nên lấy data từ byte index 24 trở đi để giải nén (lưu ý, index 24 tương đương với byte số 25)
    
    
    # tail -c +25 clickerheroes.decoded > clickerheroes.decoded2
    # openssl zlib -d < clickerheroes.decoded2 > clickerheroes.zlib

\- Sau giải nén ta sẽ thu được nội dung file như sau:
    
    
    # cat clickerheroes.zlib | jq '.' | head
    {
      "kongId": "",
      "soulsSpent": "0",
      "primalSouls": "0",
      "creationTimestamp": 1556270138755,
      "epicHeroReceivedUpTo": 0,
      "epicHeroSeed": 0,
      "shouldAutoSetHeroDpsDisplay": true,
      "shouldShowHeroDps": false,
      "purchaseRecord": {},

\- Vậy là đã có cấu hình game, giờ thì chỉnh các thông số như rubies hay gold theo mong muốn (tôi đặt gold=100M, rubies=100k).
    
    
    # cat clickerheroes.zlib | jq '.' | grep "rubies\|gold"
        "goldNotificationsEnabled": true,
      "gold": "100036589.86324544117",
      "rubies": 100000,
      "goldQuestsCompleted": 0,
      "goldMultiplier": 1,
      "goldSacrificedInWorldResets": 0,
      "goldFloatersDisabled": false,

\- Rồi đảo nghịch quá trình trên như sau:
    
    
    # head -c 24 clickerheroes.decoded > clickerheroes.compress
    # openssl zlib -e < clickerheroes.zlib >> clickerheroes.compress
    # base64 -w 0 < clickerheroes.compress > clickerheroes.import

\- Lấy nội dung file "clickerheroes.import", import trong game và ola !. Nếu đặt "debug"=true trong json config và import, sẽ có thêm 1 cửa sổ console trong phần setting. Lúc này bạn có thêm các tùy chỉnh như "add gold" ngay trong game :v

![](https://cloudcraft.info/wp-content/uploads/2019/04/cheat-clicker-heroes_5.png) | ![](https://cloudcraft.info/wp-content/uploads/2019/04/cheat-clicker-heroes_6.png)  
---|---  
 

Hướng dẫn này dựa trên liveoverflow mà tôi thấy khá hay và dễ hiểu, nếu quan tâm bạn có thể tìm nhiều hơn bằng gg.
