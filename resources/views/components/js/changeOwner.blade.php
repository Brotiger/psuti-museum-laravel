<script>
    $(document).ready(function(){
        $('#modalAddUser .resetAddUser').click(function()
        {
            $('.searchField').val('');
            $('#searchUser').click()
        });

        $('#changeAddUser').click(function(){
            $('#modalAddUser, #blockBackground').show();
            $('body').css('overflow', 'hidden');
        });

        $('#modalAddUser .closeModal, #changeOwner').click(()=>{
            $('#searchUserByName').val('');
            $('#searchUserByEmail').val('');
            $('#searchUser').click();
        });

        $('#saveChangeOwner').click(function(){
            $result = $('#searchUserResult').val();
            
            if($result){
                $('#addUserName').val($('#searchUserResult option[value=' + $result + ']').attr('userName'));
                $('#addUserEmail').val($('#searchUserResult option[value=' + $result + ']').attr('userEmail'));
                $('#addUserId').val($('#searchUserResult option[value=' + $result + ']').val());
                $('#addUserId').attr('data-field', '');
                $('#searchUserResult').val('');
            }
        });

        $('#searchUser').on('click', function(){
            let res = $.ajax({
                url: "{{ route('search_user') }}",
                data: {
                    'name': $('#searchUserByName').val(),
                    'email': $('#searchUserByEmail').val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    $("#searchUserResult").html(data);
                },
                error: function(data){
                    $('#error-message').fadeIn(300).delay(2000).fadeOut(300);
                }
            });
        });
    });
</script>