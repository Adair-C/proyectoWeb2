document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-register");
    const feedback = document.getElementById("feedback");
    
    // Referencia al campo de usuario para validación en tiempo real
    const usernameInput = document.getElementById("username");

    if (!form) return;

    // 1. VALIDACIÓN EN TIEMPO REAL
    // Elimina caracteres no permitidos mientras el usuario escribe
    usernameInput.addEventListener("input", (e) => {
        // Solo permite: Letras, Números, @, Punto, Guion bajo y Guion medio
        const invalidChars = /[^a-zA-Z0-9@._-]/g;
        
        if (invalidChars.test(e.target.value)) {
            e.target.value = e.target.value.replace(invalidChars, "");
        }
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        feedback.textContent = "";
        feedback.className = "feedback";

        // Obtenemos los valores limpios
        const username = usernameInput.value.trim();
        const password = document.getElementById("password").value;
        const password2 = document.getElementById("password2").value;
        const nombre = document.getElementById("nombre").value.trim();
        const email = document.getElementById("email").value.trim();
        const rol = document.getElementById("rol").value;

        // 2. VALIDACIÓN DE SEGURIDAD (Doble chequeo)
        const regexSeguridad = /[^a-zA-Z0-9@._-]/;
        if (regexSeguridad.test(username)) {
            feedback.textContent = "El usuario contiene caracteres no permitidos (´, ^, ¬, °, etc).";
            feedback.classList.add("error");
            return;
        }

        // 3. VALIDACIÓN DE CONTRASEÑAS
        if (password !== password2) {
            feedback.textContent = "Las contraseñas no coinciden.";
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