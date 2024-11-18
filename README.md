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

### 1. Trang Đăng Ký
- **Chức năng chính:**  
  Hiển thị form để người dùng tạo tài khoản mới (username, tên, email, mật khẩu, xác nhận mật khẩu).  
  Vai trò mặc định là học sinh (admin có thể chỉnh sửa vai trò sau).

- **Xử lý:**
  - Kiểm tra nhập thiếu thông tin, định dạng email, xác nhận mật khẩu không khớp.
  - Mã hóa mật khẩu trước khi lưu.
  - Hiển thị thông báo lỗi hoặc thành công.

- **Chức năng phụ:**
  - Nút điều hướng sang trang đăng nhập.
  - Bật/tắt xem trước mật khẩu.

---

### 2. Trang Đăng Nhập
- **Chức năng chính:**  
  Hiển thị form đăng nhập với username và mật khẩu.

- **Xử lý:**
  - Kiểm tra nhập thiếu thông tin, sai tên đăng nhập hoặc mật khẩu.
  - Bật/tắt xem trước mật khẩu.

- **Chức năng phụ:**  
  - Nút điều hướng sang trang đăng ký.  
  - Chuyển hướng sang trang chủ nếu đăng nhập thành công.

---

### 3. Trang Chủ
- **Chức năng chính:**  
  Hiển thị danh sách các ngành học kèm thông tin trạng thái, thời gian mở đơn, khối xét tuyển.

- **Chức năng khác:**  
  - **Tìm kiếm:** Theo tên ngành.  
  - **Sắp xếp:** Theo tên ngành hoặc thời gian còn lại.  
  - **Lọc:** Theo trạng thái hoặc khối.  
  - **Phân quyền hiển thị:**  
    - **Học sinh:** Hiển thị ngành trạng thái "Hiện", có nút nộp hoặc xem hồ sơ.  
    - **Giáo viên:** Hiển thị ngành được phân công, có thêm chức năng ẩn/hiện ngành.  
    - **Admin:** Hiển thị tất cả ngành, thêm/xóa ngành.

---

### 4. Trang Nộp Hồ Sơ Chi Tiết
- **Học sinh:**  
  - Form nộp hồ sơ với thông tin khối, điểm từng môn, và upload ảnh hồ sơ.  
  - Hiển thị thông tin hồ sơ nếu đã nộp.

- **Giáo viên:**  
  - Hiển thị danh sách hồ sơ của ngành được phân công.  
  - Tìm kiếm, lọc, sắp xếp hồ sơ.  
  - Chức năng duyệt/từ chối hồ sơ.  

- **Admin:**  
  - Tương tự giáo viên, nhưng có quyền xóa hồ sơ.

---

### 5. Trang Tài Khoản
- Hiển thị thông tin tài khoản.  
- Chức năng:
  - Đổi thông tin cá nhân.
  - Đổi mật khẩu (xác nhận mật khẩu cũ).  
  - Xóa tài khoản (xác nhận mật khẩu).  

---

### 6. Trang Quản Lý
#### **Ngành:**
- Tìm kiếm, sắp xếp, lọc ngành.  
- Thêm/xóa ngành.  
- Gán/xóa khối xét tuyển cho ngành.

#### **Khối:**
- Tìm kiếm, sắp xếp khối.  
- Thêm/xóa môn học cho khối.  

#### **Người dùng:**  
- Tìm kiếm, sắp xếp, lọc người dùng.  
- Thay đổi vai trò, xóa người dùng.

#### **Phân ngành giáo viên:**  
- Thêm/xóa ngành mà giáo viên quản lý.

---

### 7. Trang Thống Kê
#### **Hồ sơ:**  
- Hiển thị danh sách tất cả hồ sơ, có thể lọc và tìm kiếm.

#### **Người dùng:**  
- Hiển thị tất cả người dùng, tìm kiếm, lọc và chỉnh sửa thông tin.

---

## Liên hệ
Nếu bạn có bất kỳ câu hỏi hoặc vấn đề nào, vui lòng liên hệ tại: **email@example.com**
