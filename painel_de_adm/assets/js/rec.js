REC = {
    url: false,
	
    init: function() { 
        this.rec();
    },

    rec: function() {
        self = this;

        $(document.rec).submit(function() {
            $.ajax({
                type: 'post',
                url: 'http://127.0.0.1/pbtroll2/painel_de_adm/functions/update/',
                data: $(document.rec).serialize(),

                success: function(data) {
                    if (data == "dwoqinfewf"){
                         toastr.success(data, "q");
                    }else{
                         toastr.error(data, "w");
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