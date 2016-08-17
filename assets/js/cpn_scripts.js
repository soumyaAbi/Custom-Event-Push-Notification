jQuery('document').ready(function(){
    jQuery('.form_datetime').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        showMeridian: 1,
        startDate: new Date(),
        minuteStep: 30
    });
});
