---
title: "Đóng gói ứng dụng Flask với Docker"
date: 2018-10-05 09:35:30
categories: [Programming, Docker, Flask, Python]
---

Trước đây mình có đọc một bài của 1 anh dev nước ngoài, đại để là nên đẩy code lên môi trường production càng sớm càng tốt, như vậy thì sẽ dễ cấu hình và điều chỉnh app hơn và đặc biệt là không cần phải chuyển đổi 1 project chạy tốt trên localhost => chạy trên server (cái này khá là mất thời gian và cũng chẳng vui vẻ gì).  
  
Vì vậy hôm nay mình sẽ làm 1 bài ngắn, giới thiệu cách đóng gói Flask app + uWSGI + Nginx lên container và chạy được production luôn (ở mức cơ bản thôi nhen). Có update code gì thì cũng có khuôn mẫu để up, ko sợ phải điều chỉnh nhiều khi deploy.

~~Cũng tính làm 1 bài về setup mấy flask + uwsgi + nginx bằng tay, nhưng lười quá nên thôi làm bài container này. Về mặt lý thuyết thì mỗi container 1 process, nhưng lười thì nhét chung vô 1 con :P.~~

**Lưu ý:** Chỉ nên với những những dụng Flask nào scale nhỏ, all-in-one thì mình sẽ đóng gói lại chung 1 container trong docker cho dễ quản lý. Còn với những project lớn thì nên tách ra cho dễ scale lên

  ![](https://cloudcraft.info/wp-content/uploads/2018/10/dong-goi-ung-dung-flask-1.png)

_Workflow cơ bản cho bài này_

Người dùng sẽ kết nối tới Nginx ở port 80, sau đó request sẽ được đẩy tới cho uwsgi (trong cùng 1 container) và cuối cùng là đẩy tới Flask để xử lý.

Cấu trúc của project 
    
    
    .
    ├── app
    │   ├── main.py
    │   ├── requirements.txt
    │   ├── rsync.pass
    │   └── uwsgi.ini
    ├── build_docker.sh
    ├── client.py
    ├── Dockerfile
    └── run_docker.sh

## Hướng dẫn chạy project

Source full ở đây: [GitHub](https://github.com/nduytg/flaskAPI)

  1. Tải project về
  2. Chạy file _**build_docker.sh**_
  3. Chạy file _**run_docker.sh**_



## Cách hoạt động cơ bản

Ở đây, để demo cách hoạt động cơ bản, mình có viết sẵn 1 file main.py đóng vai trò là 1 API server. API này sẽ nhận 1 **HTTP POST Request** và thực hiện một tác vụ tương ứng trên server. Tác vụ này có thể là clean DB, đồng bộ thư mục, xóa file cũ... tùy theo bạn định nghĩa trong biến COMMAND. Server sẽ check POST request nếu có đủ 3 field là time, user và sign => kiểm tra tiếp sign này (tạm gọi là sign_1 do client gởi lên). Với sign_2 do server tự tạo dựa trên time, user và SecretKey (SecretKey này đã gởi riêng cho client trước đó). Nếu **sign_1 == sign_2** thì mới thực hiện chạy lệnh File**main.py** sẽ có dạng như vầy, demo một ứng dụng cơ bản 
    
    
    from flask import Flask
    app = Flask(__name__)
    
    from flask import jsonify, abort, request, make_response
    from hashlib import md5
    import os
    
    SecretKey = 'Qi3mnN9b3UougbpqFvsGruSir0tKPlhc'
    ALLOW_LIST = ['nduytg','cloudcraft']
    COMMAND = 'echo Hello World'
    
    @app.route("/")
    def hello():
        return "Hello World from Flask in a uWSGI Nginx Docker container with \
         Python 3.6 (from the example template)"
    
    #### Task API ####
    @app.route('/api')
    def index():
        abort(403)
    
    # Run a custom task on server when receiving POST request
    @app.route('/api/tasks', methods=['POST'])
    def runTask():
        data = request.form.to_dict()
    
        if len(data) == 0:
            abort(403)
        if data['user'] not in ALLOW_LIST:
            abort(403)
    
        # sign = md5(time + user + SecretKey)
        serverSign = md5((data['time'] + data['user'] + SecretKey).encode('utf-8')).hexdigest()
    
        if serverSign == data['sign']:
            # Run script
            #print('Do something')
            result = os.system(COMMAND)
    
            if result == 0:
                return  make_response(jsonify({'Result code': 0, 'Message':'Run task successfully'})) 
            else:
                return  make_response(jsonify({'Result code': -1, 'Message':'Task failed! Try again'})) 
        else:
            return make_response(jsonify({'Error code': -1, 'Error message':'Wrong Paramters'})) 
    
    # Error Handlers
    @app.errorhandler(404)
    def notFound(error):
        return make_response(jsonify({'Error code': 404, 'Error message':'Not found'}))
    
    @app.errorhandler(403)
    def permissionDenied(error):
        return make_response(jsonify({'Error code': 403, 'Error message':'Permission Denied'}))
    
    if __name__ == "__main__":
        # Only for debugging while developing
        app.run(host='0.0.0.0', debug=False, port=5000)
    

Tạo một file **Dockerfile** như sau để build image. Ở đây, mình dựa trên image tiangolo/uwsgi-nginx-flask:python3.6 để chạy
    
    
    FROM tiangolo/uwsgi-nginx-flask:python3.6
    LABEL MAINTAINER="nduytg"
    
    COPY ./app /app
    WORKDIR /app
    
    RUN rm -f /etc/localtime
    RUN cp /usr/share/zoneinfo/Asia/Ho_Chi_Minh /etc/localtime
    
    RUN apt-get update -y
    RUN apt-get install -y python-pip python-dev build-essential
    
    RUN pip install -r requirements.txt
    

Chạy script **build_docker.sh**
    
    
    #!/bin/bash
    
    docker build -t mini-api .

Chạy script **run_docker.sh**
    
    
    #!/bin/bash
    
    container="flaskAPI"
    docker run --name $container --hostname $container -p 80:80 --restart always -d mini-api

Ở đây, ta đã bind trực tiếp port 80 của máy host với port 80 của container, bạn có thể dùng trình duyệt truy cập vào IP của máy host để kiểm tra container đã hoạt động dc hay chưa, ở đây IP của máy ảo mình cài là 192.168.11.130 ![dong-goi-ung-dung-flask-voi-docker-2](https://cloudcraft.info/wp-content/uploads/2018/10/huong-dan-dong-goi-ung-dung-flask-voi-docker-2.png) Ta đã truy cập thành công tới web service trên container. 

## Kiểm tra hoạt động của API

Tiếp theo, ta cần kiểm tra tiếp xem API đã hoạt động ổn định chưa, dùng file client.py, để test thử API. 
    
    
    import requests
    from hashlib import md5
    import time
    import pprint
    
    SecretKey = 'Qi3mnN9b3UougbpqFvsGruSir0tKPlhc'
    domain = 'http:/localhost:80/api/tasks'
    
    user = 'nduytg'
    time = str(int(time.time()))
    sign = md5((time + user + SecretKey).encode('utf-8')).hexdigest()
    
    print(time)
    print(sign)
    
    r = requests.post(domain, data={'user': user, 'taskID': taskID, 'time': time, 'sign': sign})
    print(r.status_code, r.reason)
    print(r.content)

Chạy lệnh**python client.py** để test gởi request tới server. Trên container, ta sử dụng lệnh **docker logs** để kiểm tra các request được gởi tới: 
    
    
    docker logs -f flaskAPI

## Mở rộng API

Trên đây là một ví dụ cơ bản về 1 Flask API chạy trong container, bạn có thể mở rộng thêm bằng nhiều cách như sử dụng thêm database, sử dụng thêm queue để chứa các request được gởi đến. Dưới đây là một cách tổ chức project mẫu, phân chia thành nhiều module (tham khảo thêm tại: https://github.com/tiangolo/uwsgi-nginx-flask-docker) 
    
    
    .
    ├── app
    │   ├── app
    │   │   ├── api
    │   │   │   ├── api.py
    │   │   │   ├── endpoints
    │   │   │   │   ├── __init__.py
    │   │   │   │   └── user.py
    │   │   │   ├── __init__.py
    │   │   │   └── utils.py
    │   │   ├── core
    │   │   │   ├── app_setup.py
    │   │   │   ├── database.py
    │   │   │   └── __init__.py
    │   │   ├── __init__.py
    │   │   ├── main.py
    │   │   └── models
    │   │       ├── __init__.py
    │   │       └── user.py
    │   └── uwsgi.ini
    └── Dockerfile

## Đọc thêm

  * [Hướng dẫn cài đặt Python và virtualenv](https://cloudcraft.info/huong-dan-cai-dat-python-va-virtualenv/)
  * Hướng dẫn xây dựng REST API cơ bản với Flask (đang viết)
  * Hướng dẫn xây dựng API với Djano (đang viết)
  * Hướng dẫn deploy ứng dụng web Django (đang viết)



## Tham khảo

<https://github.com/tiangolo/uwsgi-nginx-flask-docker>
