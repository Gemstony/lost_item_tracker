(() => {
    const forms = document.querySelectorAll('.needs-validation');

    Array.from(forms).forEach((form) => {
        const password = form.querySelector('input[name="password"], input[name="new_password"]');
        const confirmPassword = form.querySelector('input[name="confirm_password"]');

        const validatePasswordMatch = () => {
            if (!password || !confirmPassword) {
                return;
            }

            confirmPassword.setCustomValidity(
                confirmPassword.value && password.value !== confirmPassword.value
                    ? 'Passwords do not match.'
                    : ''
            );
        };

        if (password && confirmPassword) {
            password.addEventListener('input', validatePasswordMatch);
            confirmPassword.addEventListener('input', validatePasswordMatch);
        }

        form.addEventListener('submit', (event) => {
            validatePasswordMatch();

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });
    });
})();
