var user;
var sawMessage = -1;

$(document).ready(function () {
    $('.bet').click(function (e) {
        var elem = $(this).closest("tr").find("td").eq(5).children("b");
        //var id = $(this).closest("tr").data('id');
        // updateVotesAux(elem, id)

        swap(e, 1, $(this), elem);
    });

    $('.betGolos').click(function (e) {
        var elem = $(this).closest("tr").find("td").eq(5).children("b");
        //var id = $(this).closest("tr").data('id');
        // updateVotesAux(elem, id)
        swap(e, 2, $(this), elem);
    });

    setInterval(function () {
        updateVotes();
    }, 10000);

});


function updateVotes() {
    $('#games_table > tbody  > tr').each(function () {
        var id = $(this).data('id');
        var elem = $(this).find("td").eq(5).children("b");
        // console.log(elem);
        if (id != undefined) {
            updateVotesAux(elem, id);
        }
    });
}


function updateVotesAux(elem, id) {
    $.post("/amountOfVotes", {gameId: id})
        .done(function (data) {
            //var elem = $(this).find("td").eq(5).children("b");
            elem.animate({opacity: 0}, 300, function () { // Fade out
                elem[0].innerHTML = data;
                elem.animate({opacity: 1}, 200, function () { // Fade in
                });
            });


        });
}

function swap(e, l, dis, elem) {
    e.stopPropagation();
    if (user === null && (sawMessage = sawMessage + 1) % 3 == 0) return needLogin();

    var currentRow = dis.closest("tr");
    var trid = currentRow.data('id');
    var tds = currentRow.find("td").eq(l).children("button");
    dis.toggleClass("btn-secondary btn-primary");
    tds.not(dis).removeClass('btn-primary');
    var bet = dis.val();
    $.post("/newBet", {gameId: trid, betTip: bet}).done(function (data) {
        elem.animate({opacity: 0}, 300, function () { // Fade out
            elem[0].innerHTML = data.status;
            elem.animate({opacity: 1}, 200, function () { // Fade in
            });
        });


    });
}

function needLogin() {
    BootstrapDialog.show({
        title: 'Informação',
        message: 'É necessário fazer login para os votos serem registados!',
        buttons: [{
            label: 'Login',
            cssClass: 'btn-primary',
            action: function () {
                window.location.href = "/login/facebook"
            }
        }, {
            label: 'Fechar',
            action: function (dialogItself) {
                dialogItself.close();
            }
        }]
    });
}

function sortData(v) {
    v.sort(function (a, b) {
        return a.data < b.data ? 1 : -1;
    });
    return v;
}

function addPercentageToData(v) {
    for (elem in v) {
        v[elem].label += '  (' + v[elem].data + '%)'
    }
    return v;
}

function draw(elem, data) {
    $.plot($(elem), data, {
        series: {
            pie: {
                innerRadius: 0.5,
                show: true
            }
        },
        legend: {
            position: "nw",
            margin: [150, 0]
        }
    });
}

function dataToDraw(v, id, sort = true) {
    v = v.replace(/&quot;/g, '\"');
    v = JSON.parse(v);
    if (sort)sortData(v);
    addPercentageToData(v);
    draw(id, v);

}