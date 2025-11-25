document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-login");
    const feedback = document.getElementById("feedback");
    const usernameInput = document.getElementById("username");

    if (!form) return;

    // VALIDACIÓN EN TIEMPO REAL (Opcional: evita que los escriban)
    // Esto borra automáticamente caracteres inválidos mientras el usuario escribe
    usernameInput.addEventListener("input", (e) => {
        // Esta expresión regular busca todo lo que NO sea letra, número, @, punto, guion bajo o guion medio
        const invalidChars = /[^a-zA-Z0-9@._-]/g;
        
        if (invalidChars.test(e.target.value)) {
            // Si encuentra algo raro, lo borra
            e.target.value = e.target.value.replace(invalidChars, "");
        }
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        feedback.textContent = "";
        feedback.className = "feedback";

        const username = usernameInput.value.trim();
        const password = document.getElementById("password").value;

        // --- VALIDACIÓN EXTRA ANTES DE ENVIAR ---
        // Verifica si se coló algún caracter especial no permitido
        // Permitimos: Letras (a-z), Números (0-9), Arroba (@), Punto (.), Guion bajo (_) y Guion medio (-)
        const regexSeguridad = /[^a-zA-Z0-9@._-]/;

        if (regexSeguridad.test(username)) {
            feedback.textContent = "El usuario contiene caracteres no permitidos (´, ^, ¬, °, etc).";
            feedback.classList.add("error");
            return; // Detiene el envío del formulario
        }
        // ----------------------------------------

        const data = {
            username: username,
            password: password
        };

        try {
            const response = await fetch("../api/login.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(data)
            });

            const res = await response.json();

            if (res.ok) {
                feedback.textContent = "Inicio de sesión correcto, redirigiendo...";
                feedback.classList.add("success");

                setTimeout(() => {
                    if (res.redirect) {
                        window.location.href = res.redirect;
                    } else {
                        window.location.href = "index.php"; // Fallback
                    }
                }, 900);
            } else {
                feedback.textContent = res.error || "Usuario o contraseña incorrectos.";
                feedback.classList.add("error");
            }
        } catch (err) {
            console.error(err);
            feedback.textContent = "Error de conexión con el servidor.";
            feedback.classList.add("error");
        }
    });
});