---
title: "Hướng dẫn sử dụng Selenium"
date: 2018-10-12 11:00:31
categories: [Automation, Programming]
---

# Giới thiệu về Selenium

Selenium là một bộ công cụ dùng để chạy tự động các thao tác trên trình duyệt. Tool này chủ yếu được các bạn tester dùng để kiểm thử phần mềm tự động trên nền web. Về lý thuyết là vậy....còn mình muốn xài tool làm gì thì làm chứ :P, như dùng để auto click, giật cô hồn trên Tiki chẳng hạn. Hoặc dùng để tự động các thao tác quản trị hệ thống trên nền web. Về mặt bản chất, Selenium có tiền tố là Selen. Selen là một nguyên tố hóa học với số nguyên tử 34 và ký hiệu hóa học Se. Nó là một phi kim, về mặt hóa học rất giống với lưu huỳnh và telua, và trong tự nhiên rất hiếm thấy ở dạng nguyên tố. Đối với sinh vật, nó là độc hại khi ở liều lượng lớn, nhưng khi ở liều lượng dấu vết thì nó là cần thiết cho chức năng của tế bào. Vậy nên, Selenium thừa hưởng các đặc tính... đùa đấy nói chung là bạn có thể dùng trình duyệt làm được gì thì bạn cũng có thể dùng Selenium để tự động hóa thao tác đó cho bạn. Hiện tại, Selenium hỗ trợ các trình duyệt phổ biến nhất hiện nay như: Chrome, Firefox, Safari, Edge... và hỗ trợ các ngôn ngữ như: Python, Java, C#, Ruby, Javascript... Trong bài viết này, mình sẽ dùng python để demo một số tính năng cơ bản. 

# Hướng dẫn cài đặt

Ở đây, mình sẽ dùng Python 3.6 và Selenium 3 để demo. Cài gói Selenium bằng pip 
    
    
    pip install -U selenium

Chú ý: Có thể dùng virtualenv để tạo môi trường test riêng biệt. Coi thêm: [Hướng dẫn cài đặt Python và virtualenv](https://cloudcraft.info/huong-dan-cai-dat-python-va-virtualenv/) Drivers

Để Selenium có thể tương tác được với browser, cần phải download driver thích hợp cho từng loại browser này. Cụ thể như sau: 

  * Với Firefox bản 35 trở lên, ta cần tải MozillaGeckoDriver (Selenium 3)
  * Với Chrome, ta cần tải ChromeDriver.
  * IE thì tải InternetExplorerDriver Server.
  * Với Opera Opera thì tải OperaDriver.
  * Safari thì tải SafariDriver.

Link tải ở đây:  **Chrome** : | <https://sites.google.com/a/chromium.org/chromedriver/downloads>  
---|---  
**Edge** : | <https://developer.microsoft.com/en-us/microsoft-edge/tools/webdriver/>  
**Firefox** : | <https://github.com/mozilla/geckodriver/releases>  
**Safari** : | <https://webkit.org/blog/6900/webdriver-support-in-safari-10/>  
  
# Một số thao tác cơ bản với Selenium

## Ví dụ 1

  * Trỏ đường dẫn tới chromedriver vừa tải về.
  * Option headless dùng để chạy driver ở chế độ background/headless.
  * Mở trình duyệt Chrome.
  * Truy cập vào trang CloudCraft.
  * Đóng trình duyệt.


    
    
    # -*- coding: utf-8 -*-
    from selenium import webdriver
    from selenium.webdriver.chrome.options import Options  
    
    chrome_options = Options()
    #chrome_options.add_argument("--headless")
    
    chromedriver = 'D:\\ChromeDriver\\chromedriver.exe'
    driver = webdriver.Chrome(chrome_options=chrome_options,executable_path=chromedriver)
    driver.get("https://cloudcraft.info")
    
    #driver.close()

 

![](https://cloudcraft.info/wp-content/uploads/2018/07/huong-dan-su-dung-selenium-1-1.jpg)

_Ta đã dùng được Selenium để điều khiển trình duyệt Chrome trên máy mình_

## Ví dụ 2

  * Mở Chrome
  * Vô google
  * Kiếm cụm từ CloudCraft
  * Đóng trình duyệt


    
    
    # -*- coding: utf-8 -*-
    from selenium import webdriver
    from selenium.webdriver.common.keys import Keys
    from selenium.webdriver.chrome.options import Options  
    
    chrome_options = Options()
    #chrome_options.add_argument("--headless")
    
    chromedriver = 'D:\\ChromeDriver\\chromedriver.exe'
    driver = webdriver.Chrome(chrome_options=chrome_options,executable_path=chromedriver)
    driver.get("https://google.com.vn")
    
    elem = browser.find_element_by_name('p')  # Find the search box
    elem.send_keys('CloudCraft.info' + Keys.RETURN)
    #driver.close()

  ![](https://cloudcraft.info/wp-content/uploads/2018/07/huong-dan-su-dung-selenium-2.jpg)

## Ví dụ 3

Điền form trên web tự động, giả sử ta có 1 form gồm 1 text box nhập họ tên và 1 nút submit, ta có thể dùng cách sau để tự động điền họ tên và bấm nút submit họ tên của mình. 

  * Mở Chrome
  * Vô google
  * Kiếm element có id là HoTen
  * Điền chuỗi 'Duy Nguyen' vào text box
  * Kiếm nút submit trên form => bấm submit.


    
    
    # -*- coding: utf-8 -*-
    from selenium import webdriver
    from selenium.webdriver.chrome.options import Options  
    
    chrome_options = Options()
    #chrome_options.add_argument("--headless")
    
    chromedriver = 'D:\\ChromeDriver\\chromedriver.exe'
    driver = webdriver.Chrome(chrome_options=chrome_options,executable_path=chromedriver)
    driver.get("http://demo.abc")
    
    hoTen = 'Duy Nguyen'
    fieldHoTen = driver.find_element_by_id("HoTen")
    try:
        fieldHoTen.send_keys(hoTen)
    except TypeError:
        print('Error')
        driver.close()
    
    driver.find_element_by_xpath("//input[@type='submit']").click()
    #driver.close()

# FAQ

Chạy nhiều thread được không? Miễn cưỡng chạy thì cũng được, nhưng không ổn định. Miễn cưỡng thì ko có hạnh phúc, nên tốt nhất là nên khởi tạo nhiều WebDriver instance, mỗi instance chạy 1 thread thì ổn nhất (khá là tốn RAM khi chạy với Chrome ấy nhé, hí hí). 

# Tham khảo

https://www.seleniumhq.org/
