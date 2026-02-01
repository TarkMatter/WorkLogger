<script>
    function toggleGroup(groupId, on) {
        const root = document.getElementById(groupId);
        if (!root) return;

        root.querySelectorAll('input.perm-checkbox').forEach(cb => {
            cb.checked = !!on;
        });
    }
</script>
