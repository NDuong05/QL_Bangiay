//Toast message
function toastMsg({
    title,
    message,
    type,
    duration = 3000
}) {
    const main = document.getElementById("toast");
    if (main) {
        const toast = document.createElement("div");

        //Auto remove toast
        const autoRemove = setTimeout( () => {
            main.removeChild(toast);
        }, duration + 1000);
        
        //Remove toast when click btn close
        toast.onclick = (e) => {
            if (e.target.closest(".toast-close")) {
                main.removeChild(toast);
                clearTimeout(autoRemove);
            }
        }

        const colors = {
            success: "#47d864",
            info: "#2777f0",
            warning: "#ffaa21",
            error: "#ed3838"
        }

        const icons = {
            success: "fa-regular fa-circle-check",
            info: "fa-solid fa-circle-info",
            warning: "fa-solid fa-triangle-exclamation",
            error: "fa-solid fa-circle-exclamation"
        };

        const color = colors[type];
        const icon = icons[type];
        const delay = (duration / 1000).toFixed(2);

        toast.classList.add("toast-modal", `toast-${type}`);
        toast.style.borderColor = colors[type];
        toast.style.animation = `slideInLeft ease-in-out 0.5s, fadeOut linear 1s ${delay}s forwards`;

        toast.innerHTML = `
        <div class="toast-private">
            <div class="toast-icon">
                <i class="${icon}" style="color: ${color}"></i>
            </div>
            <div class="toast-body">
                <h3 class="toast-title" style="color: ${color}">${title}</h3>
                <p class="toast-msg">
                    ${message}
                </p>
            </div>
            <div class="toast-close">
                <a><i class="fa-regular fa-circle-xmark"></i></a>
            </div>
        </div>
        <div class="toast-background" style="background-color: ${color};"></div>
        `
        main.appendChild(toast);
    }
}