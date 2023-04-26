function collapse_handler(event) {
    let target = document.getElementById("navbar-collapse");
    let collapse = new bootstrap.Collapse(target, {
        toggle: true,
    });
}

document.addEventListener("DOMContentLoaded", () => {
    let button = document.getElementById("toggler-button");
    button.addEventListener("click", collapse_handler, false);
});
