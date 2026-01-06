document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.report-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const url = form.action;
            const formData = new FormData(form);

            // Clear previous errors
            form.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                if (response.ok) {
                    const data = await response.json();

                    // Redirect if provided
 
                    // Call success callback if defined
                    const successCallback = form.getAttribute('data-success-callback');
                    if (successCallback && typeof window[successCallback] === 'function') {
                        window[successCallback](data);
                    }

                    window.location.href = data.redirect_url;


                } else if (response.status === 422) {
                    const errorData = await response.json();
                    const errors = errorData.errors;

                    Object.keys(errors).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        const errorDiv = document.getElementById(key);
                        if (input) input.classList.add('is-invalid');
                        if (errorDiv) errorDiv.innerText = errors[key][0];
                    });
                } else {
                    alert('Something went wrong. Please try again.');
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
            });
        });
    });
});
