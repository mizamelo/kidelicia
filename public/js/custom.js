jQuery("input.telefone")
    .mask("(99) 9999-9999?9")
    .focusout(function (event) {
        var target, phone, element;
        target = (event.currentTarget) ? event.currentTarget : event.srcElement;
        phone = target.value.replace(/\D/g, '');
        element = $(target);
        element.unmask();
        if(phone.length > 10) {
            element.mask("(99) 99999-999?9");
        } else {
            element.mask("(99) 9999-9999?9");
        }
});

$(document).ready(function (){

    var nav = $('.app-container');
    nav.addClass('expanded');

    var comanda_form= $('#gerar_comanda');

    comanda_form.submit(function (e) {
        e.preventDefault();
        $('#gerarComandaModal').modal('hide');
        $('#modalPDF').modal('show');
    });



        $('#printPDF').click(function () {
            var $inputs = $('#gerar_comanda :input');
            var values = {};
            // not sure if you wanted this, but I thought I'd add it.
            // get an associative array of just the values.

            $inputs.each(function() {
                values[this.name] = $(this).val().replace(/(<([^>]+)>)/ig,"");
            });

            // Default export is a4 paper, portrait, using milimeters for units
            var doc = new jsPDF();

            // set font size
            doc.setFontSize(12);

            console.log(values);

            doc.text("Estabelecimento: " + values["input1"],10,10);
            doc.text("Endereço: " + row["endereco"], 10, 14);
            doc.text("Endereço: " + row["numero"], 40, 14);
            // doc.text("Company: " + row["company"], 10, 22);

            //doc.save("a4.pdf");

            doc.autoPrint();
            doc.output("dataurlnewwindow");
        });




});


