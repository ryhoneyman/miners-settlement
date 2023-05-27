$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

function select2_template(obj)
{
   var data     = $(obj.element).data();
   var text     = $(obj.element).text();
   var type     = $(obj.element).prop('nodeName');
   var image    = (data && data['image']) ? data['image'] : null;
   var template = '';

   if (type == 'OPTGROUP') {
      var label = $(obj.element).attr('label');
      template  = $('<div><span>' + label + '</span></div>');
   }
   else if (image) {
      template = $('<div><img src=\"' + image + '\" style=\"width:25px; height:25px;\"/> <span style=\"font-weight:bold;\">' + text + '</span></div>');
   }
   else if (obj.id === '') {
      template = $('<div><span>' + text + '</span></div>');
   }
   else {
      template = $('<div><span style=\"font-weight:bold;\">' + text + '</span></div>');
   }

   return template;
}

