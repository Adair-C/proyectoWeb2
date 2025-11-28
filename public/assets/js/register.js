//public/assets/js/register.js
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-register");
    const feedback = document.getElementById("feedback");
    const usernameInput = document.getElementById("username");

    if (!form) return;

    
    usernameInput.addEventListener("input", (e) => {
        
        const invalidChars = /[^a-zA-Z0-9ñÑ]/g;
        
        if (invalidChars.test(e.target.value)) {
            
            e.target.value = e.target.value.replace(invalidChars, "");
        }
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        feedback.textContent = "";
        feedback.className = "feedback";

        
        const username = usernameInput.value.trim();
        const password = document.getElementById("password").value;
        const password2 = document.getElementById("password2").value;
        const nombre = document.getElementById("nombre").value.trim();
        const email = document.getElementById("email").value.trim(); 
        const rol = document.getElementById("rol").value;

        
        if (password !== password2) {
            feedback.textContent = "Las contraseñas no coinciden.";
            feedback.classList.add("error");
            return;
        }

        
        
        const regexEstricta = /[^a-zA-Z0-9ñÑ]/;
        if (regexEstricta.test(username)) {
            feedback.textContent = "El usuario solo puede contener letras y números.";
            feedback.classList.add("error");
            return; 
        }

        
        const data = {
            username: username,
            password: password,
            password2: password2,
            nombre: nombre,
            email: email, 
            rol: rol
        };

        try {
            const response = await fetch("../api/register.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(data)
            });

            const res = await response.json();

            if (res.ok) {
                feedback.textContent = "Registro exitoso. Redirigiendo al login...";
                feedback.classList.add("success");

                setTimeout(() => {
                    window.location.href = "login.php";
                }, 1200);
            } else {
                feedback.textContent = res.error || "Ocurrió un error inesperado.";
                feedback.classList.add("error");
            }
        } catch (err) {
            console.error(err);
            feedback.textContent = "Error de conexión con el servidor.";
            feedback.classList.add("error");
        }
    });
});