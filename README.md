# Hệ Thống Đăng Ký Xét Tuyển Học Bạ Đại Học

## Giới thiệu
Đây là một website giúp các trường đại học quản lý quy trình đăng ký xét tuyển học bạ. Hệ thống cho phép quản lý thông tin người dùng, hồ sơ xét tuyển, ngành học và các khối xét tuyển. Người dùng gồm học sinh, giáo viên, và admin với các chức năng phù hợp với vai trò của họ.

## Người đóng góp
- **Nguyễn Gia Hưng**  
  - Lớp: K72E2  
  - MSSV: 725105083  

## Hướng dẫn cài đặt
1. **Tải về Project từ GitHub:**
   - Nhấn nút `Code` trên trang GitHub của dự án và chọn `Download ZIP`.
   - Giải nén file ZIP vào thư mục mong muốn trên máy tính.

2. **Cài đặt XAMPP:**
   - Tải xuống và cài đặt [XAMPP](https://www.apachefriends.org/index.html).
   - Bật Apache và MySQL từ bảng điều khiển XAMPP.

3. **Import Database:**
   - Mở trình duyệt và truy cập `http://localhost/phpmyadmin`.
   - Tạo một database mới (tên: `university_admission_portal`).
   - Nhấn `Import`, chọn file `.sql` của database từ thư mục project và nhấn `Go`.

## Tài khoản có sẵn

| Loại tài khoản | Username   | Password |
|----------------|------------|----------|
| **Admin**      | admin1     | admin1   |
|                | admin2     | admin2   |
|                | admin3     | admin3   |
| **Giáo viên**  | gv1        | gv1      |
|                | gv2        | gv2      |
|                | gv3        | gv3      |
| **Học sinh**   | hs1        | hs1      |
|                | hs2        | hs2      |
|                | hs3        | hs3      |
|                | hs4        | hs4      |
|                | hs5        | hs5      |
|                | hs6        | hs6      |

## Mô tả các trang

## Trang Đăng ký

### 1) Chức năng:
- **Đăng ký thông tin người dùng**:
  - Cho phép người dùng nhập: 
    - Tên đăng nhập
    - Tên người dùng
    - Email
    - Mật khẩu
    - Xác nhận mật khẩu
    - Vai trò (người dùng chọn, nếu cần thiết có thể bỏ mục này vì admin có thể thay đổi role cho người dùng sau khi đăng ký) 
- **Hiển thị thông báo**:
  - Thông báo lỗi nếu có vấn đề xảy ra.
  - Thông báo thành công khi đăng ký hoàn tất.
- **Ẩn/Hiện mật khẩu**:
  - Cung cấp nút để ẩn/hiện nội dung của trường mật khẩu.
- **Điều hướng**:
  - Nút chuyển hướng sang trang đăng nhập.
  - Tự động điều hướng sang trang đăng nhập sau khi đăng ký thành công.
- **Mã hóa mật khẩu**:
  - Mật khẩu được mã hóa trước khi lưu trữ.

### 2) Xử lý:
- **Xử lý thông tin đầu vào**:
  - Kiểm tra người dùng đã nhập đầy đủ các trường:
    - Tên người dùng
    - Tên đăng nhập
    - Email
    - Mật khẩu
    - Xác nhận mật khẩu
    - Vai trò
- **Tên đăng nhập đã tồn tại**:
  - Kiểm tra và thông báo nếu tên đăng nhập đã được sử dụng.
- **Email không hợp lệ**:
  - Xác nhận email đúng định dạng trước khi xử lý.
- **Mật khẩu không khớp**:
  - Kiểm tra và thông báo nếu mật khẩu và xác nhận mật khẩu không trùng khớp.
---

## Trang Đăng nhập

### 1) Chức năng:
- **Đăng nhập người dùng**:
  - Cho phép người dùng nhập:
    - Tên đăng nhập
    - Mật khẩu
- **Hiển thị thông báo**:
  - Thông báo lỗi nếu đăng nhập không thành công.
  - Thông báo thành công khi đăng nhập chính xác.
- **Ẩn/Hiện mật khẩu**:
  - Cung cấp tùy chọn để ẩn/hiện nội dung trường mật khẩu.
- **Điều hướng**:
  - Nút chuyển hướng sang trang đăng ký.
  - Tự động điều hướng sang trang chủ sau khi đăng nhập thành công.

### 2) Xử lý:
- **Xử lý thông tin đầu vào**:
  - Kiểm tra nếu người dùng chưa nhập đủ thông tin (tên đăng nhập hoặc mật khẩu).
- **Tên đăng nhập/Mật khẩu không đúng**:
  - Thông báo nếu tên đăng nhập hoặc mật khẩu không chính xác.

---

## Trang Chủ

### Xử lý phiên:
- **Kiểm tra phiên đăng nhập**:
  - Nếu người dùng chưa đăng nhập, tự động chuyển hướng về trang Đăng nhập.

### 1) Admin:
#### Chức năng:
1. **Hiển thị thông tin ngành**:
   - Các thông tin bao gồm:
     - Tên ngành
     - Khối xét tuyển
     - Trạng thái: Đã quá hạn, Đang mở, Chưa mở
     - Ngày bắt đầu mở đơn
     - Ngày kết thúc nộp đơn
2. **Các nút chức năng**:
   - **Nộp hồ sơ**:
     - Chuyển hướng sang trang Thống kê hồ sơ, hiển thị mặc định ngành tương ứng.
   - **Ẩn/Hiện ngành**:
     - Thay đổi trạng thái hiển thị ngành đối với học sinh.
   - **Xóa**:
     - Xóa ngành khỏi hệ thống.
3. **Tìm kiếm ngành**:
   - Tìm kiếm theo tên ngành.
4. **Sắp xếp**:
   - Theo tên ngành.
   - Theo thời gian còn lại.
5. **Lọc**:
   - Theo trạng thái.
   - Theo khối xét tuyển.
   - Theo trạng thái hiển thị (Ẩn/Hiện).
6. **Chia trang**:
   - Giới hạn số kết quả hiển thị trên mỗi trang.
7. **Hiển thị tổng số kết quả**:
   - Hiển thị tổng số kết quả sau khi áp dụng bộ lọc và sắp xếp.
   - Nếu không có kết quả nào, hiển thị: "Không tìm thấy kết quả nào."

### 2) Giáo viên:
#### Chức năng:
1. **Hiển thị thông tin ngành**:
   - Hiển thị các ngành đã được admin giao.
   - Các thông tin bao gồm:
     - Tên ngành
     - Khối xét tuyển
     - Trạng thái: Đã quá hạn, Đang mở, Chưa mở
     - Ngày bắt đầu mở đơn
     - Ngày kết thúc nộp đơn
2. **Các nút chức năng**:
   - **Nộp hồ sơ**:
     - Chuyển hướng sang trang Thống kê hồ sơ, hiển thị mặc định ngành tương ứng.
3. **Tìm kiếm ngành**:
   - Tìm kiếm theo tên ngành.
4. **Sắp xếp**:
   - Theo tên ngành.
   - Theo thời gian còn lại.
5. **Lọc**:
   - Theo trạng thái.
   - Theo khối xét tuyển.
6. **Chia trang**:
   - Giới hạn số kết quả hiển thị trên mỗi trang.
7. **Hiển thị tổng số kết quả**:
   - Hiển thị tổng số kết quả sau khi áp dụng bộ lọc và sắp xếp.
   - Nếu không có kết quả nào, hiển thị: "Không tìm thấy kết quả nào."

### 3) Học sinh:
#### Chức năng:
1. **Hiển thị thông tin ngành**:
   - Hiển thị các ngành đã được set trạng thái **Hiện**.
   - Các thông tin bao gồm:
     - Tên ngành
     - Khối xét tuyển
     - Trạng thái: Đã quá hạn, Đang mở, Chưa mở
     - Ngày bắt đầu mở đơn
     - Ngày kết thúc nộp đơn
2. **Các nút chức năng**:
   - **Nộp hồ sơ**:
     - Nếu chưa từng nộp hồ sơ: nút màu xanh, khi click chuyển hướng sang trang nộp hồ sơ chi tiết.
     - Nếu đã nộp hồ sơ: nút bị disable.
     - Nếu ngành đã quá hạn nộp hoặc chưa tới thời gian cho phép: nút bị disable.
   - **Xem hồ sơ**:
     - Chỉ hiển thị nếu đã nộp hồ sơ.
     - Khi click, chuyển hướng sang trang xem hồ sơ chi tiết của ngành được chọn.
3. **Tìm kiếm ngành**:
   - Tìm kiếm theo tên ngành.
4. **Sắp xếp**:
   - Theo tên ngành.
   - Theo thời gian còn lại.
5. **Lọc**:
   - Theo trạng thái.
   - Theo khối xét tuyển.
   - Theo tình trạng nộp hồ sơ: Đã nộp, Chưa nộp.
6. **Chia trang**:
   - Giới hạn số kết quả hiển thị trên mỗi trang.
7. **Hiển thị tổng số kết quả**:
   - Hiển thị tổng số kết quả sau khi áp dụng bộ lọc và sắp xếp.
   - Nếu không có kết quả nào, hiển thị: "Không tìm thấy kết quả nào."


---

# Trang Nộp Hồ Sơ Chi Tiết (Cũng là trang Thống Kê Hồ Sơ)

## Xử lý phiên
- Nếu người dùng chưa đăng nhập thì chuyển hướng về trang đăng nhập.

## 1) Admin

### a) Chức năng:
1. Hiển thị thông tin tất cả các hồ sơ của ngành vừa click (hoặc hiển thị hồ sơ của *tất cả* các ngành nếu xóa nội dung trong search bar), gồm:  
   - **STT**, **Tên người nộp**, **Tên ngành**, **Tên khối**, **Trạng thái** (Chưa duyệt, Đã duyệt, Từ chối), **Tên người xét duyệt**.
2. Các nút chức năng:
   - **Duyệt:**  
     - Đổi trạng thái của hồ sơ thành “Đã duyệt”.  
     - Disable nút duyệt.  
     - Set tên người duyệt thành tên user hiện tại.  
   - **Không duyệt:**  
     - Đổi trạng thái của hồ sơ thành “Không duyệt”.  
     - Disable nút không duyệt.  
     - Set tên người duyệt thành tên user hiện tại.  
   - **Xem hồ sơ chi tiết:** Chuyển hướng sang trang xem hồ sơ chi tiết của hồ sơ đã chọn.  
   - **Xóa hồ sơ:** Xóa hồ sơ được chọn.
3. **Tìm kiếm theo:**  
   - Tên ngành, Tên người nộp, Tên người duyệt.
4. **Sắp xếp theo:**  
   - Tên ngành, Tên người nộp, Tên người duyệt, STT.
5. **Lọc theo:**  
   - Trạng thái (Chưa duyệt, Đã duyệt, Từ chối), Khối xét tuyển.
6. **Chia trang:** Giới hạn số kết quả hiển thị trên một trang.
7. **Thông báo tổng số kết quả:**  
   - Nếu không có kết quả: Hiển thị **"Không tìm thấy kết quả nào."**

## 2) Giáo viên

### a) Chức năng:
1. Hiển thị thông tin tất cả các hồ sơ của ngành vừa click (hoặc hồ sơ của *tất cả các ngành* đã được admin giao nếu xóa nội dung trong search bar), gồm:  
   - **STT**, **Tên người nộp**, **Tên ngành**, **Tên khối**, **Trạng thái** (Chưa duyệt, Đã duyệt, Từ chối), **Tên người xét duyệt**.
2. Các nút chức năng:
   - **Duyệt:**  
     - Đổi trạng thái của hồ sơ thành “Đã duyệt”.  
     - Disable nút duyệt.  
     - Set tên người duyệt thành tên user hiện tại.  
   - **Không duyệt:**  
     - Đổi trạng thái của hồ sơ thành “Không duyệt”.  
     - Disable nút không duyệt.  
     - Set tên người duyệt thành tên user hiện tại.  
   - **Xem hồ sơ chi tiết:** Chuyển hướng sang trang xem hồ sơ chi tiết của hồ sơ đã chọn.
3. **Tìm kiếm theo:**  
   - Tên ngành, Tên người nộp, Tên người duyệt.
4. **Sắp xếp theo:**  
   - Tên ngành, Tên người nộp, Tên người duyệt, STT.
5. **Lọc theo:**  
   - Trạng thái (Chưa duyệt, Đã duyệt, Từ chối), Khối xét tuyển.
6. **Chia trang:** Giới hạn số kết quả hiển thị trên một trang.
7. **Thông báo tổng số kết quả:**  
   - Nếu không có kết quả: Hiển thị **"Không tìm thấy kết quả nào."**

### b) Xử lý:
1. Nếu người dùng cố tình truy cập vào ngành không được phân qua `major_id` trên URL sẽ hiển thị **"Không tìm thấy kết quả nào."**

## 3) Học sinh

### a) Chức năng:
1. Hiển thị tên ngành vừa chọn.
2. Chọn khối xét tuyển (chỉ có thể chọn các khối mà ngành này xét tuyển).
3. Sau khi chọn khối xét tuyển:
   - Hiện ra input nhập điểm của các môn tương ứng với khối đã chọn.
   - Hiện ra nút upload ảnh hồ sơ.
   - Hiện ra nút nộp hồ sơ.
4. Hiển thị thông báo: **Thành công/Lỗi**.

### b) Xử lý:
1. Nếu cố tình truy cập vào ngành *đã nộp* hồ sơ qua `major_id` trên URL thì sẽ chuyển hướng sang trang xem hồ sơ chi tiết.
2. Nếu cố tình truy cập vào ngành *chưa mở* hoặc *quá hạn* qua `major_id` trên URL thì sẽ quay về trang chủ và hiển thị thông báo **"Không trong khoảng thời gian cho phép nộp hồ sơ ngành này."**
3. **Xử lý chưa điền đủ điểm các môn.**
4. **Xử lý file có định dạng không hợp lệ:** Chỉ chấp nhận định dạng `.jpg` hoặc `.png`.
5. **Xử lý file có dung lượng vượt quá dung lượng cho phép:** Không chấp nhận file > 100MB.
6. **Xử lý nếu file trùng tên với file đã có trong hệ thống:** Đổi tên file (thêm `_index`).

---

# Trang Xem Hồ Sơ Chi Tiết

## Xử lý phiên
- Nếu người dùng chưa đăng nhập, chuyển hướng về trang **đăng nhập**.

## Hiển thị thông tin hồ sơ
1. **Mã hồ sơ**  
2. **Tên học sinh**  
3. **Trạng thái xét duyệt**:  
   - Đang chờ duyệt  
   - Đã duyệt  
   - Từ chối  
4. **Tên ngành**  
5. **Tên khối xét tuyển**  
6. **Điểm từng môn tương ứng với khối**  
7. **Ảnh hồ sơ**  
   - Hiển thị ảnh được upload.

---

# Trang Thống Kê Hồ Sơ

## Xử lý phiên
- Nếu người dùng chưa đăng nhập, chuyển hướng về trang **đăng nhập**.

## Hiển thị nội dung
- **Trang thực chất là "Trang Nộp Hồ Sơ Chi Tiết"**, nhưng:
  - **Admin** và **giáo viên**:
    - Hiển thị tất cả các ngành mà người dùng được phép xem.
    - Nếu truy cập thông qua nút nộp hồ sơ, hiển thị ngành tương ứng.
    - Nếu truy cập trực tiếp vào trang này, hiển thị tất cả các ngành.

---

# Trang Thống Kê Người Dùng

## Xử lý phiên
- Nếu người dùng chưa đăng nhập, chuyển hướng về trang **đăng nhập**.
- Nếu người dùng không phải **admin**, chuyển hướng về **trang chủ**.

## Hiển thị bảng người dùng
- Các cột thông tin:
  1. **STT**  
  2. **Tên đăng nhập**  
  3. **Tên người dùng**  
  4. **Email**  
  5. **Vai trò** (có thể thay đổi qua selection dropdown)  
  6. **Nút xóa người dùng**  

## Quy định chức năng
- **Thay đổi vai trò người dùng**:
  - Sử dụng dropdown trong cột vai trò.
  - **Không thể thay đổi vai trò của admin**.
- **Xóa người dùng**:
  - Sử dụng nút trong cột xóa.
  - **Không thể xóa admin**.


## Tìm kiếm, sắp xếp và lọc
- **Tìm kiếm theo**:
  - Tên người dùng
  - Email  
- **Sắp xếp theo**:
  - STT
  - Tên người dùng  
- **Lọc theo**:
  - Vai trò  

## Hiển thị kết quả
- **Chia trang**:
  - Giới hạn số kết quả hiển thị trên 1 trang.
- **Tổng số kết quả**:
  - Hiển thị tổng số kết quả sau khi đã lọc và sắp xếp.
  - Nếu không có kết quả, hiển thị thông báo:  
    `"Không có kết quả phù hợp"`.

---

# Trang Phân Ngành Giáo Viên

## Xử lý phiên
- Nếu người dùng **chưa đăng nhập**, chuyển hướng về trang **đăng nhập**.
- Nếu người dùng **không phải admin**, chuyển hướng về **trang chủ**.

## Hiển thị bảng giáo viên
### Các cột thông tin
1. **STT**  
2. **Tên người dùng**  
3. **Email**  
4. **Ngành đã được phân công**:  
   - Nếu chưa có ngành nào, hiển thị `"Chưa có"`.  
5. **Cột ngành muốn thêm**:  
   - Cho phép gõ tên ngành để thêm ngành phân công cho giáo viên.  
6. **Cột ngành muốn xóa**:  
   - Cho phép gõ tên ngành để xóa ngành đã được phân công.

## Quy định chức năng
### Phân công ngành
- **Thêm ngành**:
  - Người dùng nhập tên ngành cần thêm vào cột **ngành muốn thêm**.  
  - **Xử lý lỗi**:  
    - Nếu **ngành đã được phân công**, hiển thị thông báo:  
      `"Ngành này đã được phân công cho giáo viên."`.  
    - Nếu **ngành không tồn tại**, hiển thị thông báo:  
      `"Ngành học không tồn tại."`.

### Xóa ngành
- **Xóa ngành đã phân công**:
  - Người dùng nhập tên ngành cần xóa vào cột **ngành muốn xóa**.  
  - **Xử lý lỗi**:  
    - Nếu **ngành chưa được phân công**, hiển thị thông báo:  
      `"Ngành này chưa được phân công cho giáo viên."`.

## Hiển thị kết quả
- **Thông báo**:
  - Hiển thị thông báo **thành công/lỗi** sau khi thêm/xóa ngành.
- **Tìm kiếm theo**:
  - Tên người dùng
  - Email  
- **Sắp xếp theo**:
  - STT
  - Tên người dùng  
- **Chia trang**:
  - Giới hạn số kết quả hiển thị trên 1 trang.  
- **Tổng số kết quả**:
  - Hiển thị tổng số kết quả sau khi đã lọc và sắp xếp.  
  - Nếu không có kết quả nào, hiển thị thông báo:  
    `"Không có kết quả phù hợp"`.
---

# Trang Quản Lý Ngành

## Xử lý phiên
- Nếu người dùng **chưa đăng nhập**, chuyển hướng về trang **đăng nhập**.  
- Nếu người dùng **không phải admin**, chuyển hướng về **trang chủ**.  

## Hiển thị bảng các ngành
### Các cột thông tin
1. **STT**
2. **Tên ngành**  
   - **Chức năng**:  
     - Cho phép chỉnh sửa tên ngành trực tiếp.  
     - **Xử lý lỗi**:  
       - Nếu tên trống: Hiển thị thông báo `"Tên ngành không được để trống."`.  
       - Nếu tên trùng lặp: Hiển thị thông báo `"Tên ngành đã tồn tại."`.
3. **Khối xét tuyển**  
   - Danh sách các khối hiện có.  
   - **Thêm khối**:
     - Nhập tên khối tại cột **Thêm khối**.  
     - **Xử lý lỗi**:  
       - Nếu khối không tồn tại: Hiển thị thông báo `"Khối không tồn tại."`.  
       - Nếu khối đã được gán: Hiển thị thông báo `"Khối đã được gán."`.
   - **Xóa khối**:
     - Nhập tên khối tại cột **Xóa khối**.  
     - **Xử lý lỗi**:  
       - Nếu khối không tồn tại: Hiển thị thông báo `"Khối không tồn tại."`.  
       - Nếu khối chưa được gán: Hiển thị thông báo `"Khối chưa được gán."`.
4. **Ngày bắt đầu mở đơn**  
   - Cho phép chỉnh sửa ngày bắt đầu.  
   - **Xử lý lỗi**:  
     - Ngày bắt đầu phải trước ngày kết thúc nộp hồ sơ. Nếu sai, hiển thị thông báo `"Ngày bắt đầu phải trước ngày kết thúc."`.
5. **Ngày kết thúc nộp hồ sơ**  
   - Cho phép chỉnh sửa ngày kết thúc.  
   - **Xử lý lỗi**:  
     - Ngày kết thúc phải sau ngày bắt đầu mở đơn. Nếu sai, hiển thị thông báo `"Ngày kết thúc phải sau ngày bắt đầu."`.
6. **Cột Xóa Ngành**  
   - Nút xóa ngành tương ứng với mỗi ngành.  

## Chức năng khác
### Tạo ngành mới
- Nhập thông tin ngành mới và xác nhận.  
- **Xử lý lỗi**:  
  - Nếu tên ngành đã tồn tại, hiển thị thông báo `"Tên ngành đã tồn tại."`.  

### Tìm kiếm
- Tìm kiếm theo tên ngành.  

### Sắp xếp
- Sắp xếp theo:  
  - **STT**  
  - **Tên ngành**  
  - **Thời gian còn lại** (tính từ ngày hiện tại đến ngày kết thúc).

### Lọc
- Lọc theo:  
  - **Trạng thái** (đang mở, đã kết thúc, chưa mở).  
  - **Khối**.  

### Chia trang
- Giới hạn số kết quả hiển thị trên 1 trang.  


## Hiển thị kết quả
- Hiển thị tổng số kết quả sau khi đã lọc và sắp xếp.  
- Nếu không có kết quả nào, hiển thị thông báo:  
  `"Không có kết quả phù hợp."`.  

## Thông báo
- Hiển thị thông báo **thành công/lỗi** cho các thao tác thêm, xóa, chỉnh sửa.  


# Trang Quản Lý Khối

## Xử lý phiên
- Nếu người dùng **chưa đăng nhập**, chuyển hướng về trang **đăng nhập**.  
- Nếu người dùng **không phải admin**, chuyển hướng về **trang chủ**.  

## Hiển thị bảng các khối
### Các cột thông tin
1. **STT**  
2. **Mã khối**  
   - Hiển thị mã khối của từng khối.
3. **Môn học**  
   - Danh sách các môn học thuộc khối.
4. **Thêm môn học**  
   - Gõ tên môn học muốn thêm vào cột này.
   - **Xử lý lỗi**:  
     - Nếu môn học không tồn tại: Hiển thị thông báo `"Môn học không tồn tại."`.  
     - Nếu môn học đã được gán: Hiển thị thông báo `"Môn học đã được gán cho khối."`.
5. **Xóa môn học**  
   - Gõ tên môn học muốn xóa vào cột này.
   - **Xử lý lỗi**:  
     - Nếu môn học không tồn tại: Hiển thị thông báo `"Môn học không tồn tại."`.  
     - Nếu môn học chưa được gán: Hiển thị thông báo `"Môn học chưa được gán cho khối."`.
6. **Xóa khối**  
   - Nút xóa khối tương ứng cho mỗi khối.  

## Chức năng khác
### Tạo khối mới
- Nhập thông tin mã khối và xác nhận.
- **Xử lý lỗi**:  
  - Nếu mã khối đã tồn tại, hiển thị thông báo `"Mã khối đã tồn tại."`.

### Tìm kiếm
- Tìm kiếm theo mã khối.

### Sắp xếp
- Sắp xếp theo:  
  - **STT**  
  - **Mã khối**  

### Lọc
- Lọc theo môn học.

### Chia trang
- Giới hạn số kết quả hiển thị trên 1 trang.  

## Hiển thị kết quả
- Hiển thị tổng số kết quả sau khi đã lọc và sắp xếp.  
- Nếu không có kết quả nào, hiển thị thông báo:  
  `"Không có kết quả phù hợp."`.  

## Thông báo
- Hiển thị thông báo **thành công/lỗi** cho các thao tác thêm, xóa môn học, tạo mới, và xóa khối.  


---
# Trang Tài Khoản

## Hiển thị Hồ sơ Người Dùng
### Thông tin bao gồm:
1. **Avatar**  
   - Nếu chưa đặt ảnh đại diện, hiển thị **avatar mặc định**.
2. **Tên đăng nhập**
3. **Tên người dùng**
4. **Email**
5. **Vai trò**  

### Thông báo:
- Hiển thị **thông báo thành công/lỗi** khi thực hiện các thao tác.


## Các Form và Chức Năng

### 1. **Đổi Thông Tin Cá Nhân**
#### Nội dung:
- **Tên đăng nhập**, **tên người dùng**, **email**:  
  - Tự động điền sẵn các giá trị hiện tại.
  - Người dùng có thể chỉnh sửa.
- **Chọn ảnh đại diện mới** hoặc **xóa ảnh đại diện**.

#### Xử lý:
- **Thông tin không hợp lệ**:  
  - **Tên đăng nhập** hoặc **tên người dùng**: Rỗng hoặc không hợp lệ.  
  - **Email**: Sai định dạng.  
  - Hiển thị thông báo lỗi tương ứng.
- **Ảnh không hợp lệ**:  
  - File không phải định dạng ảnh.  
  - Dung lượng file lớn hơn **5MB**.  
  - Hiển thị thông báo: `"Ảnh không hợp lệ hoặc vượt quá giới hạn dung lượng."`.

### 2. **Đổi Mật Khẩu**
#### Nội dung:
- **Mật khẩu cũ**  
- **Mật khẩu mới**  
- **Xác nhận mật khẩu mới**  
- Chức năng **ẩn/hiện** trường mật khẩu.

#### Xử lý:
- **Nhập sai mật khẩu cũ**: Hiển thị thông báo `"Mật khẩu cũ không chính xác."`.
- **Chưa nhập đủ thông tin**: Hiển thị thông báo `"Vui lòng điền đầy đủ thông tin."`.
- **Xác nhận mật khẩu mới không trùng khớp**: Hiển thị thông báo `"Xác nhận mật khẩu không khớp."`.

### 3. **Xóa Tài Khoản**
#### Nội dung:
- **Xác nhận mật khẩu**: Yêu cầu nhập mật khẩu hiện tại.
- Chức năng **ẩn/hiện** trường mật khẩu.

#### Xử lý:
- **Nhập sai mật khẩu**: Hiển thị thông báo `"Mật khẩu không chính xác."`.
- **Chưa nhập đủ thông tin**: Hiển thị thông báo `"Vui lòng điền đầy đủ thông tin."`.

## Thông báo
- **Thành công**: Hiển thị thông báo `"Thay đổi thành công."`, `"Xóa tài khoản thành công."`, hoặc thông báo tương tự.  
- **Lỗi**: Hiển thị thông báo chi tiết về nguyên nhân lỗi trong từng thao tác.  

---

## Liên hệ
Nếu bạn có bất kỳ câu hỏi hoặc vấn đề nào, vui lòng liên hệ tại: **nguyengiahung1403@gmail.com**
