$(function () {
    var $table = $('#trip-split');
    var $tbody = $table.find('tbody');

    $.getJSON('api/trip_split.php', { trip_id: $table.data('trip-id') }, function (data) {
        $tbody.empty();
        if (!data.clients.length) {
            $tbody.append('<tr><td colspan="6" class="empty">Клиентов нет</td></tr>');
            return;
        }

        var total = 0;
        $.each(data.clients, function (i, c) {
            total += Number(c.amount);
            $tbody.append(
                '<tr>' +
                '<td>' + esc(c.name) + '</td>' +
                '<td class="r">' + c.cargo_count + '</td>' +
                '<td class="r">' + num(c.weight_kg || 0) + '</td>' +
                '<td class="r">' + num(c.volume_m3 || 0) + '</td>' +
                '<td class="r">' + num(c.share * 100) + '</td>' +
                '<td class="r">' + money(c.amount) + '</td>' +
                '</tr>'
            );
        });

        $tbody.append(
            '<tr class="total">' +
            '<td colspan="5">Сумма по клиентам</td>' +
            '<td class="r">' + money(total) + '</td>' +
            '</tr>' +
            '<tr class="sub">' +
            '<td colspan="5">Стоимость рейса</td>' +
            '<td class="r">' + money(data.cost) + '</td>' +
            '</tr>'
        );
    });
});
