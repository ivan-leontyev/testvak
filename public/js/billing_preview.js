function renderBillingPreview(data) {
    var $tbody = $('#billing-preview tbody');
    $tbody.empty();

    if (!data.clients.length) {
        $tbody.append('<tr><td colspan="5" class="empty">Клиентов нет</td></tr>');
        return;
    }

    $.each(data.clients, function (i, c) {
        $tbody.append(
            '<tr>' +
            '<td>' + esc(c.name) + '</td>' +
            '<td>' + (c.invoice_id === null ? '—' : '№ ' + c.invoice_id) + '</td>' +
            '<td class="r">' + money(c.before) + '</td>' +
            '<td class="r">' + money(c.after) + '</td>' +
            '<td class="r">' + money(c.diff) + '</td>' +
            '</tr>'
        );
    });

    $tbody.append(
        '<tr class="total">' +
        '<td colspan="2">Итого</td>' +
        '<td class="r">' + money(data.total_before) + '</td>' +
        '<td class="r">' + money(data.total_after) + '</td>' +
        '<td class="r">' + money(Number(data.total_after) - Number(data.total_before)) + '</td>' +
        '</tr>'
    );
}

$(function () {
    $('#billing-show').on('click', function () {
        $.getJSON('api/billing_preview.php', { period: $('#billing-period select[name="period"]').val() }, renderBillingPreview);
    });
});
