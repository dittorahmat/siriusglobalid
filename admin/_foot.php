</div>
<script>
function confirmDel(form) {
  var boxes = form.querySelectorAll('input[name^="del"]:checked');
  if (boxes.length === 0) return true;
  return confirm('Hapus ' + boxes.length + ' item yang dicentang? Data yang sudah disimpan tetap bisa dikembalikan via menu Backup (snapshot otomatis).');
}
</script>
</body></html>
