document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-register");
    const feedback = document.getElementById("feedback");
    
    // Inputs
    const usernameInput = document.getElementById("username");
    const nombreInput = document.getElementById("nombre");
    const emailInput = document.getElementById("email");

    if (!form) return;

    // --- 1. VALIDACIÓN USUARIO (En tiempo real) ---
    // Regla: Solo Letras, Números y ñ/Ñ.
    // Bloquea: Espacios, @, _, -, y cualquier otro símbolo.
    usernameInput.addEventListener("input", (e) => {
        const invalidChars = /[^a-zA-Z0-9ñÑ]/g;
        if (invalidChars.test(e.target.value)) {
            e.target.value = e.target.value.replace(invalidChars, "");
        }
    });

    // --- 2. VALIDACIÓN NOMBRE (En tiempo real) ---
    // Regla: Solo Letras, Acentos (áéíóú), ñ/Ñ y Espacios.
    // Bloquea: Números y símbolos especiales.
    if (nombreInput) {
        nombreInput.addEventListener("input", (e) => {
            const invalidChars = /[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g;
            if (invalidChars.test(e.target.value)) {
                e.target.value = e.target.value.replace(invalidChars, "");
            }
        });
    }

    // --- 3. VALIDACIÓN CORREO (En tiempo real) ---
    // Regla: Solo Letras, Números, @, ., _, -
    // Bloquea: $, %, &, *, (), etc.
    if (emailInput) {
        emailInput.addEventListener("input", (e) => {
            const invalidChars = /[^a-zA-Z0-9@._-]/g;
            if (invalidChars.test(e.target.value)) {
                e.target.value = e.target.value.replace(invalidChars, "");
            }
        });
    }

    // --- 4. ENVÍO DEL FORMULARIO ---
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        feedback.textContent = "";
        feedback.className = "feedback";

        // Obtener valores
        const username = usernameInput.value.trim();
        const password = document.getElementById("password").value;
        const password2 = document.getElementById("password2").value;
        const nombre = document.getElementById("nombre").value.trim();
        const email = emailInput.value.trim();
        const rol = document.getElementById("rol").value;

        // Validar contraseñas iguales
        if (password !== password2) {
            feedback.textContent = "Las contraseñas no coinciden.";
            feedback.classList.add("error");
            return;
        }

        // Doble validación de Usuario (por seguridad)
        const regexUser = /[^a-zA-Z0-9ñÑ]/;
        if (regexUser.test(username)) {
            feedback.textContent = "El usuario contiene caracteres no permitidos.";
            feedback.classList.add("error");
            return;
        }

        // Doble validación de Nombre
        const regexNombre = /[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/;
        if (regexNombre.test(nombre)) {
            feedback.textContent = "El nombre contiene caracteres no permitidos o números.";
            feedback.classList.add("error");
            return;
        }

        // Doble validación de Correo (Caracteres permitidos)
        const regexEmailChars = /[^a-zA-Z0-9@._-]/;
        if (regexEmailChars.test(email)) {
            feedback.textContent = "El correo contiene caracteres prohibidos.";
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