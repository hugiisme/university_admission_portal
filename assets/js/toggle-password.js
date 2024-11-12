// hiển thị mật khẩu khi ấn vào icon lock
function togglePassword(inputId){
    var passwordInput = document.getElementById(inputId);
    var icon = passwordInput.parentElement.querySelector('i');
    if (passwordInput.type === "password"){
        passwordInput.type = "text"; // đổi type thành text để hiển thị được mật khẩu
        icon.classList.replace("bx-lock-alt", "bxs-lock-open-alt"); // đổi icon thành khóa mở
        
    } else {
        passwordInput.type = "password";
        icon.classList.replace("bxs-lock-open-alt", "bx-lock-alt");
    }
}