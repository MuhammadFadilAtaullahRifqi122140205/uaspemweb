$(document).ready(function () {
  // Validasi sebelum submit
  $('#userForm').on('submit', function (e) {
    if (!$('#agree').is(':checked')) {
      alert('Anda harus setuju dengan syarat dan ketentuan.');
      e.preventDefault();
    }
  });

  // Muat data pengguna
  function loadUsers() {
    $.get('load_users.php', function (data) {
      $('#userTable tbody').html(data);
    });
  }

  loadUsers();
});
