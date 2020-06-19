REC = {
    url: false,
	
    init: function() { 
        this.rec();
    },

    rec: function() {
        self = this;

        $(document.cash).submit(function() {
            $.ajax({
                type: 'post',
                url: 'http://127.0.0.1/pbtroll2/painel_de_adm/functions/cash/',
                data: $(document.cash).serialize(),

                success: function(data) {
                    if (data.indexOf("Cash Total") == -1){
						toastr.error(data, "Erro ao enviar cash.");
                    }else{
                        toastr.success(data, "Parabens.");
					}
                },

                error: function(XMLHttpRequest, textStatus, errorThrown) {
                     toastr.warning(data, "w");
                },
            });
        });
		
		$(document.gold).submit(function() {
            $.ajax({
                type: 'post',
                url: 'http://127.0.0.1/pbtroll2/painel_de_adm/functions/gold/',
                data: $(document.gold).serialize(),

                success: function(data) {
                    if (data.indexOf("Gold Total") == -1){
                    	toastr.error(data, "Erro ao enviar gold.");
                    }else{
                        toastr.success(data, "Gold enviado com sucesso.");
					}
                },

                error: function(XMLHttpRequest, textStatus, errorThrown) {
                     $('#retorno_ativar_pincode p').html("errrrro");
                },
            });
        });
    },
}

$(document).ready(function(){
    REC.init();
});