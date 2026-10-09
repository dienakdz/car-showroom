<script>
    document.addEventListener('livewire:init', () => {
        if (typeof window.toastr === 'undefined') {
            return;
        }

        window.toastr.options = Object.assign({
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 4000,
            extendedTimeOut: 1000,
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
        }, window.toastr.options || {});

        Livewire.on('toast', (event) => {
            const payload = (Array.isArray(event) ? event[0] : event) || {};
            const allowedTypes = ['success', 'error', 'warning', 'info'];
            const type = allowedTypes.includes(payload.type) ? payload.type : 'success';
            const message = payload.message || '';

            if (message !== '') {
                window.toastr[type](message);
            }
        });
    });
</script>
