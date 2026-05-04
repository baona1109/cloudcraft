---
title: "Lập trình Bash Shell cơ bản - Phần 1"
date: 2018-02-02 14:45:19
categories: [Linux, Programming, Bash Shell]
---

Bài viết này mục đích sưu tầm những kiến thức cơ bản cần biết để có thể viết được một vài script đơn giản. Hi vọng bài này sẽ giúp ích cho những ai mới bắt đầu học viết shellscript trên linux. Do trình mình cũng hơi chuối nên có thể sẽ sai sót, mong các bạn thông cảm nhé ^^ 

## **Biến đặc biệt**

**Biến đặc biệt** | **Ý nghĩa**  
---|---  
**$0** | Chứa tên file script hiện tại  
**$n ($1, $2,..$n)** | Với n có thể là 1, 2,... tương ứng với các tham số đưa vào khi gọi script VD: ./test.sh so01 so02 so03 Như VD trên thì có 3 tham số được đưa vào là so01, so02, so03 tương ứng với $1, $2, $3.  
**$#** | Tổng số tham số đã cung cấp cho script. Dựa theo VD trên thì kết quả khi dùng biến này sẽ ra 3  
**$*** | Chứa tất cả tham số được đưa vào script. Nếu script nhận 3 tham số thì giá trị sẽ là $1 $2 $3 khi sử dụng  
**$@** | Chứa tất cả tham số nhưng phân biệt thành những tham số riêng lẽ không như $* được đưa vào script. Nếu script nhận 3 tham số thì giá trị sẽ là $1 $2 $3 khi sử dụng  
**$?** | Trạng thái thoát ra của lệnh trước được chạy (thường la 0 đại diện cho lệnh trước chạy thành công, khác 0 là failed) Max range [0 – 255]  
**$$** | Số tiến trình của shell hiện tại. Đối với Shell script đây là số ProcessID mà chúng đang chạy  
**$!** | Số tiến trình của lệnh background trước  
**Sự khác nhau giữa $* và $@** Về cơ bản thì $* và $@ giống nhau khi sử dụng với biến đợn không đặt trong dấu “”. Khi đặt vào trong dấu nháy kép “” thì: 

  * $*: Các tham số đưa vào sẽ được nối thành một chuỗi và được phân cách nhau bởi dấu cách.
  * $@: Các tham số đưa vào sẽ được phân biệt một cách riêng lẽ từng tham số một.

**VD:**
    
    
    #!/bin/bash
    
    echo -e "\nUsing \"\$*\":"
    
    for str in "$*"
    do
       echo $str
    done
    
    echo -e "\nUsing \"\$@\":"
    
    for str in "$@"
    do
       echo $str
    done

Kết quả khi chạy script trên sẽ là: **./test.sh my name is Dang**
    
    
    Using "$*":
    my name is Dang

Khi sử dụng “$*” thì các tham số được xem là một chuỗi phân biệt bởi khoảng trắng 
    
    
    Using "$@":
    my
    name
    is
    Dang

Khi sử dụng “$@” thì các tham số được xem như các biến riêng lẽ được đặt trong một mảng 

## **Mảng**

**Cú pháp khai báo:**
    
    
    <tên mảng>=(giatri1 giatri2 ... giatrin)

**VD:** list=(coffee water beer) **Lấy số lượng phần tử của mảng:**
    
    
    ${#<tên mảng>[@]}

**VD:** elements = ${#list[@]} **Truy xuất phần tử trong mảng:**
    
    
    ${<tên mảng>[<giá trị>]

**VD:** ${list[0]} sẽ cho giá trị đầu tiên trong mảng là coffee **Tìm và thay thế phần tử trong mảng:**
    
    
    ${<tên mảng>[@]/pt_find/pt_replace}

**VD:** ${list[@]/coffee/milk} lệnh này sẽ thay thế phần tử có giá trị coffee thành milk 

## **Các toán tử cơ bản**

Về cơ bản thì shell linux sử dụng các toán tử cơ bản như các ngôn ngữ lập trình khác như C/C++, Java,... Tuy nhiên với toán tử **nhân** khi sử dụng nên dùng **\\*** để phân biệt với lệnh bất kì ***** của linux Để thực hiện phép tính cộng, trừ, nhân, chia có 3 cú pháp sau: 

  * Sử dụng cú pháp: `**expr op1 <phép tính> op2` **(để trong 2 dấu `` không phải nháy đơn đây là để thực thi lệnh shell expr)

**VD:**
    
    
    echo `expr 2 + 3` sẽ hiển thị kết quả là 5
    echo `expr 2 \* 3’ kết quả là 2 * 3 = 6

  * Sử dụng cú pháp **let “phép tính”**

**VD:**
    
    
    let “a=$a+3”
    let ”c=$a*$b”

Ngoài ra **let** có thể sử dụng các toán tử +=, -=, =+, =- khá tương tự như trong C/C++ và java 

  * Sử dụng cú pháp: **$((...))**

**VD:**
    
    
    Z=$(($a + $b))
    Z=$(($a * $b))

Đối với chuỗi thì khi sử dụng toán tử **=** có nghĩa là thực hiện so sánh 2 chuỗi với nhau chứ không phải toán tử gán dành cho số 

## **Một số toán tử dành cho chuỗi**

Giả sử có biến a giữ giá trị “abc” và b giữ giá trị “def”  **Toán tử** | **Miêu tả** | **Ví dụ**  
---|---|---  
**=** | Kiểm tra giá trị của 2 chuỗi có bằng nhau hay không. Nếu bằng thì trả về giá trị đúng | $a = $b sẽ cho ra kết quả là sai  
**!=** | Kiểm tra sự khác nhau giữa 2 chuỗi. Nếu khác nhau thì trả về giá trị đúng | $a != $b sẽ ra kết quả là đúng  
  
## **Phép toán logic**

**Toán tử** | **Miêu tả** | **Ví dụ**  
---|---|---  
**! <biểu thức>** | Phép NOT | If [ ! “$a” == “$b” ]  
**< biểu thức 1> -a <biểu thức 2>** | Phép AND | if [ “$a” == “$b” -a “$c” == “$d” ]  
**< biểu thức 1> -o <biểu thức 2>** | Phép OR | if [ “$a” == “$b” -o “$c” == “$d” ]  
Lưu ý: Phải có các khoảng trắng giữa toán tử và biểu thức để tránh bị lỗi syntax 

## **Câu lệnh điều kiện**

Các mẫu câu lệnh: 

  * **Lệnh if ... fi**

Cú pháp: 
    
    
    if [ condition ]
    then
       command
    fi

  * **Lệnh if ... else ... fi**


    
    
    if [ condition ]
    then
       command
    else
       command
    
    fi

  * **Lệnh if ... elif ... else ... fi**


    
    
    if [ condition ]
    then
       command
    elif
    then
       command
    else
       command
    fi

Khi có nhiều lựa chọn về các điều kiện thì ta nên sử dụng cấu trúc case … esac thay vì sử dụng nhiều câu lệnh if lồng nhau. Cấu trúc lệnh **case ... esac** : 
    
    
    case “$luachon” in
       giatrimau_1) Các câu lệnh ;;
       giatrimau_2) Các câu lệnh ;;
       giatrimau_n) Các câu lệnh ;;
       *) Câu lệnh cho TH còn lại ;; (Giống default trong C/C++)
    esac

**VD:**
    
    
    #!/bin/bash
    
    case "$1" in
       1) echo 'Monday' ;;
       2) echo 'Tuesday' ;;
       3) echo 'Wednesday' ;;
       4) echo 'Thursday' ;;
       5) echo 'Friday' ;;
       6) echo 'Saturday' ;;
       7) echo 'Sunday' ;;
       *)
       echo "Don't match anything"
       exit 1
    ;;
    esac
    
    exit 0

**Lưu ý** : Cú pháp đúng có khoảng trắng giữa condition với [] Case...asce mỗi trường hợp có thể lồng nhiều điều kiện chung nhóm tại giá trị mẫu và cách nhau bởi dấu | 

## **Vòng lặp**

Khi ta có một mảng và cần duyệt toàn bộ trên đó để lọc phần tử trong mảng để sử dụng cho mục đích nào đó ta không thể truy xuất từng phần tử trong mảng bằng lệnh if, nếu mảng có n phần tử thì ta phải viết n lệnh if tương ứng. Vì vậy mà shell cung cấp chúng ta vòng lặp như các ngôn ngữ khác để tiện hơn trong quá trình lập trình. Có 4 loại vòng lặp trong shell là: 

  * **Vòng lặp for:** Hoạt động dựa trên danh sách của các mục. Nó lập đi lập lại một tập các câu lệnh cho mỗi mục có trong danh sách. Có thể sử duyệt mảng với điều kiện cho trước (khá giống với trong C/C++)

**VD:** Vòng lặp for cho các danh mục có sẵn 
    
    
    for var in Mon Tue Wed Thurs Fri Sat Sun
    do
       echo $var
    done

Vòng lặp for dùng cho duyệt mảng 
    
    
    for (( i=0; $i < $elements; i++ ))
    do
       echo ${array[$i]}
    done

  * **Vòng lặp while:** Chủ yếu sử dụng vòng lặp này khi ta muốn thực thi một tập lệnh lặp đi lặp lại nhiều lần trong khi điều kiện vẫn còn đúng. (Ở phần điều kiện nếu là true thì vòng lặp while sẽ được thực thi)

**VD:** Vòng lặp while 
    
    
    a=0
    while [ $a –le 5 ]
    do
       echo $a
       a=$(($a + 1))
    done

  * **Vòng lặp until:** Ngược lại với while, vòng lặp này sẽ thực thi tập lệnh của nó khi giá trị điều kiện là sai. Nó sẽ chạy tới khi thỏa mãn điều kiện là đúng.

**VD:**
    
    
    a=0
    until [ ! $a –le 5 ]
    do
       echo $a
       a=$(($a + 1))
    done

  * **Vòng lặp select:** Vòng lặp cung cấp một danh sách menu từ một tập các phần tử được cho có đánh số ở đầu để lựa chọn. (Đây là vòng lặp có sẵn trong ksh được điều chỉnh vào bash. Nó không có sẵn trong bash

**VD:**
    
    
    #!/bin/ksh
    
    select DRINK in tea cofee water juice appe all none
    do
    
       case $DRINK in
          tea|cofee|water|all)
             echo "Go to canteen"
             ;;
          juice|appe)
             echo "Available at home"
             ;;
          none)
             break
             ;;
          *) echo "ERROR: Invalid selection"
             ;;
       esac
    
    done

Kết quả xuất ra sẽ cho 1 danh sách lựa chọn 
    
    
    [root@localhost shellpractice]# ./selectloop.sh
    1) tea
    2) cofee
    3) water
    4) juice
    5) appe
    6) all
    7) none
    #?

## **Trạng thái Exit**

Mặc định trong linux khi một lệnh hoặc một script được thực thi, nó sẽ trả về 2 loại giá trị để xác định xem là lệnh hoặc script đó thực thi có thành công hay không. 

  * Giá trị 0: Tức là lệnh hoặc script đã thực thi thành công.
  * Giá trị trả về khác không: Tức là lệnh hoặc script khi thực thi đã gây lỗi.

Giá trị này gọi là trạng thái Exit 

## **Các lệnh điều hướng**

Đề thực hiện điều hướng lại output ra một file cũng như input lấy đầu vào từ một file ta có thể sử dụng các cú pháp sau:  **Ký hiệu** | **Ý nghĩa**  
---|---  
**>** | Điều hướng output ra một file và ghi đè toàn bộ nội dung một file đã có. Nếu chưa có thì sẽ tạo file mới. VD: ls > listfile  
**> >** | Điều hướng output ra một file đã có và ghi vào cuối file đó. Nếu file chưa có thì sẽ tạo file mới VD: ls >> listfile  
**<** | Lấy dữ liệu cho câu lệnh linux từ một file  
Ngoài ra có một số trường hợp chúng ta không muốn kết quả output xuất ra file hoặc terminal ta có thể loại bỏ output bằng cách điều hướng nó vào tệp /dev/null. Đây là tệp đặc biệt sẽ tự động loại bỏ tất cả input của nó. Có thể loại bỏ đầu ra của một lệnh và đầu ra bị lỗi của lệnh đó bằng việc thêm 2>&1 với 2 là STDERR được điều hướng lại ra 1 là đại diện cho STDOUT. Cú pháp: **command > /dev/null 2>&1**

## **Hàm trong Shell**

**Cú pháp:**
    
    
    function_name() {
       list of comands
    }

**Truyền tham số cho hàm** tương tự như truyền tham số cho file script. Các biến tham số lần lượt là $1, $2, ... $n. Tuy nhiên những biến này là biến local chỉ có giá trị trong hàm và không ảnh hưởng gì tới các biến tham số được đưa vào script. Các biến còn lại thì có giá trị toàn cục, chẳng hạn ta khai báo một biến trong hàm, khi ra khỏi hàm khi truy xuất ta vẫn nhận được giá trị của biến đó khi được gán trong hàm **VD:**
    
    
    #!/bin/bash
    
    Hello() {
       Echo “ Hello world $1 $2”
    }
    
    Hello Hai Dang

Lúc này kết quả output khi chạy script sẽ là **Hello world Hai Dang** **Trả về giá trị từ một hàm** tương tự như thực hiện **return** giá trị trong các ngôn ngữ lập trình khác. Sử dụng **$?** Để lấy giá trị trả về (chỉ nên sử dụng return khi trả mã lỗi). Ngoài ra có thể sử dụng echo để lấy giá trị trả về, khi sử dụng echo thì chỉ nên echo 1 lần trong hàm với giá trị cần lấy, có nhiều echo thì kết quả từ lệnh echo đầu tiên trong hàm sẽ được gán cho biến bên ngoài, cũng có thể sử dụng biến global để lấy giá trị) **Lưu ý:** Khuyến khích có chữ function trước tên hàm để dễ phân biệt function tenham() 

## **Lệnh read – đọc giá trị nhập từ bàn phím, file,…**

Dùng để lấy dữ liệu nhập từ bàn phím hoặc từ một file và lưu vào biến. **Cú pháp:**
    
    
    read var1 var2 var3... varN

Nếu sử dụng read nhưng không kèm tham số thì giá trị sẽ được lưu vào biến **$REPLY** Khi có nhiều biến thì mỗi biến sẽ được gán một phần giá trị trong dòng mà read nhận được. Nó được phân cách bởi giá trị của biến **$IFS** , theo mặc định thì biến **$IFS** chứa ký tự khoảng trắng. Khi đó mỗi biến sẽ giữ một chuỗi được cách biệt bởi kí tự của **$IFS** chứ không phải là một dòng. Biến cuối cùng sẽ giữ các giá trị còn lại của dòng đó. **VD:** Minh họa cho ví dụ sử dụng nhiều biến để nhận giá trị nhập vào từ lệnh read 
    
    
    [dangtgh@MP_Internal_LAP_21 script]$ read var1 var2 var3
    day la vi du minh hoa cho read
    [dangtgh@MP_Internal_LAP_21 script]$ echo -e "var1: $var1\nvar2: $var2\nvar3: $var3"
    var1: day
    var2: la
    var3: vi du minh hoa cho read

Như ví dụ trên thì giá trị của biến $var1 và $var2 được tách ra từ cả dòng nhập vào và được phân biệt bởi khoảng trắng (giá trị mặc định **$IFS – Internal File Separator**). Nếu ta muốn chỉnh lại cách phân biệt thì ta chỉ cần sửa lại giá trị cho biến**$IFS**. 
    
    
    [dangtgh@MP_Internal_LAP_21 script]$ grep "dangtgh" /etc/passwd
    
    dangtgh:x:502:502::/home/dangtgh:/bin/bash
    
    [dangtgh@MP_Internal_LAP_21 script]$ IFS=:
    
    [dangtgh@MP_Internal_LAP_21 script]$ read var1 var2 var3
    
    dangtgh:x:502:502::/home/dangtgh:/bin/bash
    
    [dangtgh@MP_Internal_LAP_21 script]$ echo -e "var1: $var1\nvar2: $var2\nvar3: $var3"
    
    var1: dangtgh
    
    var2: x
    
    var3: 502:502::/home/dangtgh:/bin/bash
    
    [dangtgh@MP_Internal_LAP_21 script]$ unset IFS

Ví dụ trên ta lấy một dòng trong file /etc/passwd và dùng dấu “:” để phân biệt các giá trị tách ra từ dòng. **Lưu ý:** Khi thay đổi biến **$IFS** thì sau khi sử dụng xong ta nên trả lại giá trị mặc định cho IFS bằng lệnh **unset** Đối với file thì ta sử dụng ký tự điều hướng **<** với cú pháp: 
    
    
    read var1 var2 ... varN < ten_file

Ngoài ra khi sử dụng nên thêm cờ -r để ngắt dòng khi nhận kí tự xuống dòng và không thực hiện ghi tiếp khi gặp kí tự **“\”** **Link:** <http://tldp.org/LDP/Bash-Beginners-Guide/html/index.html> <http://vietjack.com/unix/shell_la_gi_trong_unix_linux.jsp> <https://www.cyberciti.biz/faq/bash-for-loop/>
