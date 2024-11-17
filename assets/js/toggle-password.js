function togglePassword(inputId){
    var passwordInput = document.getElementById(inputId);
    var icon = passwordInput.parentElement.querySelector('i');
    if (passwordInput.type === "password"){
        passwordInput.type = "text"; 
        icon.classList.replace("bx-lock-alt", "bxs-lock-open-alt"); 
        
    } else {
        passwordInput.type = "password";
        icon.classList.replace("bxs-lock-open-alt", "bx-lock-alt");
    }
}