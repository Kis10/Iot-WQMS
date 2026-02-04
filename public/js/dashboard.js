$(function() {
    $('#turbidityKnob, #tdsKnob, #phKnob, #tempKnob').knob({
        min: 0,
        max: 100,
        width: 100,
        height: 100,
        readOnly: true,
        fgColor: '#4B5563',
        bgColor: '#E5E7EB',
        thickness: 0.3
    });

    $('input[name="turbidity"]').on('input', function(){
        $('#turbidityKnob').val(this.value).trigger('change');
    });
    $('input[name="tds"]').on('input', function(){
        $('#tdsKnob').val(this.value).trigger('change');
    });
    $('input[name="ph"]').on('input', function(){
        $('#phKnob').val(this.value).trigger('change');
    });
    $('input[name="temperature"]').on('input', function(){
        $('#tempKnob').val(this.value).trigger('change');
    });
});
