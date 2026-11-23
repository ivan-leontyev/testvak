$(function () {
    $('#billing-apply').on('click', function () {
        var period = $('#billing-period select[name="period"]').val();
        if (!confirm('Вы уверены, что хотите сформировать счета за период ' + period + '?')) {
            return;
        }

        $.ajax({
            url: 'api/billing_apply.php?period=' + encodeURIComponent(period),
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                $('#billing-msg')
                    .text('Счета за ' + period + ': создано ' + data.created + ', обновлено ' + data.updated)
                    .show();
                if (data.raw) {
                    renderBillingPreview(data.raw);
                }
            }
        });
    });
});
