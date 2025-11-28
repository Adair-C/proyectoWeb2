document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-login");
    const feedback = document.getElementById("feedback");
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");

    
    if (!form) return;

    
    usernameInput.addEventListener("input", (e) => {
        
        const invalidChars = /[^a-zA-Z0-9ñÑ]/g;
        
        
        if (invalidChars.test(e.target.value)) {
            e.target.value = e.target.value.replace(invalidChars, "");
        }
    });

    
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        
        feedback.style.display = 'none';
        feedback.textContent = "";
        feedback.className = "feedback";

        const username = usernameInput.value.trim();
        const password = passwordInput.value.trim();

        
        if (!username || !password) {
            feedback.style.display = 'block';
            feedback.textContent = "Por favor, completa todos los campos.";
            feedback.classList.add("error");
            return;
        }

        const data = {
            username: username,
            password: password
        };

        try {
            
            const response = await fetch("../api/login.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            });

            const res = await response.json();

            
            feedback.style.display = 'block';

            if (res.ok) {
                
                feedback.textContent = "Inicio de sesión correcto. Redirigiendo...";
                feedback.classList.add("success");

                
                setTimeout(() => {
                    if (res.redirect) {
                        window.location.href = res.redirect;
                    } else {
                        window.location.href = "index.php"; 
                    }
                }, 1000);

            } else {
                
                feedback.textContent = res.error || "Usuario o contraseña incorrectos.";
                feedback.classList.add("error");
            }

        } catch (err) {
            console.error(err);
            feedback.style.display = 'block';
            feedback.textContent = "Error de conexión con el servidor.";
            feedback.classList.add("error");
        }
    });
});