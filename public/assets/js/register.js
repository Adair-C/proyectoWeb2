document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-register");
    const feedback = document.getElementById("feedback");

    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        feedback.textContent = "";
        feedback.className = "feedback";

        const data = {
            username: document.getElementById("username").value.trim(),
            password: document.getElementById("password").value,
            password2: document.getElementById("password2").value,
            nombre: document.getElementById("nombre").value.trim(),
            email: document.getElementById("email").value.trim(),
            rol: document.getElementById("rol").value
        };

        try {
            const response = await fetch("../api/register.php", { // 👈 ruta correcta a la API
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(data)
            });

            const res = await response.json();

            if (res.ok) {
                feedback.textContent = "Registro exitoso. Redirigiendo al login...";
                feedback.classList.add("success");

                // 👇 Redirección al login dentro de /public
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
