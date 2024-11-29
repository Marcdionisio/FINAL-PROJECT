function editAnnouncement(id) {
    // Implement the logic for editing an announcement
    // e.g., populate a form with existing announcement data
    console.log("Edit Announcement ID: " + id);
}

function archiveAnnouncement(id) {
    // Confirm before archiving
    Swal.fire({
        title: 'Are you sure?',
        text: "This announcement will be archived!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, archive it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "announcement_process.php?action=archive&id=" + id;
        }
    });
}

function deleteAnnouncement(id) {
    // Confirm before deleting
    Swal.fire({
        title: 'Are you sure?',
        text: "This announcement will be deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "announcement_process.php?action=delete&id=" + id;
        }
    });
}

function unarchiveAnnouncement(id) {
    // Confirm before unarchiving
    Swal.fire({
        title: 'Are you sure?',
        text: "This announcement will be restored!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, restore it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "announcement_process.php?action=restore&id=" + id;
        }
    });
}
function editAnnouncement(id) {
    // Fetch the announcement data using AJAX or from the DOM
    const title = document.querySelector(`tr[data-id="${id}"] td:nth-child(1)`).textContent;
    const content = document.querySelector(`tr[data-id="${id}"] td:nth-child(2)`).textContent;

    // Fill the form fields with the existing data
    document.getElementById('edit_announcement_id').value = id;
    document.getElementById('edit_announcement_title').value = title;
    document.getElementById('edit_announcement_content').value = content;

    // Show the edit announcement modal
    document.getElementById('editAnnouncementModal').style.display = 'block';
}

function closeEditModal() {
    // Hide the edit announcement modal
    document.getElementById('editAnnouncementModal').style.display = 'none';
}
