$(document).ready(function () {
    const alertPlaceholder = document.getElementById('liveAlertPlaceholder')

    const alert = (message, type) => {
        const wrapper = document.createElement('div')
        wrapper.innerHTML = [
            `<div class="alert alert-${type} alert-dismissible fade show" role="alert">`,
            `   <div>${message}</div>`,
            '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
            '</div>'
        ].join('')

        alertPlaceholder.append(wrapper)

        // Hilangkan alert setelah 5 detik
        setTimeout(() => {
            // Menambahkan kelas fade untuk animasi menghilang
            wrapper.querySelector('.alert').classList.remove('show')
            wrapper.querySelector('.alert').classList.add('fade')

            // Setelah 1 detik (durasi animasi fade-out), hapus elemen dari DOM
            setTimeout(() => {
                wrapper.remove()
            }, 1000); // 1000 ms = 1 detik, untuk menunggu animasi selesai
        }, 5000); // 5000 ms = 5 detik, sebelum fade-out dimulai
    }

    var table = $('#user_jobtitle').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "autoWidth": false, // Menyesuaikan lebar kolom berdasarkan konten
        "ajax": {
            "url": "/transactions/getUserJobTitles", // URL API untuk mengambil data
            "type": "POST",
            "data": function (d) {
                // Tambahkan CSRF Token jika diperlukan
                d.csrf_token = $('meta[name="csrf-token"]').attr('content');
            }
        },
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]], // Opsi jumlah data per halaman
        "pageLength": 10, // Default jumlah data per halaman
        "columns": [
            { "data": "intUserID" }, // ID User
            { "data": "txtFullName" }, // Nama lengkap
            {
                "data": "jobTitles",
                "render": function (data, type, row) {
                    if (!data || !data.length) return 'N/A';

                    // ID unik untuk setiap accordion berdasarkan intUserID
                    const accordionId = `accordionJobTitles${row.intUserID}`;
                    const collapseId = `collapseJobTitles${row.intUserID}`;

                    // Bangun list item untuk setiap job title dengan tombol Edit dan Details
                    const jobList = data.map(job => `
            <li>
                <strong>${job.title}</strong> - Achieved: ${job.achieved}
                <div class="mt-2">
                    <a href="/transactions/user_jobtitle/details/${job.jobTitleID}" class="btn btn-info btn-sm">Details</a>
                    <button class="btn btn-warning edit-btn" data-id="${job.jobTitleID}">Edit</button>
                </div>
            </li>
        `).join('');

                    // Struktur accordion dengan daftar job titles di dalamnya
                    return `
            <div class="accordion accordion-flush" id="${accordionId}">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading${row.intUserID}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                            Job Titles
                        </button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse" data-bs-parent="#${accordionId}">
                        <div class="accordion-body">
                            <ul>${jobList}</ul>
                        </div>
                    </div>
                </div>
            </div>
        `;
                }
            },
            {
                "data": null,
                "orderable": false,
                "render": function (data, type, row) {
                    return `
                        <a href="/transactions/user_jobtitle/details/${row.intUserID}" class="btn btn-info btn-sm">Details</a>
                        <a href="/transactions/user_jobtitle/edit/${row.intUserID}" class="btn btn-warning btn-sm">Edit</a>
                    `;
                }
            }
        ], "columnDefs": [
            { "width": "10%", "targets": [0] }, // Lebar kolom ID User (10%)
            { "width": "20%", "targets": [1] }, // Lebar kolom Nama User (20%)
            { "width": "20%", "targets": [3] }, // Lebar kolom Action (20%)
            { "width": "auto", "targets": [2] } // Kolom Job Titles dan Inserted By menyesuaikan secara otomatis
        ]
    });

    // Menangani event ketika accordion dibuka
    $('#user_jobtitle').on('show.bs.collapse', '.accordion-collapse', function () {
        // Tutup semua accordion yang lain ketika yang satu dibuka
        var accordionId = $(this).attr('id');
        $('.accordion-collapse').not(`#${accordionId}`).collapse('hide');
    });

    // Event listener for the edit button
    $('#user_jobtitle tbody').on('click', '.edit-btn', function () {
        var userjobtitlesId = $(this).data('id');

        // AJAX request to get the data
        $.ajax({
            url: '/transactions/user_jobtitle/edit/' + userjobtitlesId,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                // Populate modal fields with the data
                $('#editUserJobTitleModal #userJobTitleId').val(data.intTrUserJobTitleID);
                $('#editUserJobTitleModal #intUserID').val(data.intUserID);
                $('#editUserJobTitleModal #userName').val(data.userName);
                $('#editUserJobTitleModal #jobTitle').val(data.txtJobTitle);
                $('#editUserJobTitleModal #achieved').prop('checked', data.bitAchieved == '1');
                $('#editUserJobTitleModal #active').prop('checked', data.bitActive == '1');

                // Show the modal
                $('#editUserJobTitleModal').modal('show');
            },
            error: function (xhr) {
                alert('Error fetching data: ' + xhr.statusText, 'danger');
            }
        });
    });

    // Handle submit form edit
    // Event listener for the save button in the modal
    $('#saveChanges').on('click', function () {
        // Ambil data dari modal
        var dataToSend = {
            intTrUserJobTitleID: $('#editUserJobTitleModal #userJobTitleId').val(),
            intUserID: $('#editUserJobTitleModal #intUserID').val(),
            bitAchieved: $('#editUserJobTitleModal #achieved').is(':checked') ? 1 : 0,
            bitActive: $('#editUserJobTitleModal #active').is(':checked') ? 1 : 0,
        };

        // Kirim data menggunakan AJAX
        $.ajax({
            url: '/transactions/user_jobtitle/update', // Endpoint untuk update
            type: 'POST',
            data: JSON.stringify(dataToSend),
            contentType: 'application/json',
            success: function (response) {
                alert(response.message, 'success');
                table.ajax.reload(null, false); // Memuat ulang tanpa reset posisi paging

                // Tutup modal
                $('#editUserJobTitleModal').modal('hide');
            },
            error: function (xhr) {
                alert('Error saving data: ' + xhr.statusText, 'danger');
            }
        });
    });
});
