document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-login");
    const feedback = document.getElementById("feedback");

    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        feedback.textContent = "";
        feedback.className = "feedback";

        const data = {
            username: document.getElementById("username").value.trim(),
            password: document.getElementById("password").value
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
                        window.location.href = "index.php";
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
