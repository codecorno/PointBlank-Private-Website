(function($) {
	
$.fn.ajaxSubmit.debug = true;

$(document).ajaxError(function(ev,xhr,o,err) {
    alert(err);
    if (window.console && window.console.log) console.log(err);
});

$(function() {
    // initialize page tabs (../malsup-tabs-init.js)
    malsup.initializeTabs('examples', 'ajaxForm', /ajaxForm|ajaxSubmit|validation|json|xml|html|file-upload/);

    var options1 = {
        target:        '#output1',   // target element to update
        beforeSubmit:  showRequest,  // pre-submit callback
        success:       showResponse  // post-submit callback
    };

    // bind form1 using 'ajaxForm'
    $('#myForm1').ajaxForm(options1);

    var options2 = {
        target:        '#output2',   // target element to update
        beforeSubmit:  showRequest,  // pre-submit callback
        success:       showResponse  // post-submit callback
    };

    // bind form2 using ajaxSubmit
    $('#myForm2').submit(function() {
        // submit the form via ajax
        $(this).ajaxSubmit(options2);
        return false;
    });

    $('#test').submit(function() {
        return false;
    });

    $('#inputForm').submit(function() {
        var query = $('#query').val();
        showIt(query);
        return false;
    });

    $('<div id="busy">Loading...</div>')
        .ajaxStart(function() {$(this).show();})
        .ajaxStop(function() {$(this).hide();})
        .appendTo('#main');

    $('#jsonForm').ajaxForm({
        dataType: 'json',
        success:  processJson
    });

    $('#validateForm1').ajaxForm({ beforeSubmit: validate1 });
    $('#validateForm2').ajaxForm({ beforeSubmit: validate2 });
    $('#validateForm3').ajaxForm({ beforeSubmit: validate3 });

    $('#xmlForm').ajaxForm({
        // dataType identifies the expected content type of the server response
        dataType:  'xml',

        // success identifies the function to invoke when the server response
        // has been received
        success:   processXml
    });

    $('#checkform').ajaxForm({
        // target identifies the element(s) to update with the server response
        target: '#htmlExampleTarget',
        success: function() {
            $('#htmlExampleTarget').fadeIn('slow');
        }
    });

    $('#uploadForm').ajaxForm({
        beforeSubmit: function(a,f,o) {
            o.dataType = $('#uploadResponseType')[0].value;
            $('#uploadOutput').html('Submitting...');
        },
        success: function(data) {
            var $out = $('#uploadOutput');
            $out.html('Form success handler received: <strong>' + typeof data + '</strong>');
            if (typeof data == 'object' && data.nodeType)
                data = elementToString(data.documentElement, true);
            else if (typeof data == 'object')
                data = objToString(data);
            $out.append('<div><pre>'+ data +'</pre></div>');
        }
    });


    // pre-submit callback
    function showRequest(formData, jqForm) {
        alert('About to submit: \n\n' + $.param(formData));
        return true;
    }

    // post-submit callback
    function showResponse(responseText, statusText)  {
        alert('this: ' + this.tagName +
            '\nstatus: ' + statusText +
            '\n\nresponseText: \n' +
            responseText + '\n\nThe output div should have already been updated with the responseText.');
    }

    function showIt(query) {
        var successful = $('#successful')[0].checked;
        var val = $(query, '#test').fieldValue(successful);
        var ser = $(query, '#test').fieldSerialize(successful);
        alert('$("'+query+'").fieldValue(): ' + val + '\n\n$("'+query+'").fieldSerialize(): ' + ser);
    }

    function validate1(formData, jqForm, options) {
        for (var i=0; i < formData.length; i++) {
            if (!formData[i].value) {
                alert('Please enter a value for both Username and Password');
                return false;
            }
        }
        alert('Both fields contain values.');
    }

    function validate2(formData, jqForm, options) {
        var form = jqForm[0];
        if (!form.username.value || !form.password.value) {
            alert('Please enter a value for both Username and Password');
            return false;
        }
        alert('Both fields contain values.');
    }

    function validate3(formData, jqForm, options) {
        var usernameValue = $('#validateForm3 input[name=username]').fieldValue();
        var passwordValue = $('#validateForm3 input[name=password]').fieldValue();

        if (!usernameValue[0] || !passwordValue[0]) {
            alert('Please enter a value for both Username and Password');
            return false;
        }
        alert('Both fields contain values.');
    }

    function processJson(data) {
        alert(data.message);
    }

    function processXml(responseXML) {
        var message = $('message', responseXML).text();
        alert(message);
    }

    // helper
    function objToString(o) {
        var s = '{\n';
        for (var p in o)
            s += '    "' + p + '": "' + o[p] + '"\n';
        return s + '}';
    }

    // helper
    function elementToString(n, useRefs) {
        var attr = "", nest = "", a = n.attributes;
        for (var i=0; a && i < a.length; i++)
            attr += ' ' + a[i].nodeName + '="' + a[i].nodeValue + '"';

        if (n.hasChildNodes == false)
            return "<" + n.nodeName + "\/>";

        for (var i=0; i < n.childNodes.length; i++) {
            var c = n.childNodes.item(i);
            if (c.nodeType == 1)       nest += elementToString(c);
            else if (c.nodeType == 2)  attr += " " + c.nodeName + "=\"" + c.nodeValue + "\" ";
            else if (c.nodeType == 3)  nest += c.nodeValue;
        }
        var s = "<" + n.nodeName + attr + ">" + nest + "<\/" + n.nodeName + ">";
        return useRefs ? s.replace(/</g,'&lt;').replace(/>/g,'&gt;') : s;
    };

});

})(jQuery);
