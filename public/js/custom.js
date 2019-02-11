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
        // $('#gerarComandaModal').modal('hide');
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

            doc.text("Endereço: " + values["endereco"], 10, 18);
            doc.text("Numero: " + values["numero"], 10, 22);
            doc.text("Complemento: " + values["complemento"], 10, 26);
            doc.text("Bairo: " + values["bairro"], 10, 30);
            doc.text("Cidade: " + values["cidade"], 10, 34);

            doc.text("Comprador: " + values["input3"], 10, 40);
            doc.text("Telefone: " + values["input7"], 10, 44);
            doc.text("________________________Descrição_______________________", 10, 48);

            var splitDescricao = doc.splitTextToSize(values['input4'], 180);
            doc.text(10, 52, splitDescricao);


            doc.text("Data do Pedido: " + values["input9"], 10, 132);
            doc.text("Data da Entrega: " + values["input10"], 10, 136);
            doc.text("__________________________________________________________", 10, 140);

            doc.text("Assinatura do comprador: _________________________________", 10, 144);


            // doc.text("Company: " + row["company"], 10, 22);
            //doc.save("a4.pdf");

            doc.autoPrint();
            doc.output("dataurlnewwindow");
        });




});


