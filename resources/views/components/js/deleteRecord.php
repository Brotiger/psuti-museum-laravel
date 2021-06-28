<script>
    var recordId = null;
    $("[deleteRecord]").click(function(event){
        recordId = $(this).attr('record-id')

        $("#modalRemoveRecord, #blockBackground").show();
        $('body').css('overflow', 'hidden');
    });

    $('.closeModal').on('click', closeModal);

    $('.deleteRecordYes').on('click', function(){
        $('tr[row-record-id="'+ recordId +'"]').hide();
        let formData = new FormData();
        formData.append('id', recordId);

        let res = $.ajax({
            type: "POST",
            url: $('#routeToDelete').val(),
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        closeModal();
    })

    function closeModal(){
        recordId = null;

        $('#blockBackground').hide();
        $('body').css('overflow', 'auto');
        $("#modalRemoveRecord").hide();
    }
</script>