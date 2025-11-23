/**
 * Invitation Link Copy to Clipboard
 */
(function() {
    // Copy single invitation link
    window.copyLink = function(event) {
        const input = document.getElementById('invitationLink');
        const url = input.value;
        
        navigator.clipboard.writeText(url).then(() => {
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
            setTimeout(() => {
                btn.innerHTML = originalHTML;
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy:', err);
            // Fallback for older browsers
            input.select();
            document.execCommand('copy');
            const btn = event.target.closest('button');
            btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-clipboard"></i> Copy';
            }, 2000);
        });
    };

    // Event delegation for copy invitation buttons in table
    document.addEventListener('click', function(event) {
        const btn = event.target.closest('.copy-invitation-btn');
        if (btn) {
            const url = btn.dataset.url;
            navigator.clipboard.writeText(url).then(() => {
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check"></i>';
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy:', err);
                alert('Failed to copy link. Please copy manually.');
            });
        }
    });
})();
