var runesOptions = {
   maximumSelectionLength: 5
}

$('.runes').select2(runesOptions);

function updateRunes(caller, runeDiv = 'rune-section', runeId = 'runes')
{
   $('#'+runeDiv).html();

   var equipState = {}
   const gearTypes = ['weapon','shield','ring','amulet','weapon-skin','shield-skin'];

   gearTypes.forEach((gearType) => {
      equipState[gearType] = $('#'+gearType).val();
   });

   currentRunes = $('#'+runeId).val();

   $.ajax({url:'/assets/ajax/runeselect.php?caller='+caller+'&runes='+JSON.stringify(currentRunes)+'&state='+JSON.stringify(equipState), async:true}).done(function(data) { $('#'+runeDiv).html(data); $('.runes').select2(runesOptions); });
}
