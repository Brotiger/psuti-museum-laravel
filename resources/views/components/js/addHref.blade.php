<script>
    $(document).ready(function(){
        var caretEnd;
        var hrefId = 0;
        var textarea = null;

        $('#modalUnit .resetSearchHref').click(function()
        {
            $('.searchField').val('');
            $('#searchUnit').click()
        });

        $('#modalEmp .resetSearchHref').click(function()
        {
            $('.searchField').val('');
            $('#searchEmp').click()
        });

        $('#modalEvent .resetSearchHref').click(function()
        {
            $('.searchField').val('');
            $('#searchEvent').click()
        });

        $("body").delegate('textarea','blur', function(e) {
            textarea = $(this);
            caretEnd = this.selectionEnd;
        });

        $("body").delegate('[addEmpHref]', 'click', function(){
            $('body').css('overflow', 'hidden');
            $('#modalEmp, #blockBackground').show();
            $(this).parent().parent().find('textarea').focus().removeClass("errorField");
        });

        $("body").delegate('[addUnitHref]','click', function(){
            $('body').css('overflow', 'hidden');
            $('#modalUnit, #blockBackground').show();
            $(this).parent().parent().find('textarea').focus().removeClass("errorField");
        });

        $("body").delegate('[addEventHref]','click', function(){
            $('body').css('overflow', 'hidden');
            $('#modalEvent, #blockBackground').show();
            $(this).parent().parent().find('textarea').focus().removeClass("errorField");
        });

        $('#searchEmpResult').change(function(){
            hrefId = $(this).val();
            let name = $('#searchEmpResult option[value='+ hrefId +']').text();
            $('#empHrefText').val(name.trim());
        });

        $('#searchUnitResult').change(function(){
            hrefId = $(this).val();
            let name = $('#searchUnitResult option[value='+ hrefId +']').text();
            $('#unitHrefText').val(name.trim());
        });

        $('#searchEventResult').change(function(){
            hrefId = $(this).val();
            let name = $('#searchEventResult option[value='+ hrefId +']').text();
            $('#eventHrefText').val(name.trim());
        });

        $('#addEmpHrefDes').click(function(){
            let oldText = textarea.val();
            if(hrefId != null &&  $('#empHrefText').val().trim() != ''){
                let data = oldText.substr(0, caretEnd) + '<a href="/employees/{{ $site }}/more/'+ hrefId +'">' + $('#empHrefText').val().trim() + '</a>' + oldText.substr(caretEnd);
                
                textarea.val('').focus().val(data);
            }
        });

        $('#addUnitHrefDes').click(function(){
            let oldText = textarea.val();
            if(hrefId != null &&  $('#unitHrefText').val().trim() != ''){
                let data = oldText.substr(0, caretEnd) + '<a href="/units/{{ $site }}/more/'+ hrefId +'">' + $('#unitHrefText').val().trim() + '</a>' + oldText.substr(caretEnd);
                
                textarea.val('').focus().val(data);
            }
        });

        $('#addEventHrefDes').click(function(){
            let oldText = textarea.val();
            if(hrefId != null &&  $('#eventHrefText').val().trim() != ''){
                let data = oldText.substr(0, caretEnd) + '<a href="/events/{{ $site }}/more/'+ hrefId +'">' + $('#eventHrefText').val().trim() + '</a>' + oldText.substr(caretEnd);
                
                textarea.val('').focus().val(data);
            }
        });

        $('.closeModal, .addModalHref').on('click', function(test){
            hrefId = null;
            $('.modalAddHref input[type="text"]').val('');
            $('.modalAddHref select').val('');
            $('#blockBackground').hide();
            $('body').css('overflow', 'auto');
            $(this).parent().parent().hide();
        });

        $('#modalUnit .closeModal, #modalUnit .addModalHref').click(()=>{
            $('#searchUnit').click();
        });

        $('#modalEmp .closeModal, #modalEmp .addModalHref').click(()=>{
            $('#searchEmp').click();
        });

        $('#modalEvent .closeModal, #modalEvent .addModalHref').click(()=>{
            $('#searchEvent').click();
        });

        $('#searchEmp').on('click', function(){
            let res = $.ajax({
                url: "{{ route('search_employee') }}",
                data: {
                    'firstName': $('#searchFirstNameHref').val(),
                    'lastName': $('#searchLastNameHref').val(),
                    'secondName': $('#searchSecondHref').val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    $('#empHrefText').val('');
                    $("#searchEmpResult").html(data);
                },
                error: function(data){
                    $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                }
            });
        });

        $('#searchUnit').on('click', function(){
            let res = $.ajax({
                url: "{{ route('search_unit') }}",
                data: {
                    'fullUnitName': $('#fullUnitNameHref').val(),
                    'shortUnitName': $('#shortUnitNameHref').val(),
                    'typeUnit': $('#typeUnitHref').val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    $('#unitHrefText').val('');
                    $("#searchUnitResult").html(data);
                },
                error: function(data){
                    $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                }
            });
        });

        $('#searchEvent').on('click', function(){
            let res = $.ajax({
                url: "{{ route('search_event') }}",
                data: {
                    'name': $('#eventNameHref').val(),
                    'dateFrom': $('#eventDateToHref').val(),
                    'dateTo': $('#eventDateToHref').val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    $('#eventHrefText').val('');
                    $("#searchEventResult").html(data);
                },
                error: function(data){
                    $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                }
            });
        });
    });
</script>