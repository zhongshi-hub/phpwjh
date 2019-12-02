
// Form-Component.js
$(document).ready(function() {


    $('#demo-chosen-select').chosen();
    $('#demo-cs-multiselect').chosen({width:'100%'});
    var rs_def = document.getElementById('demo-range-def');
    var rs_def_value = document.getElementById('demo-range-def-val');

    noUiSlider.create(rs_def,{
        start   : [ 20 ],
        connect : 'lower',
        range   : {
            'min': [  0 ],
            'max': [ 100 ]
        }
    });

    rs_def.noUiSlider.on('update', function( values, handle ) {
        rs_def_value.innerHTML = values[handle];
    });

    var rs_step = document.getElementById('demo-range-step');
    var rs_step_value = document.getElementById('demo-range-step-val');


    noUiSlider.create(rs_step,{
        start   : [ 20 ],
        connect : 'lower',
        step    : 10,
        range   : {
            'min': [  0 ],
            'max': [ 100 ]
        }
    });

    rs_step.noUiSlider.on('update', function( values, handle ) {
        rs_step_value.innerHTML = values[handle];
    });

    var rs_range_ver1 = document.getElementById('demo-range-ver1');
    var rs_range_ver2 = document.getElementById('demo-range-ver2');
    var rs_range_ver3 = document.getElementById('demo-range-ver3');
    var rs_range_ver4 = document.getElementById('demo-range-ver4');
    var rs_range_ver5 = document.getElementById('demo-range-ver5');


    noUiSlider.create(rs_range_ver1,{
        start: [ 80 ],
        connect: 'lower',
        range: {
            'min':  [20],
            'max':  [100]
        },
        orientation: 'vertical',
        direction: 'rtl'
    });


    noUiSlider.create(rs_range_ver2,{
        start: [ 50 ],
        connect: 'lower',
        range: {
            'min':  [20],
            'max':  [100]
        },
        orientation: 'vertical',
        direction: 'rtl'
    });

    noUiSlider.create(rs_range_ver3,{
        start: [ 30 ],
        connect: 'lower',
        range: {
            'min':  [20],
            'max':  [100]
        },
        orientation: 'vertical',
        direction: 'rtl'
    });

    noUiSlider.create(rs_range_ver4,{
        start: [ 50 ],
        connect: 'lower',
        range: {
            'min':  [20],
            'max':  [100]
        },
        orientation: 'vertical',
        direction: 'rtl'
    });

    noUiSlider.create(rs_range_ver5,{
        start: [ 80 ],
        connect: 'lower',
        range: {
        'min':  [20],
        'max':  [100]
        },
        orientation: 'vertical',
        direction: 'rtl'
    });


    var rs_range_drg = document.getElementById('demo-range-drg');

    noUiSlider.create(rs_range_drg, {
        start: [ 40, 60 ],
        behaviour: 'drag',
        connect: true,
        range: {
        'min':  20,
        'max':  80
        }
    });


    var rs_range_fxt = document.getElementById('demo-range-fxt');

    noUiSlider.create(rs_range_fxt, {
        start: [ 40, 60 ],
        behaviour: 'drag-fixed',
        connect: true,
        range: {
            'min':  20,
            'max':  80
        }
    });

    var range_all_sliders = {
        'min': [ 0 ],
        '25%': [ 50 ],
        '50%': [ 100 ],
        '75%': [ 150 ],
        'max': [ 200 ]
    };

    var rs_range_hpips = document.getElementById('demo-range-hpips');

    noUiSlider.create(rs_range_hpips, {
        range: range_all_sliders,
        connect: 'lower',
        start: 90,
        pips: { // Show a scale with the slider
            mode: 'steps',
            density: 2
        }
    });
    $("#demo-select2").select2();
    $("#demo-select2-placeholder").select2({
        placeholder: "Select a state",
        allowClear: true
    });
    $("#demo-select2-multiple-selects").select2();
    $('#demo-tp-com').timepicker();
    $('#demo-tp-textinput').timepicker({
        minuteStep: 5,
        showInputs: false,
        disableFocus: true
    });
    $('#demo-dp-txtinput input').datepicker();
    $('#demo-dp-component .input-group.date').datepicker({autoclose:true});
    $('#demo-dp-range .input-daterange').datepicker({
        format: "MM dd, yyyy",
        todayBtn: "linked",
        autoclose: true,
        todayHighlight: true
    });
    $('#demo-dp-inline div').datepicker({
    format: "MM dd, yyyy",
    todayBtn: "linked",
    autoclose: true,
    todayHighlight: true
    });
    


});
