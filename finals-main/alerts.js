function showAlertWithRedirect(title, text, icon, redirectUrl) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect after confirmation
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        }
    });
}
